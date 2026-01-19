import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import {
    BookOpen,
    RefreshCw,
    Trophy,
    Flame,
    Award,
    ChevronLeft,
    Play,
    Plus,
    Target,
    Star,
    Sparkles,
    TrendingUp
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
    weeklyActivity,
    readingSummary,
    ayaOfTheDay,
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

                {/* Aya of the Day */}
                {ayaOfTheDay && (
                    <motion.div variants={itemVariants}>
                        <AyaOfTheDay aya={ayaOfTheDay} />
                    </motion.div>
                )}

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
                                            <div className="flex items-center gap-4 mt-2">
                                                <span className="text-sm text-text-light dark:text-text-dark-muted">
                                                    {todayItem.verses_count || (todayItem.end_verse - todayItem.start_verse + 1)} آية
                                                </span>
                                                <span className="text-sm text-text-light dark:text-text-dark-muted">
                                                    {todayItem.word_count || 0} كلمة
                                                </span>
                                            </div>
                                        </div>
                                        <Link href={`/app/session/${todayItem.id}`}>
                                            <Button className="w-full" icon={Play}>
                                                ابدأ الحفظ
                                            </Button>
                                        </Link>
                                    </div>
                                ) : activePlan ? (
                                    <div className="text-center py-6">
                                        <div className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-success/20 to-success/10 rounded-full flex items-center justify-center">
                                            <Sparkles className="w-8 h-8 text-success" />
                                        </div>
                                        <p className="text-text-primary dark:text-text-dark-primary font-medium">
                                            بارك الله فيك! أكملت حفظ اليوم
                                        </p>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1">
                                            "خيركم من تعلم القرآن وعلمه"
                                        </p>
                                        <Link href="/app/revisions" className="mt-4 inline-block">
                                            <Button variant="outline" size="sm" icon={RefreshCw}>
                                                راجع محفوظاتك
                                            </Button>
                                        </Link>
                                    </div>
                                ) : (
                                    <div className="text-center py-6">
                                        <div className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/30 dark:to-primary-900/10 rounded-full flex items-center justify-center">
                                            <BookOpen className="w-8 h-8 text-primary-500 dark:text-primary-400" />
                                        </div>
                                        <p className="text-text-primary dark:text-text-dark-primary font-medium">
                                            ابدأ رحلتك مع القرآن
                                        </p>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1 mb-4">
                                            "إن الذي يقرأ القرآن وهو ماهر به مع السفرة الكرام البررة"
                                        </p>
                                        <Link href="/app/plans/create">
                                            <Button icon={Plus}>
                                                أنشئ خطة حفظ
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
                                        <div className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-accent-100 to-accent-50 dark:from-accent-900/30 dark:to-accent-900/10 rounded-full flex items-center justify-center">
                                            <Star className="w-8 h-8 text-accent-500 dark:text-accent-400" />
                                        </div>
                                        <p className="text-text-primary dark:text-text-dark-primary font-medium">
                                            لا توجد مراجعات مطلوبة
                                        </p>
                                        <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1">
                                            تابع الحفظ لتُنشأ مراجعات تلقائية
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </motion.div>
                </div>

                {/* Daily Reading (Wird) */}
                <motion.div variants={itemVariants}>
                    <Card>
                        <CardHeader className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <BookOpen className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        ورد القراءة اليومي
                                    </h2>
                                    <p className="text-xs text-text-muted dark:text-text-dark-muted mt-0.5">
                                        متابعة ختمة القرآن بالقراءة
                                    </p>
                                </div>
                            </div>
                            {readingSummary && (
                                <span className={`text-xs px-2 py-1 rounded-lg ${
                                    readingSummary.has_read_today
                                        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'
                                        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300'
                                }`}>
                                    {readingSummary.has_read_today ? 'تم إتمام ورد اليوم 🎉' : 'لم تُكمل ورد اليوم بعد'}
                                </span>
                            )}
                        </CardHeader>
                        <CardContent>
                            {readingSummary ? (
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between bg-surface-100 dark:bg-dark-300 rounded-xl p-4">
                                        <div>
                                            <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                                خطة القراءة
                                            </p>
                                            <p className="text-lg font-bold text-text-primary dark:text-text-dark-primary">
                                                {readingSummary.name}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                                ورد اليوم
                                            </p>
                                            <p className="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                صفحة {readingSummary.start_page} - {readingSummary.end_page}
                                            </p>
                                            <p className="text-xs text-text-muted dark:text-text-dark-muted mt-1">
                                                {readingSummary.pages_count} صفحة
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex flex-col sm:flex-row gap-3">
                                        <Link href={`/app/reading/experience/${readingSummary.plan_id}`}>
                                            <Button className="w-full" icon={Play}>
                                                {readingSummary.has_read_today ? 'تابع القراءة' : 'ابدأ قراءة الورد'}
                                            </Button>
                                        </Link>
                                        <Link href="/app/reading">
                                            <Button variant="outline" className="w-full">
                                                إدارة خطة القراءة
                                            </Button>
                                        </Link>
                                    </div>
                                </div>
                            ) : (
                                <div className="text-center py-6">
                                    <div className="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900/30 dark:to-emerald-900/10 rounded-full flex items-center justify-center">
                                        <BookOpen className="w-8 h-8 text-emerald-500 dark:text-emerald-400" />
                                    </div>
                                    <p className="text-text-primary dark:text-text-dark-primary font-medium">
                                        ابدأ ختمة القرآن الكريم
                                    </p>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted mt-1 mb-4">
                                        "اقرأ القرآن فإنه يأتي يوم القيامة شفيعاً لأصحابه"
                                    </p>
                                    <Link href="/app/reading">
                                        <Button variant="outline" icon={Plus}>
                                            أنشئ خطة قراءة
                                        </Button>
                                    </Link>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </motion.div>

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
                                        <BadgeDisplay key={badge.id} badge={badge} />
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

function BadgeDisplay({ badge }) {
    // Check if icon is an SVG file path or emoji
    const isSvgPath = badge.icon && (badge.icon.endsWith('.svg') || badge.icon.endsWith('.png') || badge.icon.endsWith('.jpg'));
    const iconUrl = isSvgPath ? `/images/badges/${badge.icon}` : null;
    const [imageError, setImageError] = useState(false);

    // Badge icon mapping for fallback
    const badgeEmojis = {
        'first-steps.svg': '🎯',
        'dedicated-learner.svg': '📚',
        'quran-scholar.svg': '🎓',
        'consistent-learner.svg': '🔥',
        'perfect-review.svg': '⭐',
        'point-collector.svg': '💰',
    };

    const displayIcon = imageError || !iconUrl
        ? (badgeEmojis[badge.icon] || badge.icon || '🏆')
        : null;

    return (
        <motion.div
            whileHover={{ scale: 1.1 }}
            className="text-center"
        >
            <div className="w-14 h-14 mx-auto mb-2 bg-gradient-to-br from-accent-400 to-accent-600 rounded-full flex items-center justify-center">
                {iconUrl && !imageError ? (
                    <img
                        src={iconUrl}
                        alt={badge.name}
                        className="w-8 h-8 object-contain"
                        onError={() => setImageError(true)}
                    />
                ) : (
                    <span className="text-2xl">{displayIcon}</span>
                )}
            </div>
            <p className="text-xs text-text-muted dark:text-text-dark-muted">
                {badge.name}
            </p>
        </motion.div>
    );
}

function QuickLinkCard({ href, icon: Icon, label, color }) {
    return (
        <Link href={href}>
            <Card hover className="p-4 text-center group transition-all duration-200">
                <div className={`
                    w-12 h-12 mx-auto mb-2 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110
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

/**
 * Aya of the Day Component - Beautiful display of a daily Quranic verse
 */
function AyaOfTheDay({ aya }) {
    if (!aya) return null;

    return (
        <Card className="overflow-hidden">
            {/* Decorative Header */}
            <div className="bg-gradient-to-r from-primary-600 via-primary-500 to-emerald-500 dark:from-primary-700 dark:via-primary-600 dark:to-emerald-600 px-6 py-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <Sparkles className="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 className="text-white font-bold text-lg">آية اليوم</h3>
                            <p className="text-white/80 text-sm">تدبر وتأمل</p>
                        </div>
                    </div>
                    <div className="text-right">
                        <p className="text-white/90 text-sm font-medium">
                            سورة {aya.chapter_name_ar}
                        </p>
                        <p className="text-white/70 text-xs">
                            الآية {aya.verse_number}
                        </p>
                    </div>
                </div>
            </div>
            
            {/* Verse Content */}
            <CardContent className="p-6">
                <div className="relative">
                    {/* Decorative Quote Marks */}
                    <span className="absolute -top-2 -right-2 text-6xl text-primary-100 dark:text-primary-900/30 font-serif leading-none select-none">
                        ❝
                    </span>
                    
                    {/* Verse Text */}
                    <p 
                        className="text-xl md:text-2xl leading-loose text-text-primary dark:text-text-dark-primary text-center font-quran px-4 py-4"
                        style={{ fontFamily: "'Amiri', 'Scheherazade New', serif" }}
                        dir="rtl"
                    >
                        {aya.text}
                    </p>
                    
                    <span className="absolute -bottom-4 -left-2 text-6xl text-primary-100 dark:text-primary-900/30 font-serif leading-none select-none rotate-180">
                        ❝
                    </span>
                </div>
                
                {/* Action Link */}
                <div className="mt-6 pt-4 border-t border-surface-200 dark:border-dark-300 flex justify-center">
                    <Link 
                        href={`/app/quran/chapter/${aya.chapter_id}?verse=${aya.verse_number}`}
                        className="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 flex items-center gap-2 transition-colors"
                    >
                        <span>اقرأ في السورة</span>
                        <ChevronLeft className="w-4 h-4" />
                    </Link>
                </div>
            </CardContent>
        </Card>
    );
}
