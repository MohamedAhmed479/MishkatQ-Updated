import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    BookOpen,
    RefreshCw,
    Trophy,
    Flame,
    Award,
    ChevronLeft,
    Play,
    Plus,
    Target
} from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';

const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
        opacity: 1,
        transition: {
            staggerChildren: 0.1
        }
    }
};

const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { opacity: 1, y: 0 }
};

export default function Dashboard({
    activePlan,
    todayItem,
    pendingRevisionsCount,
    stats,
    recentBadges,
    weeklyActivity
}) {
    return (
        <MainLayout title="الرئيسية">
            <Head title="لوحة التحكم" />

            <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="space-y-6"
            >
                {/* Welcome Section */}
                <motion.div variants={itemVariants}>
                    <h1 className="text-2xl md:text-3xl font-bold text-text-primary dark:text-text-dark-primary">
                        السلام عليكم 👋
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        واصل رحلتك في حفظ كتاب الله
                    </p>
                </motion.div>

                {/* Stats Grid */}
                <motion.div
                    variants={itemVariants}
                    className="grid grid-cols-2 md:grid-cols-4 gap-4"
                >
                    <StatCard
                        icon={BookOpen}
                        label="آية محفوظة"
                        value={stats.total_verses_memorized}
                        color="primary"
                    />
                    <StatCard
                        icon={Flame}
                        label="يوم متتالي"
                        value={stats.current_streak}
                        color="accent"
                    />
                    <StatCard
                        icon={Trophy}
                        label="نقطة"
                        value={stats.total_points}
                        color="primary"
                    />
                    <StatCard
                        icon={Award}
                        label="شارة"
                        value={stats.badges_count}
                        color="accent"
                    />
                </motion.div>

                {/* Main Actions */}
                <div className="grid md:grid-cols-2 gap-6">
                    {/* Today's Memorization */}
                    <motion.div variants={itemVariants}>
                        <Card className="h-full">
                            <CardHeader className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                        <BookOpen className="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        حفظ اليوم
                                    </h2>
                                </div>
                                {activePlan && (
                                    <span className="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 px-2 py-1 rounded-lg">
                                        {activePlan.progress}% مكتمل
                                    </span>
                                )}
                            </CardHeader>
                            <CardContent>
                                {todayItem ? (
                                    <div className="space-y-4">
                                        <div className="bg-surface-100 dark:bg-dark-300 rounded-xl p-4">
                                            <p className="text-lg font-bold text-text-primary dark:text-text-dark-primary">
                                                سورة {todayItem.chapter_name}
                                            </p>
                                            <p className="text-text-muted dark:text-text-dark-muted">
                                                الآيات {todayItem.start_verse} - {todayItem.end_verse}
                                            </p>
                                            <p className="text-sm text-text-light dark:text-text-dark-muted mt-2">
                                                {todayItem.word_count} كلمة
                                            </p>
                                        </div>
                                        <Link href={`/app/session/${todayItem.id}`}>
                                            <Button className="w-full" icon={Play}>
                                                ابدأ الحفظ
                                            </Button>
                                        </Link>
                                    </div>
                                ) : activePlan ? (
                                    <div className="text-center py-6">
                                        <div className="w-16 h-16 mx-auto mb-4 bg-success/10 rounded-full flex items-center justify-center">
                                            <Target className="w-8 h-8 text-success" />
                                        </div>
                                        <p className="text-text-primary dark:text-text-dark-primary font-medium">
                                            أكملت حفظ اليوم! 🎉
                                        </p>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1">
                                            لا تنسَ المراجعات
                                        </p>
                                    </div>
                                ) : (
                                    <div className="text-center py-6">
                                        <div className="w-16 h-16 mx-auto mb-4 bg-surface-200 dark:bg-dark-300 rounded-full flex items-center justify-center">
                                            <Plus className="w-8 h-8 text-text-muted" />
                                        </div>
                                        <p className="text-text-primary dark:text-text-dark-primary font-medium">
                                            لا توجد خطة نشطة
                                        </p>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1 mb-4">
                                            أنشئ خطة حفظ جديدة للبدء
                                        </p>
                                        <Link href="/app/plans/create">
                                            <Button variant="outline" icon={Plus}>
                                                إنشاء خطة
                                            </Button>
                                        </Link>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </motion.div>

                    {/* Today's Revisions */}
                    <motion.div variants={itemVariants}>
                        <Card className="h-full">
                            <CardHeader className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                                        <RefreshCw className="w-5 h-5 text-accent-600 dark:text-accent-400" />
                                    </div>
                                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        مراجعات اليوم
                                    </h2>
                                </div>
                                {pendingRevisionsCount > 0 && (
                                    <span className="text-xs bg-error/10 text-error px-2 py-1 rounded-lg">
                                        {pendingRevisionsCount} في الانتظار
                                    </span>
                                )}
                            </CardHeader>
                            <CardContent>
                                {pendingRevisionsCount > 0 ? (
                                    <div className="space-y-4">
                                        <div className="bg-accent-50 dark:bg-accent-900/20 border border-accent-200 dark:border-accent-800 rounded-xl p-4">
                                            <p className="text-4xl font-bold text-accent-600 dark:text-accent-400 text-center">
                                                {pendingRevisionsCount}
                                            </p>
                                            <p className="text-center text-text-muted dark:text-text-dark-muted mt-1">
                                                مراجعة تنتظرك
                                            </p>
                                        </div>
                                        <Link href="/app/revisions">
                                            <Button variant="accent" className="w-full" icon={RefreshCw}>
                                                ابدأ المراجعة
                                            </Button>
                                        </Link>
                                    </div>
                                ) : (
                                    <div className="text-center py-6">
                                        <div className="w-16 h-16 mx-auto mb-4 bg-success/10 rounded-full flex items-center justify-center">
                                            <RefreshCw className="w-8 h-8 text-success" />
                                        </div>
                                        <p className="text-text-primary dark:text-text-dark-primary font-medium">
                                            لا توجد مراجعات اليوم
                                        </p>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1">
                                            استمر في الحفظ لإنشاء مراجعات
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </motion.div>
                </div>

                {/* Weekly Activity Heatmap */}
                <motion.div variants={itemVariants}>
                    <Card>
                        <CardHeader>
                            <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                نشاط الأسبوع
                            </h2>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-center justify-between gap-2">
                                {weeklyActivity.map((day, index) => (
                                    <motion.div
                                        key={day.date}
                                        initial={{ scale: 0 }}
                                        animate={{ scale: 1 }}
                                        transition={{ delay: index * 0.05 }}
                                        className="flex-1 text-center"
                                    >
                                        <div
                                            className={`
                                                w-full aspect-square rounded-lg mb-2 flex items-center justify-center
                                                ${day.active
                                                    ? 'bg-primary-500 dark:bg-primary-600'
                                                    : 'bg-surface-200 dark:bg-dark-300'
                                                }
                                            `}
                                        >
                                            {day.active && (
                                                <Flame className="w-4 h-4 text-white" />
                                            )}
                                        </div>
                                        <span className="text-xs text-text-muted dark:text-text-dark-muted">
                                            {day.day.slice(0, 3)}
                                        </span>
                                    </motion.div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </motion.div>

                {/* Recent Badges */}
                {recentBadges.length > 0 && (
                    <motion.div variants={itemVariants}>
                        <Card>
                            <CardHeader className="flex items-center justify-between">
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    آخر الشارات
                                </h2>
                                <Link
                                    href="/app/achievements"
                                    className="text-sm text-primary-600 dark:text-primary-400 flex items-center gap-1 hover:underline"
                                >
                                    <span>عرض الكل</span>
                                    <ChevronLeft className="w-4 h-4" />
                                </Link>
                            </CardHeader>
                            <CardContent>
                                <div className="flex gap-4">
                                    {recentBadges.map((badge) => (
                                        <motion.div
                                            key={badge.id}
                                            whileHover={{ scale: 1.1 }}
                                            className="text-center"
                                        >
                                            <div className="w-14 h-14 mx-auto mb-2 bg-gradient-to-br from-accent-400 to-accent-600 rounded-full flex items-center justify-center text-2xl">
                                                {badge.icon || '🏆'}
                                            </div>
                                            <p className="text-xs text-text-muted dark:text-text-dark-muted">
                                                {badge.name}
                                            </p>
                                        </motion.div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    </motion.div>
                )}

                {/* Quick Links */}
                <motion.div variants={itemVariants}>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <QuickLinkCard
                            href="/app/quran"
                            icon={BookOpen}
                            label="تصفح القرآن"
                            color="primary"
                        />
                        <QuickLinkCard
                            href="/app/plans"
                            icon={Target}
                            label="خططي"
                            color="accent"
                        />
                        <QuickLinkCard
                            href="/app/leaderboard"
                            icon={Trophy}
                            label="لوحة الصدارة"
                            color="primary"
                        />
                        <QuickLinkCard
                            href="/app/analytics"
                            icon={Flame}
                            label="تحليلاتي"
                            color="accent"
                        />
                    </div>
                </motion.div>
            </motion.div>
        </MainLayout>
    );
}

function StatCard({ icon: Icon, label, value, color }) {
    return (
        <Card hover className="p-4">
            <div className="flex items-center gap-3">
                <div className={`
                    w-10 h-10 rounded-xl flex items-center justify-center
                    ${color === 'primary'
                        ? 'bg-primary-100 dark:bg-primary-900/30'
                        : 'bg-accent-100 dark:bg-accent-900/30'
                    }
                `}>
                    <Icon className={`w-5 h-5 ${color === 'primary'
                        ? 'text-primary-600 dark:text-primary-400'
                        : 'text-accent-600 dark:text-accent-400'
                        }`} />
                </div>
                <div>
                    <p className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        {value}
                    </p>
                    <p className="text-xs text-text-muted dark:text-text-dark-muted">
                        {label}
                    </p>
                </div>
            </div>
        </Card>
    );
}

function QuickLinkCard({ href, icon: Icon, label, color }) {
    return (
        <Link href={href}>
            <Card hover className="p-4 text-center">
                <div className={`
                    w-12 h-12 mx-auto mb-2 rounded-xl flex items-center justify-center
                    ${color === 'primary'
                        ? 'bg-primary-100 dark:bg-primary-900/30'
                        : 'bg-accent-100 dark:bg-accent-900/30'
                    }
                `}>
                    <Icon className={`w-6 h-6 ${color === 'primary'
                        ? 'text-primary-600 dark:text-primary-400'
                        : 'text-accent-600 dark:text-accent-400'
                        }`} />
                </div>
                <p className="text-sm font-medium text-text-primary dark:text-text-dark-primary">
                    {label}
                </p>
            </Card>
        </Link>
    );
}
