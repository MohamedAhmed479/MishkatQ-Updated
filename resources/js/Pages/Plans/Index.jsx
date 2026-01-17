import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Plus, BookOpen, ChevronLeft, Target, Pause, Play } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

const statusLabels = {
    active: { label: 'نشطة', color: 'bg-success text-white' },
    paused: { label: 'متوقفة', color: 'bg-warning text-white' },
    completed: { label: 'مكتملة', color: 'bg-primary-500 text-white' },
};

export default function PlansIndex({ plans }) {
    return (
        <MainLayout title="خططي">
            <Head title="خطط الحفظ" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                            خطط الحفظ
                        </h1>
                        <p className="text-text-muted dark:text-text-dark-muted mt-1">
                            إدارة خطط حفظك
                        </p>
                    </div>
                    <Link href="/app/plans/create">
                        <Button icon={Plus}>
                            خطة جديدة
                        </Button>
                    </Link>
                </div>

                {/* Plans List */}
                {plans.length > 0 ? (
                    <div className="grid gap-4">
                        {plans.map((plan, index) => (
                            <motion.div
                                key={plan.id}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: index * 0.1 }}
                            >
                                <Link href={`/app/plans/${plan.id}`}>
                                    <Card hover className="p-4">
                                        <div className="flex items-center gap-4">
                                            <div className="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                                <BookOpen className="w-6 h-6 text-primary-600 dark:text-primary-400" />
                                            </div>
                                            <div className="flex-1">
                                                <div className="flex items-center gap-2">
                                                    <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                                        {plan.name || `سورة ${plan.start_chapter}`}
                                                    </h3>
                                                    <span className={`text-xs px-2 py-0.5 rounded-full ${statusLabels[plan.status]?.color || 'bg-gray-200'}`}>
                                                        {statusLabels[plan.status]?.label || plan.status}
                                                    </span>
                                                </div>
                                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                                    {plan.start_chapter} - {plan.end_chapter}
                                                </p>
                                                <div className="mt-2 flex items-center gap-4">
                                                    <span className="text-xs text-text-light dark:text-text-dark-muted">
                                                        {plan.completed_items} / {plan.total_items} عنصر
                                                    </span>
                                                    <span className="text-xs text-primary-600 dark:text-primary-400">
                                                        {plan.progress}% مكتمل
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex items-center">
                                                {/* Progress Circle */}
                                                <div className="relative w-12 h-12">
                                                    <svg className="w-12 h-12 transform -rotate-90">
                                                        <circle
                                                            cx="24"
                                                            cy="24"
                                                            r="20"
                                                            stroke="currentColor"
                                                            strokeWidth="4"
                                                            fill="transparent"
                                                            className="text-surface-200 dark:text-dark-300"
                                                        />
                                                        <circle
                                                            cx="24"
                                                            cy="24"
                                                            r="20"
                                                            stroke="currentColor"
                                                            strokeWidth="4"
                                                            fill="transparent"
                                                            strokeDasharray={`${plan.progress * 1.26} 126`}
                                                            className="text-primary-500"
                                                        />
                                                    </svg>
                                                    <span className="absolute inset-0 flex items-center justify-center text-xs font-bold text-text-primary dark:text-text-dark-primary">
                                                        {plan.progress}%
                                                    </span>
                                                </div>
                                                <ChevronLeft className="w-5 h-5 text-text-muted mr-2" />
                                            </div>
                                        </div>
                                    </Card>
                                </Link>
                            </motion.div>
                        ))}
                    </div>
                ) : (
                    <Card className="p-12 text-center">
                        <div className="w-20 h-20 mx-auto mb-6 bg-surface-200 dark:bg-dark-300 rounded-full flex items-center justify-center">
                            <Target className="w-10 h-10 text-text-muted" />
                        </div>
                        <h3 className="text-xl font-bold text-text-primary dark:text-text-dark-primary mb-2">
                            لا توجد خطط بعد
                        </h3>
                        <p className="text-text-muted dark:text-text-dark-muted mb-6">
                            ابدأ رحلة الحفظ بإنشاء خطتك الأولى
                        </p>
                        <Link href="/app/plans/create">
                            <Button icon={Plus} size="lg">
                                إنشاء خطة جديدة
                            </Button>
                        </Link>
                    </Card>
                )}
            </div>
        </MainLayout>
    );
}
