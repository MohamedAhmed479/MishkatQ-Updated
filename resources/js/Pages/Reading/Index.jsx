import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    BookOpen,
    Plus,
    Play,
    Pause,
    Target,
    TrendingUp,
    Calendar,
    Flame,
    Award,
    ChevronLeft,
    Settings,
    BarChart3,
    Clock,
    CheckCircle,
    AlertCircle,
    Zap,
    Moon,
    Sun,
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

export default function ReadingHub({ activePlan, statistics, suggestions }) {
    const stats = statistics?.data || {};
    const plan = activePlan?.data?.plan || null;
    const dailyWird = activePlan?.data?.daily_wird || null;
    const streakInfo = activePlan?.data?.streak_info || {};
    const suggestedPlans = suggestions?.data?.suggestions || [];

    return (
        <MainLayout title="ورد القراءة">
            <Head title="ورد القراءة - مشكاة" />

            <div className="space-y-6 pb-24">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                            ورد القراءة
                        </h1>
                        <p className="text-text-muted dark:text-text-dark-muted mt-1">
                            {plan ? 'تابع وردك اليومي' : 'ابدأ رحلتك مع القرآن'}
                        </p>
                    </div>
                    {plan && (
                        <Link href="/app/reading/statistics">
                            <Button variant="ghost" size="sm" icon={BarChart3}>
                                الإحصائيات
                            </Button>
                        </Link>
                    )}
                </div>

                {/* Active Plan Card or Create New */}
                {plan ? (
                    <ActivePlanCard 
                        plan={plan} 
                        dailyWird={dailyWird} 
                        streakInfo={streakInfo} 
                    />
                ) : (
                    <CreatePlanPrompt suggestions={suggestedPlans} />
                )}

                {/* Quick Stats */}
                {plan && (
                    <div className="grid grid-cols-2 gap-4">
                        <StatCard
                            icon={Flame}
                            label="سلسلة الأيام"
                            value={streakInfo.current_streak || 0}
                            suffix="يوم"
                            color="warning"
                        />
                        <StatCard
                            icon={Award}
                            label="الختمات"
                            value={plan.hatmah_count || 0}
                            suffix="ختمة"
                            color="success"
                        />
                        <StatCard
                            icon={Target}
                            label="صفحات الأسبوع"
                            value={stats.pages_this_week || 0}
                            suffix="صفحة"
                            color="primary"
                        />
                        <StatCard
                            icon={Clock}
                            label="أيام متبقية"
                            value={plan.days_remaining || 0}
                            suffix="يوم"
                            color="accent"
                        />
                    </div>
                )}

                {/* Reading History Chart */}
                {plan && stats.reading_history?.length > 0 && (
                    <ReadingHistoryCard history={stats.reading_history} />
                )}

                {/* Quick Suggestions for new users */}
                {!plan && suggestedPlans.length > 0 && (
                    <SuggestionsGrid suggestions={suggestedPlans} />
                )}
            </div>

            {/* Floating Action Button */}
            {plan && (
                <FloatingReadButton 
                    plan={plan} 
                    hasReadToday={streakInfo.has_read_today}
                />
            )}
        </MainLayout>
    );
}

