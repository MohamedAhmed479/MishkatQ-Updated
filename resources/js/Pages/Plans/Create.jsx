import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import { BookOpen, ChevronLeft, ChevronRight, Search, Check } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';
import Input from '@/Components/UI/Input';

export default function CreatePlan({ chapters = [] }) {
    const [step, setStep] = useState(1);
    const [searchTerm, setSearchTerm] = useState('');

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        start_chapter_id: null,
        end_chapter_id: null,
        start_verse: 1,
        end_verse: null,
    });

    const filteredChapters = (chapters || []).filter(chapter =>
        (chapter.name_arabic || '').includes(searchTerm) ||
        (chapter.name_english || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
        chapter.id.toString() === searchTerm
    );

    const selectedStartChapter = (chapters || []).find(c => c.id === data.start_chapter_id);
    const selectedEndChapter = (chapters || []).find(c => c.id === data.end_chapter_id);

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/app/plans', {
            preserveScroll: true,
            onSuccess: () => router.visit('/app/plans'),
        });
    };

    return (
        <MainLayout title="إنشاء خطة">
            <Head title="إنشاء خطة حفظ" />

            <div className="max-w-2xl mx-auto space-y-6">
                {/* Header */}
                <div className="text-center">
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        إنشاء خطة حفظ جديدة
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        حدد السور التي تريد حفظها
                    </p>
                </div>

                {/* Progress Steps */}
                <div className="flex items-center justify-center gap-4">
                    {[1, 2, 3].map((s) => (
                        <div key={s} className="flex items-center">
                            <div className={`
                                w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                ${step >= s
                                    ? 'bg-primary-500 text-white'
                                    : 'bg-surface-200 dark:bg-dark-300 text-text-muted'
                                }
                            `}>
                                {step > s ? <Check className="w-4 h-4" /> : s}
                            </div>
                            {s < 3 && (
                                <div className={`w-16 h-1 mx-2 rounded ${step > s ? 'bg-primary-500' : 'bg-surface-200 dark:bg-dark-300'
                                    }`} />
                            )}
                        </div>
                    ))}
                </div>

                {/* Step Content */}
                <Card>
                    <CardHeader>
                        <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                            {step === 1 && 'اختر سورة البداية'}
                            {step === 2 && 'اختر سورة النهاية'}
                            {step === 3 && 'تأكيد الخطة'}
                        </h2>
                    </CardHeader>
                    <CardContent>
                        {(step === 1 || step === 2) && (
                            <div className="space-y-4">
                                {/* Search */}
                                <Input
                                    placeholder="ابحث عن سورة..."
                                    icon={Search}
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />

                                {/* Chapters Grid */}
                                <div className="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-80 overflow-y-auto">
                                    {filteredChapters.map((chapter) => {
                                        const isSelected = step === 1
                                            ? data.start_chapter_id === chapter.id
                                            : data.end_chapter_id === chapter.id;
                                        const isDisabled = step === 2 && chapter.id < data.start_chapter_id;

                                        return (
                                            <motion.button
                                                key={chapter.id}
                                                whileHover={{ scale: 1.02 }}
                                                whileTap={{ scale: 0.98 }}
                                                onClick={() => {
                                                    if (!isDisabled) {
                                                        if (step === 1) {
                                                            setData('start_chapter_id', chapter.id);
                                                        } else {
                                                            setData('end_chapter_id', chapter.id);
                                                            setData('end_verse', chapter.verses_count);
                                                        }
                                                    }
                                                }}
                                                disabled={isDisabled}
                                                className={`
                                                    p-3 rounded-xl border-2 text-right transition-all
                                                    ${isSelected
                                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                                        : isDisabled
                                                            ? 'border-surface-200 dark:border-dark-300 opacity-50 cursor-not-allowed'
                                                            : 'border-surface-300 dark:border-dark-300 hover:border-primary-300'
                                                    }
                                                `}
                                            >
                                                <div className="flex items-center gap-2">
                                                    <span className="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-sm font-bold text-primary-600 dark:text-primary-400">
                                                        {chapter.id}
                                                    </span>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="font-bold text-text-primary dark:text-text-dark-primary truncate">
                                                            {chapter.name_arabic}
                                                        </p>
                                                        <p className="text-xs text-text-muted dark:text-text-dark-muted">
                                                            {chapter.verses_count} آية
                                                        </p>
                                                    </div>
                                                </div>
                                            </motion.button>
                                        );
                                    })}
                                </div>
                            </div>
                        )}

                        {step === 3 && (
                            <div className="space-y-6">
                                {/* Plan Name */}
                                <Input
                                    label="اسم الخطة (اختياري)"
                                    placeholder="مثال: حفظ جزء عم"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                />

                                {/* Summary */}
                                <div className="bg-surface-100 dark:bg-dark-300 rounded-2xl p-6 space-y-4">
                                    <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        ملخص الخطة
                                    </h3>
                                    <div className="flex items-center justify-between">
                                        <span className="text-text-muted dark:text-text-dark-muted">من سورة</span>
                                        <span className="font-bold text-text-primary dark:text-text-dark-primary">
                                            {selectedStartChapter?.name_arabic}
                                        </span>
                                    </div>
                                    <div className="flex items-center justify-between">
                                        <span className="text-text-muted dark:text-text-dark-muted">إلى سورة</span>
                                        <span className="font-bold text-text-primary dark:text-text-dark-primary">
                                            {selectedEndChapter?.name_arabic}
                                        </span>
                                    </div>
                                    <div className="flex items-center justify-between">
                                        <span className="text-text-muted dark:text-text-dark-muted">عدد السور</span>
                                        <span className="font-bold text-primary-600 dark:text-primary-400">
                                            {data.end_chapter_id - data.start_chapter_id + 1} سورة
                                        </span>
                                    </div>
                                </div>

                                <p className="text-sm text-text-muted dark:text-text-dark-muted text-center">
                                    سيتم توزيع الآيات تلقائياً على الأيام حسب وقتك ومستواك
                                </p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Navigation */}
                <div className="flex items-center justify-between">
                    <Button
                        variant="ghost"
                        onClick={() => setStep(Math.max(1, step - 1))}
                        disabled={step === 1}
                        icon={ChevronRight}
                    >
                        السابق
                    </Button>

                    {step < 3 ? (
                        <Button
                            onClick={() => setStep(step + 1)}
                            disabled={(step === 1 && !data.start_chapter_id) || (step === 2 && !data.end_chapter_id)}
                            icon={ChevronLeft}
                            iconPosition="left"
                        >
                            التالي
                        </Button>
                    ) : (
                        <Button
                            onClick={handleSubmit}
                            loading={processing}
                            icon={Check}
                        >
                            إنشاء الخطة
                        </Button>
                    )}
                </div>
            </div>
        </MainLayout>
    );
}
