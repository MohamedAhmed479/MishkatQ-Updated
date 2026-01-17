import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { 
    BarChart3, 
    TrendingUp, 
    Clock, 
    Target,
    Calendar,
    CheckCircle,
    XCircle
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';

export default function AnalyticsIndex({ analytics }) {
    // Default values if analytics is empty
    const data = analytics || {
        total_memorized: 0,
        total_reviewed: 0,
        average_rating: 0,
        completion_rate: 0,
        weekly_progress: [],
        chapters_progress: []
    };

    return (
        <MainLayout title="التحليلات">
            <Head title="تحليلات الأداء" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        تحليلات الأداء
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        تابع تقدمك وأدائك في الحفظ
                    </p>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <StatCard
                        icon={Target}
                        label="آيات محفوظة"
                        value={data.total_memorized || 0}
                        trend="+5%"
                        color="primary"
                    />
                    <StatCard
                        icon={CheckCircle}
                        label="مراجعات مكتملة"
                        value={data.total_reviewed || 0}
                        trend="+12%"
                        color="success"
                    />
                    <StatCard
                        icon={TrendingUp}
                        label="متوسط التقييم"
                        value={`${(data.average_rating || 0).toFixed(1)}/5`}
                        color="accent"
                    />
                    <StatCard
                        icon={Clock}
                        label="معدل الإنجاز"
                        value={`${data.completion_rate || 0}%`}
                        color="primary"
                    />
                </div>

                {/* Weekly Progress */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <Calendar className="w-5 h-5 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    تقدم الأسبوع
                                </h2>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    النشاط اليومي
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-end justify-between h-40 gap-2">
                            {['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'].map((day, index) => {
                                const height = Math.random() * 80 + 20; // Placeholder data
                                return (
                                    <div key={day} className="flex-1 flex flex-col items-center gap-2">
                                        <motion.div
                                            initial={{ height: 0 }}
                                            animate={{ height: `${height}%` }}
                                            transition={{ delay: index * 0.1 }}
                                            className="w-full bg-gradient-to-t from-primary-500 to-primary-400 rounded-t-lg"
                                        />
                                        <span className="text-xs text-text-muted dark:text-text-dark-muted">
                                            {day.slice(0, 3)}
                                        </span>
                                    </div>
                                );
                            })}
                        </div>
                    </CardContent>
                </Card>

                {/* Performance Metrics */}
                <div className="grid md:grid-cols-2 gap-6">
                    {/* Memorization Quality */}
                    <Card>
                        <CardHeader>
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                جودة الحفظ
                            </h2>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {[
                                    { label: 'ممتاز (5)', percentage: 30, color: 'bg-success' },
                                    { label: 'جيد جداً (4)', percentage: 40, color: 'bg-primary-500' },
                                    { label: 'جيد (3)', percentage: 20, color: 'bg-accent-500' },
                                    { label: 'مقبول (2)', percentage: 7, color: 'bg-warning' },
                                    { label: 'ضعيف (1)', percentage: 3, color: 'bg-error' },
                                ].map((item) => (
                                    <div key={item.label} className="space-y-1">
                                        <div className="flex justify-between text-sm">
                                            <span className="text-text-primary dark:text-text-dark-primary">
                                                {item.label}
                                            </span>
                                            <span className="text-text-muted dark:text-text-dark-muted">
                                                {item.percentage}%
                                            </span>
                                        </div>
                                        <div className="h-2 bg-surface-200 dark:bg-dark-300 rounded-full overflow-hidden">
                                            <motion.div
                                                initial={{ width: 0 }}
                                                animate={{ width: `${item.percentage}%` }}
                                                transition={{ duration: 0.5 }}
                                                className={`h-full ${item.color} rounded-full`}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Review Performance */}
                    <Card>
                        <CardHeader>
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                أداء المراجعات
                            </h2>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="text-center p-4 bg-success/10 rounded-2xl">
                                    <CheckCircle className="w-8 h-8 mx-auto mb-2 text-success" />
                                    <p className="text-2xl font-bold text-success">85%</p>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        مراجعات ناجحة
                                    </p>
                                </div>
                                <div className="text-center p-4 bg-error/10 rounded-2xl">
                                    <XCircle className="w-8 h-8 mx-auto mb-2 text-error" />
                                    <p className="text-2xl font-bold text-error">15%</p>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        تحتاج تركيز أكثر
                                    </p>
                                </div>
                            </div>

                            <div className="mt-6 p-4 bg-surface-100 dark:bg-dark-300 rounded-2xl">
                                <h4 className="font-medium text-text-primary dark:text-text-dark-primary mb-2">
                                    نصيحة
                                </h4>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    أداؤك ممتاز! حافظ على هذا المستوى وركز على المراجعات اليومية.
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </MainLayout>
    );
}

function StatCard({ icon: Icon, label, value, trend, color }) {
    const colors = {
        primary: 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400',
        success: 'bg-success/10 text-success',
        accent: 'bg-accent-100 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400',
    };

    return (
        <Card hover className="p-4">
            <div className="flex items-center gap-3">
                <div className={`w-10 h-10 rounded-xl flex items-center justify-center ${colors[color]}`}>
                    <Icon className="w-5 h-5" />
                </div>
                <div className="flex-1">
                    <p className="text-xl font-bold text-text-primary dark:text-text-dark-primary">
                        {value}
                    </p>
                    <p className="text-xs text-text-muted dark:text-text-dark-muted">
                        {label}
                    </p>
                </div>
                {trend && (
                    <span className="text-xs text-success font-medium">
                        {trend}
                    </span>
                )}
            </div>
        </Card>
    );
}