// Active Plan Card with Progress Ring
function ActivePlanCard({ plan, dailyWird, streakInfo }) {
    const progressPercentage = plan.progress_percentage || 0;
    const circumference = 2 * Math.PI * 45;
    const strokeDashoffset = circumference - (progressPercentage / 100) * circumference;

    return (
        <Card className="overflow-hidden">
            <div className="bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 p-6 text-white">
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h2 className="text-lg font-bold">{plan.name}</h2>
                        <p className="text-primary-100 text-sm">
                            {plan.reading_mode === 'tadabbur' ? 'وضع التدبر' : 'وضع القراءة السريعة'}
                        </p>
                    </div>
                    <Link href={`/app/reading/plans/${plan.id}/settings`}>
                        <motion.button
                            whileHover={{ rotate: 90 }}
                            className="p-2 rounded-full bg-white/20 hover:bg-white/30 transition"
                        >
                            <Settings className="w-5 h-5" />
                        </motion.button>
                    </Link>
                </div>

                {/* Progress Ring */}
                <div className="flex items-center justify-center py-4">
                    <div className="relative">
                        <svg className="w-32 h-32 transform -rotate-90">
                            {/* Background circle */}
                            <circle
                                cx="64"
                                cy="64"
                                r="45"
                                stroke="rgba(255,255,255,0.2)"
                                strokeWidth="8"
                                fill="none"
                            />
                            {/* Progress circle */}
                            <motion.circle
                                cx="64"
                                cy="64"
                                r="45"
                                stroke="white"
                                strokeWidth="8"
                                fill="none"
                                strokeLinecap="round"
                                strokeDasharray={circumference}
                                initial={{ strokeDashoffset: circumference }}
                                animate={{ strokeDashoffset }}
                                transition={{ duration: 1, ease: "easeOut" }}
                            />
                        </svg>
                        <div className="absolute inset-0 flex flex-col items-center justify-center">
                            <span className="text-3xl font-bold">{Math.round(progressPercentage)}%</span>
                            <span className="text-xs text-primary-100">مكتمل</span>
                        </div>
                    </div>
                </div>

                {/* Daily Wird Info */}
                {dailyWird && (
                    <div className="bg-white/10 rounded-xl p-4 mt-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-primary-100 text-sm">ورد اليوم</p>
                                <p className="text-xl font-bold">
                                    صفحة {dailyWird.start_page} - {dailyWird.end_page}
                                </p>
                            </div>
                            <div className="text-left">
                                <p className="text-primary-100 text-sm">عدد الصفحات</p>
                                <p className="text-xl font-bold">{dailyWird.pages_count}</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Status Indicators */}
                <div className="flex items-center gap-4 mt-4 text-sm">
                    <div className="flex items-center gap-2">
                        <div className={`w-2 h-2 rounded-full ${streakInfo.has_read_today ? 'bg-green-400' : 'bg-yellow-400'}`} />
                        <span className="text-primary-100">
                            {streakInfo.has_read_today ? 'تمت القراءة اليوم' : 'لم تقرأ اليوم بعد'}
                        </span>
                    </div>
                    {streakInfo.is_streak_broken && (
                        <div className="flex items-center gap-2 text-yellow-300">
                            <AlertCircle className="w-4 h-4" />
                            <span>السلسلة انقطعت</span>
                        </div>
                    )}
                </div>
            </div>
        </Card>
    );
}

// Create Plan Prompt for new users
function CreatePlanPrompt({ suggestions }) {
    return (
        <Card className="overflow-hidden">
            <div className="bg-gradient-to-br from-accent-500 via-accent-600 to-primary-600 p-8 text-white text-center">
                <motion.div
                    initial={{ scale: 0 }}
                    animate={{ scale: 1 }}
                    transition={{ type: "spring", stiffness: 200 }}
                    className="w-20 h-20 mx-auto mb-4 rounded-full bg-white/20 flex items-center justify-center"
                >
                    <BookOpen className="w-10 h-10" />
                </motion.div>
                <h2 className="text-2xl font-bold mb-2">ابدأ رحلتك مع القرآن</h2>
                <p className="text-white/80 mb-6">
                    أنشئ خطة قراءة مخصصة تناسب وقتك وأهدافك
                </p>
                <Link href="/app/reading/plans/create">
                    <Button
                        variant="secondary"
                        size="lg"
                        icon={Plus}
                        className="bg-white text-primary-600 hover:bg-white/90"
                    >
                        إنشاء خطة جديدة
                    </Button>
                </Link>
            </div>
        </Card>
    );
}

// Stat Card Component
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
                        {value} <span className="text-sm font-normal text-text-muted">{suffix}</span>
                    </p>
                    <p className="text-xs text-text-muted dark:text-text-dark-muted">
                        {label}
                    </p>
                </div>
            </div>
        </Card>
    );
}

