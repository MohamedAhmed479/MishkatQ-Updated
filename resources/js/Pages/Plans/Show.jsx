import { Head, Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import {
    ArrowRight,
    BookOpen,
    Calendar,
    CheckCircle,
    Clock,
    Pause,
    Play,
    Trash2,
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
    Info,
    Loader2
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';
import Modal from '@/Components/UI/Modal';

const statusLabels = {
    active: { label: 'نشطة', color: 'bg-success text-white' },
    paused: { label: 'متوقفة', color: 'bg-warning text-white' },
    completed: { label: 'مكتملة', color: 'bg-primary-500 text-white' },
};

function Pagination({ items, planId }) {
    const { current_page, last_page, from, to, total, per_page } = items;

    const goToPage = (page) => {
        router.get(`/app/plans/${planId}`, { page, per_page: per_page }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const changePerPage = (newPerPage) => {
        router.get(`/app/plans/${planId}`, { page: 1, per_page: newPerPage }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    if (last_page <= 1) return null;

    // Generate page numbers to show
    const getPageNumbers = () => {
        const pages = [];
        const showEllipsis = last_page > 7;

        if (!showEllipsis) {
            for (let i = 1; i <= last_page; i++) {
                pages.push(i);
            }
        } else {
            // Always show first page
            pages.push(1);

            if (current_page > 3) {
                pages.push('...');
            }

            // Show pages around current page
            for (let i = Math.max(2, current_page - 1); i <= Math.min(last_page - 1, current_page + 1); i++) {
                pages.push(i);
            }

            if (current_page < last_page - 2) {
                pages.push('...');
            }

            // Always show last page
            if (last_page > 1) {
                pages.push(last_page);
            }
        }

        return pages;
    };

    return (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-surface-200 dark:border-dark-300">
            {/* Info */}
            <div className="text-sm text-text-muted dark:text-text-dark-muted">
                {from && to ? `عرض ${from} - ${to} من ${total} عنصر` : `إجمالي ${total} عنصر`}
            </div>

            {/* Per Page Selector */}
            <div className="flex items-center gap-2">
                <span className="text-sm text-text-muted dark:text-text-dark-muted">عناصر لكل صفحة:</span>
                <select
                    value={per_page}
                    onChange={(e) => changePerPage(parseInt(e.target.value))}
                    className="px-2 py-1 text-sm rounded-lg border border-surface-300 dark:border-dark-200 bg-white dark:bg-dark-400 text-text-primary dark:text-text-dark-primary focus:ring-2 focus:ring-primary-500"
                >
                    <option value={5}>5</option>
                    <option value={10}>10</option>
                    <option value={20}>20</option>
                    <option value={50}>50</option>
                </select>
            </div>

            {/* Page Navigation */}
            <div className="flex items-center gap-1">
                {/* First Page */}
                <button
                    onClick={() => goToPage(1)}
                    disabled={current_page === 1}
                    className="p-2 rounded-lg hover:bg-surface-200 dark:hover:bg-dark-300 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    title="الصفحة الأولى"
                >
                    <ChevronsRight className="w-4 h-4 text-text-primary dark:text-text-dark-primary" />
                </button>

                {/* Previous Page */}
                <button
                    onClick={() => goToPage(current_page - 1)}
                    disabled={current_page === 1}
                    className="p-2 rounded-lg hover:bg-surface-200 dark:hover:bg-dark-300 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    title="الصفحة السابقة"
                >
                    <ChevronRight className="w-4 h-4 text-text-primary dark:text-text-dark-primary" />
                </button>

                {/* Page Numbers */}
                {getPageNumbers().map((page, index) => (
                    page === '...' ? (
                        <span key={`ellipsis-${index}`} className="px-2 text-text-muted dark:text-text-dark-muted">
                            ...
                        </span>
                    ) : (
                        <button
                            key={page}
                            onClick={() => goToPage(page)}
                            className={`
                                w-8 h-8 rounded-lg text-sm font-medium transition-colors
                                ${current_page === page
                                    ? 'bg-primary-500 text-white'
                                    : 'hover:bg-surface-200 dark:hover:bg-dark-300 text-text-primary dark:text-text-dark-primary'
                                }
                            `}
                        >
                            {page}
                        </button>
                    )
                ))}

                {/* Next Page */}
                <button
                    onClick={() => goToPage(current_page + 1)}
                    disabled={current_page === last_page}
                    className="p-2 rounded-lg hover:bg-surface-200 dark:hover:bg-dark-300 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    title="الصفحة التالية"
                >
                    <ChevronLeft className="w-4 h-4 text-text-primary dark:text-text-dark-primary" />
                </button>

                {/* Last Page */}
                <button
                    onClick={() => goToPage(last_page)}
                    disabled={current_page === last_page}
                    className="p-2 rounded-lg hover:bg-surface-200 dark:hover:bg-dark-300 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    title="الصفحة الأخيرة"
                >
                    <ChevronsLeft className="w-4 h-4 text-text-primary dark:text-text-dark-primary" />
                </button>
            </div>
        </div>
    );
}

export default function PlanShow({ plan, items }) {
    const [modalOpen, setModalOpen] = useState(false);
    const [itemDetails, setItemDetails] = useState(null);
    const [loadingDetails, setLoadingDetails] = useState(false);

    const handlePause = () => {
        if (confirm('هل أنت متأكد من إيقاف الخطة مؤقتًا؟')) {
            router.post(`/app/plans/${plan.id}/pause`, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    // Refresh the page to update the plan status
                    router.reload({ only: ['plan'] });
                },
            });
        }
    };

    const handleActivate = () => {
        if (confirm('هل تريد استئناف الخطة؟')) {
            router.post(`/app/plans/${plan.id}/activate`, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    // Refresh the page to update the plan status
                    router.reload({ only: ['plan'] });
                },
            });
        }
    };

    const handleDelete = () => {
        if (confirm('هل أنت متأكد من حذف هذه الخطة؟')) {
            router.delete(`/api/v1/memorization-plans/${plan.id}`);
        }
    };

    const handleShowDetails = async (itemId) => {
        setLoadingDetails(true);
        setModalOpen(true);
        
        try {
            const response = await fetch(`/app/plans/${plan.id}/items/${itemId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            
            if (response.ok) {
                const data = await response.json();
                setItemDetails(data);
            } else {
                alert('حدث خطأ أثناء جلب التفاصيل');
                setModalOpen(false);
            }
        } catch (error) {
            console.error('Error fetching item details:', error);
            alert('حدث خطأ أثناء جلب التفاصيل');
            setModalOpen(false);
        } finally {
            setLoadingDetails(false);
        }
    };

    const handleCloseModal = () => {
        setModalOpen(false);
        setItemDetails(null);
    };

    // Get items array from paginated data
    const itemsData = items?.data || items || [];
    const isPaginated = items?.data !== undefined;

    return (
        <MainLayout title={plan.name || 'تفاصيل الخطة'}>
            <Head title={plan.name || 'تفاصيل الخطة'} />

            <div className="space-y-6">
                {/* Back Button */}
                <Link
                    href="/app/plans"
                    className="inline-flex items-center gap-2 text-text-muted dark:text-text-dark-muted hover:text-text-primary dark:hover:text-text-dark-primary transition-colors"
                >
                    <ArrowRight className="w-4 h-4" />
                    <span>العودة للخطط</span>
                </Link>

                {/* Plan Header */}
                <Card gradient className="p-6">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <div className="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                                <BookOpen className="w-8 h-8" />
                            </div>
                            <div>
                                <div className="flex items-center gap-2">
                                    <h1 className="text-2xl font-bold">
                                        {plan.name || `سورة ${plan.start_chapter}`}
                                    </h1>
                                    <span className={`text-xs px-2 py-1 rounded-full ${statusLabels[plan.status]?.color}`}>
                                        {statusLabels[plan.status]?.label}
                                    </span>
                                </div>
                                <p className="text-white/80 mt-1">
                                    من {plan.start_chapter} إلى {plan.end_chapter}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-2">
                            {plan.status === 'active' ? (
                                <Button variant="secondary" onClick={handlePause} icon={Pause}>
                                    إيقاف مؤقت
                                </Button>
                            ) : plan.status === 'paused' ? (
                                <Button variant="secondary" onClick={handleActivate} icon={Play}>
                                    استئناف
                                </Button>
                            ) : null}
                            <Button variant="danger" onClick={handleDelete} icon={Trash2}>
                                حذف
                            </Button>
                        </div>
                    </div>

                    {/* Progress Bar */}
                    <div className="mt-6">
                        <div className="flex items-center justify-between text-sm mb-2">
                            <span className="text-white/80">التقدم</span>
                            <span className="font-bold">{plan.progress}%</span>
                        </div>
                        <div className="h-3 bg-white/20 rounded-full overflow-hidden">
                            <motion.div
                                initial={{ width: 0 }}
                                animate={{ width: `${plan.progress}%` }}
                                transition={{ duration: 0.5 }}
                                className="h-full bg-white rounded-full"
                            />
                        </div>
                    </div>
                </Card>

                {/* Plan Stats */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <Card className="p-4 text-center">
                        <Calendar className="w-6 h-6 mx-auto mb-2 text-primary-500" />
                        <p className="text-sm text-text-muted dark:text-text-dark-muted">تاريخ البدء</p>
                        <p className="font-bold text-text-primary dark:text-text-dark-primary">
                            {plan.start_date || '-'}
                        </p>
                    </Card>
                    <Card className="p-4 text-center">
                        <Calendar className="w-6 h-6 mx-auto mb-2 text-accent-500" />
                        <p className="text-sm text-text-muted dark:text-text-dark-muted">تاريخ الانتهاء</p>
                        <p className="font-bold text-text-primary dark:text-text-dark-primary">
                            {plan.end_date || '-'}
                        </p>
                    </Card>
                    <Card className="p-4 text-center">
                        <BookOpen className="w-6 h-6 mx-auto mb-2 text-primary-500" />
                        <p className="text-sm text-text-muted dark:text-text-dark-muted">إجمالي العناصر</p>
                        <p className="font-bold text-text-primary dark:text-text-dark-primary">
                            {isPaginated ? items.total : itemsData.length}
                        </p>
                    </Card>
                    <Card className="p-4 text-center">
                        <CheckCircle className="w-6 h-6 mx-auto mb-2 text-success" />
                        <p className="text-sm text-text-muted dark:text-text-dark-muted">المكتملة</p>
                        <p className="font-bold text-text-primary dark:text-text-dark-primary">
                            {plan.completed_items ?? itemsData.filter(i => i.is_completed).length}
                        </p>
                    </Card>
                </div>

                {/* Plan Items */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                عناصر الخطة
                            </h2>
                            {isPaginated && (
                                <span className="text-sm text-text-muted dark:text-text-dark-muted">
                                    صفحة {items.current_page} من {items.last_page}
                                </span>
                            )}
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-3">
                            {itemsData.length === 0 ? (
                                <div className="text-center py-8 text-text-muted dark:text-text-dark-muted">
                                    <BookOpen className="w-12 h-12 mx-auto mb-2 opacity-50" />
                                    <p>لا توجد عناصر في هذه الخطة</p>
                                </div>
                            ) : (
                                itemsData.map((item, index) => (
                                    <motion.div
                                        key={item.id}
                                        initial={{ opacity: 0, x: 20 }}
                                        animate={{ opacity: 1, x: 0 }}
                                        transition={{ delay: index * 0.03 }}
                                        className={`
                                        flex items-center gap-4 p-4 rounded-xl transition-colors
                                        ${item.is_completed
                                                ? 'bg-success/10'
                                                : 'bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200'
                                            }
                                    `}
                                    >
                                        <div className={`
                                        w-10 h-10 rounded-full flex items-center justify-center
                                        ${item.is_completed
                                                ? 'bg-success text-white'
                                                : 'bg-surface-200 dark:bg-dark-200 text-text-muted'
                                            }
                                    `}>
                                            {item.is_completed ? (
                                                <CheckCircle className="w-5 h-5" />
                                            ) : (
                                                <span className="font-bold text-sm">
                                                    {item.sequence || (isPaginated && items.from ? (items.from + index) : (index + 1))}
                                                </span>
                                            )}
                                        </div>

                                        <div className="flex-1">
                                            <p className={`font-medium ${item.is_completed ? 'text-success line-through' : 'text-text-primary dark:text-text-dark-primary'}`}>
                                                سورة {item.chapter_name}
                                            </p>
                                            <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                                الآيات {item.start_verse} - {item.end_verse}
                                            </p>
                                        </div>

                                        <div className="text-left">
                                            <p className="text-xs text-text-muted dark:text-text-dark-muted flex items-center gap-1">
                                                <Clock className="w-3 h-3" />
                                                {item.scheduled_date}
                                            </p>
                                            {item.completed_at && (
                                                <p className="text-xs text-success">
                                                    أُكمل: {item.completed_at}
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex items-center gap-2">
                                            <Button 
                                                size="sm" 
                                                variant="outline"
                                                icon={Info}
                                                onClick={() => handleShowDetails(item.id)}
                                            >
                                                التفاصيل
                                            </Button>
                                            {!item.is_completed && (
                                                <Link href={`/app/session/${item.id}`}>
                                                    <Button size="sm" icon={ChevronLeft} iconPosition="left">
                                                        ابدأ
                                                    </Button>
                                                </Link>
                                            )}
                                        </div>
                                    </motion.div>
                                ))
                            )}
                        </div>

                        {/* Pagination */}
                        {isPaginated && <Pagination items={items} planId={plan.id} />}
                    </CardContent>
                </Card>
            </div>

            {/* Item Details Modal */}
            <Modal 
                isOpen={modalOpen} 
                onClose={handleCloseModal}
                title={itemDetails ? `تفاصيل العنصر - ${itemDetails.item.chapter_name}` : 'تفاصيل العنصر'}
                size="xl"
            >
                {loadingDetails ? (
                    <div className="flex items-center justify-center py-12">
                        <Loader2 className="w-8 h-8 animate-spin text-primary-500" />
                    </div>
                ) : itemDetails ? (
                    <div className="space-y-6">
                        {/* Item Basic Info */}
                        <Card>
                            <CardHeader>
                                <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    معلومات العنصر
                                </h3>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted">السورة</p>
                                        <p className="font-semibold text-text-primary dark:text-text-dark-primary">
                                            {itemDetails.item.chapter_name}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted">الآيات</p>
                                        <p className="font-semibold text-text-primary dark:text-text-dark-primary">
                                            من {itemDetails.item.start_verse} إلى {itemDetails.item.end_verse}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted">تاريخ الاستحقاق</p>
                                        <p className="font-semibold text-text-primary dark:text-text-dark-primary">
                                            {itemDetails.item.target_date || '-'}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted">الحالة</p>
                                        <p className={`font-semibold ${itemDetails.item.is_completed ? 'text-success' : 'text-warning'}`}>
                                            {itemDetails.item.is_completed ? 'مكتمل' : 'غير مكتمل'}
                                        </p>
                                    </div>
                                    {itemDetails.item.completed_at && (
                                        <div>
                                            <p className="text-sm text-text-muted dark:text-text-dark-muted">تاريخ الإكمال</p>
                                            <p className="font-semibold text-success">
                                                {itemDetails.item.completed_at}
                                            </p>
                                        </div>
                                    )}
                                    <div>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted">تاريخ الإنشاء</p>
                                        <p className="font-semibold text-text-primary dark:text-text-dark-primary">
                                            {itemDetails.item.created_at || '-'}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Spaced Repetitions */}
                        <Card>
                            <CardHeader>
                                <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    المراجعات المتباعدة ({itemDetails.spaced_repetitions.length})
                                </h3>
                            </CardHeader>
                            <CardContent>
                                {itemDetails.spaced_repetitions.length === 0 ? (
                                    <p className="text-center py-6 text-text-muted dark:text-text-dark-muted">
                                        لا توجد مراجعات متباعدة لهذا العنصر بعد
                                    </p>
                                ) : (
                                    <div className="space-y-3">
                                        {itemDetails.spaced_repetitions.map((repetition, index) => (
                                            <div
                                                key={repetition.id}
                                                className={`
                                                    p-4 rounded-xl border
                                                    ${repetition.is_completed 
                                                        ? 'bg-success/10 border-success/20' 
                                                        : repetition.is_overdue
                                                        ? 'bg-error/10 border-error/20'
                                                        : 'bg-surface-100 dark:bg-dark-300 border-surface-200 dark:border-dark-200'
                                                    }
                                                `}
                                            >
                                                <div className="flex items-start justify-between mb-3">
                                                    <div>
                                                        <p className="font-semibold text-text-primary dark:text-text-dark-primary">
                                                            مراجعة #{index + 1} - فهرس الفترة: {repetition.interval_index}
                                                        </p>
                                                        <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1">
                                                            {repetition.memory_state}
                                                        </p>
                                                    </div>
                                                    <div className="flex gap-2">
                                                        {repetition.is_completed && (
                                                            <span className="px-2 py-1 text-xs rounded-full bg-success text-white">
                                                                مكتملة
                                                            </span>
                                                        )}
                                                        {repetition.is_overdue && (
                                                            <span className="px-2 py-1 text-xs rounded-full bg-error text-white">
                                                                متأخرة
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                    <div>
                                                        <p className="text-text-muted dark:text-text-dark-muted">تاريخ الجدولة</p>
                                                        <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                            {repetition.scheduled_date || '-'}
                                                        </p>
                                                    </div>
                                                    {repetition.last_reviewed_at && (
                                                        <div>
                                                            <p className="text-text-muted dark:text-text-dark-muted">آخر مراجعة</p>
                                                            <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                                {repetition.last_reviewed_at}
                                                            </p>
                                                        </div>
                                                    )}
                                                    <div>
                                                        <p className="text-text-muted dark:text-text-dark-muted">الاستقرار</p>
                                                        <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                            {repetition.stability?.toFixed(2) || '-'}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <p className="text-text-muted dark:text-text-dark-muted">الصعوبة</p>
                                                        <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                            {repetition.difficulty?.toFixed(2) || '-'}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <p className="text-text-muted dark:text-text-dark-muted">عدد المراجعات</p>
                                                        <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                            {repetition.repetition_count || 0}
                                                        </p>
                                                    </div>
                                                </div>

                                                {repetition.review_record && (
                                                    <div className="mt-4 pt-4 border-t border-surface-200 dark:border-dark-300">
                                                        <p className="text-sm font-semibold text-text-primary dark:text-text-dark-primary mb-2">
                                                            تفاصيل التقييم
                                                        </p>
                                                        <div className="grid grid-cols-2 gap-4 text-sm">
                                                            <div>
                                                                <p className="text-text-muted dark:text-text-dark-muted">تاريخ التقييم</p>
                                                                <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                                    {repetition.review_record.review_date || '-'}
                                                                </p>
                                                            </div>
                                                            <div>
                                                                <p className="text-text-muted dark:text-text-dark-muted">التقييم</p>
                                                                <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                                    {repetition.review_record.performance_description} ({repetition.review_record.performance_rating}/5)
                                                                </p>
                                                            </div>
                                                            <div>
                                                                <p className="text-text-muted dark:text-text-dark-muted">النتيجة</p>
                                                                <p className={`font-medium ${repetition.review_record.successful ? 'text-success' : 'text-error'}`}>
                                                                    {repetition.review_record.successful ? 'نجح' : 'فشل'}
                                                                </p>
                                                            </div>
                                                            {repetition.review_record.notes && (
                                                                <div className="col-span-2">
                                                                    <p className="text-text-muted dark:text-text-dark-muted">ملاحظات</p>
                                                                    <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                                        {repetition.review_record.notes}
                                                                    </p>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                ) : null}
            </Modal>
        </MainLayout>
    );
}
