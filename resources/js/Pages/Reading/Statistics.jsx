import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    BarChart3,
    TrendingUp,
    Calendar,
    Target,
    Flame,
    Award,
    BookOpen,
    ChevronRight,
    Clock,
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';

export default function Statistics({ statistics }) {
    const stats = statistics?.data || {};
    const activePlan = stats.active_plan || null;
    const readingHistory = stats.reading_history || [];
    const totalHatmah = stats.total_hatmah || 0;
    const longestStreak = stats.longest_streak || 0;

    // Derive weekly/monthly pages from history when available (fallback to API values)
    const pagesThisWeek = readingHistory.length
        ? readingHistory.slice(-7).reduce((sum, day) => sum + (day.total_pages || 0), 0)
        : (stats.pages_this_week || 0);

    const pagesThisMonth = readingHistory.length
        ? readingHistory.slice(-30).reduce((sum, day) => sum + (day.total_pages || 0), 0)
        : (stats.pages_this_month || 0);

    return (
        <MainLayout title="إحصائيات القراءة">
            <Head title="إحصائيات القراءة - مشكاة" />

            <div className="space-y-6 pb-8">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link
                        href="/app/reading"
                        className="p-2 rounded-xl bg-surface-100 dark:bg-dark-300 hover:bg-surface-200 dark:hover:bg-dark-200 transition"
                    >
                        <ChevronRight className="w-5 h-5 text-text-primary dark:text-text-dark-primary" />
                    </Link>
                    <div>
                        <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                            إحصائيات القراءة
                        </h1>
                        <p className="text-text-muted dark:text-text-dark-muted">
                            تابع تقدمك في قراءة القرآن
                        </p>
                    </div>
                </div>

                {/* Main Stats Grid */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <StatCard
                        icon={Award}
                        label="الختمات"
                        value={totalHatmah}
                        color="success"
                    />
                    <StatCard
                        icon={Flame}
                        label="أطول سلسلة"
                        value={longestStreak}
                        suffix="يوم"
                        color="warning"
                    />
                    <StatCard
                        icon={Target}
                        label="صفحات الأسبوع"
                        value={pagesThisWeek}
                        color="primary"
                    />
                    <StatCard
                        icon={BookOpen}
                        label="صفحات الشهر"
                        value={pagesThisMonth}
                        color="accent"
                    />
                </div>

                {/* Summary Cards */}
                <div className="grid md:grid-cols-2 gap-4">
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <BarChart3 className="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div>
                                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        ملخص الخطط
                                    </h2>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex justify-between items-center">
                                    <span className="text-text-muted dark:text-text-dark-muted">
                                        إجمالي الخطط
                                    </span>
                                    <span className="font-bold text-text-primary dark:text-text-dark-primary">
                                        {stats.total_plans || 0}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-text-muted dark:text-text-dark-muted">
                                        خطط مكتملة
                                    </span>
                                    <span className="font-bold text-green-600">
                                        {stats.completed_plans || 0}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-text-muted dark:text-text-dark-muted">
                                        السلسلة الحالية
                                    </span>
                                    <span className="font-bold text-orange-600">
                                        {stats.current_streak || 0} يوم
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {activePlan && (
                        <Card className="bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                            <CardContent className="py-6">
                                <h2 className="font-bold mb-4">الخطة النشطة</h2>
                                <div className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-primary-100">اسم الخطة</span>
                                        <span className="font-medium">{activePlan.name}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-primary-100">التقدم</span>
                                        <span className="font-medium">
                                            {Math.round(activePlan.progress_percentage || 0)}%
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-primary-100">الصفحات المتبقية</span>
                                        <span className="font-medium">{activePlan.remaining_pages ?? '—'}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-primary-100">أيام متبقية</span>
                                        <span className="font-medium">{activePlan.days_remaining ?? '—'}</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Reading History Chart */}
                {readingHistory.length > 0 && (
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                                    <Calendar className="w-5 h-5 text-accent-600 dark:text-accent-400" />
                                </div>
                                <div>
                                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        سجل القراءة
                                    </h2>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        آخر 30 يوم
                                    </p>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <ReadingChart history={readingHistory} />
                        </CardContent>
                    </Card>
                )}

                {/* Achievements Summary */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                                <Award className="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div>
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    الإنجازات
                                </h2>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-3 gap-4 text-center">
                            <AchievementBadge
                                icon="📖"
                                label="ختمات"
                                value={stats.total_hatmah || 0}
                                unlocked={(stats.total_hatmah || 0) > 0}
                            />
                            <AchievementBadge
                                icon="🔥"
                                label="سلسلة 7 أيام"
                                value={7}
                                unlocked={(stats.longest_streak || 0) >= 7}
                            />
                            <AchievementBadge
                                icon="⭐"
                                label="سلسلة 30 يوم"
                                value={30}
                                unlocked={(stats.longest_streak || 0) >= 30}
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}

function StatCard({ icon: Icon, label, value, suffix, color }) {
    const colors = {
        primary: 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400',
        success: 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        warning: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
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
                        {suffix && <span className="text-sm font-normal text-text-muted mr-1">{suffix}</span>}
                    </p>
                    <p className="text-xs text-text-muted dark:text-text-dark-muted">
                        {label}
                    </p>
                </div>
            </div>
        </Card>
    );
}

function ReadingChart({ history }) {
    const recentHistory = history.slice(-30);
    const maxPages = Math.max(...recentHistory.map(h => h.total_pages || 0), 1);

    return (
        <div className="space-y-4">
            <div className="overflow-x-auto pb-2">
                <div className="flex items-end gap-2 h-32 min-w-[320px]">
                    {recentHistory.map((day, index) => {
                        const height = Math.max((day.total_pages / maxPages) * 100, 5);
                        const date = new Date(day.date);
                        const isToday = date.toDateString() === new Date().toDateString();
                        
                        return (
                            <div key={day.date} className="flex flex-col items-center shrink-0 w-2.5 md:w-3">
                                <motion.div
                                    initial={{ height: 0 }}
                                    animate={{ height: `${height}%` }}
                                    transition={{ delay: index * 0.03, duration: 0.4 }}
                                    className={`w-full rounded-md ${
                                        isToday 
                                            ? 'bg-gradient-to-t from-accent-500 to-accent-400' 
                                            : 'bg-gradient-to-t from-primary-500 to-primary-400'
                                    }`}
                                    title={`${day.date}: ${day.total_pages} صفحة`}
                                />
                            </div>
                        );
                    })}
                    {!recentHistory.length && (
                        <div className="text-sm text-text-muted">لا يوجد بيانات للعرض</div>
                    )}
                </div>
            </div>
            <div className="flex justify-between text-xs text-text-muted">
                <span>الأقدم</span>
                <span>الأحدث</span>
            </div>
        </div>
    );
}

function AchievementBadge({ icon, label, value, unlocked }) {
    return (
        <div className={`p-4 rounded-xl ${unlocked ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-surface-100 dark:bg-dark-300 opacity-50'}`}>
            <div className="text-3xl mb-2">{icon}</div>
            <p className={`text-sm font-medium ${unlocked ? 'text-yellow-700 dark:text-yellow-400' : 'text-text-muted'}`}>
                {label}
            </p>
        </div>
    );
}