// Reading History Card
function ReadingHistoryCard({ history }) {
    const maxPages = Math.max(...history.map(h => h.total_pages || 0), 1);

    return (
        <Card>
            <CardHeader>
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <Calendar className="w-5 h-5 text-primary-600 dark:text-primary-400" />
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
                <div className="flex items-end gap-1 h-24 overflow-x-auto pb-2">
                    {history.slice(-14).map((day, index) => {
                        const height = Math.max((day.total_pages / maxPages) * 100, 5);
                        return (
                            <motion.div
                                key={day.date}
                                initial={{ height: 0 }}
                                animate={{ height: `${height}%` }}
                                transition={{ delay: index * 0.05 }}
                                className="flex-1 min-w-[16px] bg-gradient-to-t from-primary-500 to-primary-400 rounded-t-md"
                                title={`${day.date}: ${day.total_pages} صفحة`}
                            />
                        );
                    })}
                </div>
                <div className="flex justify-between text-xs text-text-muted mt-2">
                    <span>قبل أسبوعين</span>
                    <span>اليوم</span>
                </div>
            </CardContent>
        </Card>
    );
}

// Suggestions Grid for Quick Plan Creation
function SuggestionsGrid({ suggestions }) {
    return (
        <div className="space-y-4">
            <h2 className="text-lg font-bold text-text-primary dark:text-text-dark-primary">
                خطط مقترحة
            </h2>
            <div className="grid grid-cols-2 gap-4">
                {suggestions.map((suggestion, index) => (
                    <motion.div
                        key={suggestion.name}
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: index * 0.1 }}
                    >
                        <Link
                            href={`/app/reading/plans/create?pages=${suggestion.pages_per_day}&days=${suggestion.days}`}
                        >
                            <Card hover className="p-4 h-full">
                                <div className="flex flex-col h-full">
                                    <div className="flex items-center justify-between mb-2">
                                        <Zap className="w-5 h-5 text-accent-500" />
                                        <span className={`text-xs px-2 py-1 rounded-full ${
                                            suggestion.difficulty === 'صعب جداً' ? 'bg-red-100 text-red-600' :
                                            suggestion.difficulty === 'متوسط' ? 'bg-yellow-100 text-yellow-600' :
                                            'bg-green-100 text-green-600'
                                        }`}>
                                            {suggestion.difficulty}
                                        </span>
                                    </div>
                                    <h3 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        {suggestion.name}
                                    </h3>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1 flex-1">
                                        {suggestion.description}
                                    </p>
                                    <div className="mt-3 pt-3 border-t border-surface-200 dark:border-dark-300">
                                        <p className="text-xs text-text-muted">
                                            <span className="font-bold text-primary-600">{suggestion.pages_per_day}</span> صفحة/يوم
                                        </p>
                                    </div>
                                </div>
                            </Card>
                        </Link>
                    </motion.div>
                ))}
            </div>
        </div>
    );
}

// Floating Action Button for Reading
function FloatingReadButton({ plan, hasReadToday }) {
    return (
        <div className="fixed bottom-6 left-1/2 -translate-x-1/2 z-50">
            <Link href={`/app/reading/experience/${plan.id}`}>
                <motion.button
                    whileHover={{ scale: 1.05 }}
                    whileTap={{ scale: 0.95 }}
                    className={`
                        flex items-center gap-3 px-8 py-4 rounded-full shadow-2xl
                        ${hasReadToday 
                            ? 'bg-gradient-to-r from-green-500 to-green-600' 
                            : 'bg-gradient-to-r from-primary-500 to-primary-600'
                        }
                        text-white font-bold text-lg
                    `}
                >
                    {hasReadToday ? (
                        <>
                            <CheckCircle className="w-6 h-6" />
                            <span>تابع القراءة</span>
                        </>
                    ) : (
                        <>
                            <Play className="w-6 h-6" />
                            <span>ابدأ الورد</span>
                        </>
                    )}
                </motion.button>
            </Link>
        </div>
    );
}
