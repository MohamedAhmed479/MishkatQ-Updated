import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import { Award, Trophy, Flame, BookOpen, Star, Lock, Coins } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';

const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
        opacity: 1,
        transition: { staggerChildren: 0.05 }
    }
};

const itemVariants = {
    hidden: { opacity: 0, scale: 0.8 },
    visible: { opacity: 1, scale: 1 }
};

export default function AchievementsIndex({ badges, stats, pointsHistory }) {
    const earnedBadges = badges.filter(b => b.earned);
    const lockedBadges = badges.filter(b => !b.earned);

    return (
        <MainLayout title="الإنجازات">
            <Head title="الإنجازات والشارات" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        الإنجازات
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        شاراتك ونقاطك وإنجازاتك
                    </p>
                </div>

                {/* Stats Overview */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <StatCard
                        icon={Coins}
                        label="مجموع النقاط"
                        value={stats.total_points}
                        color="accent"
                    />
                    <StatCard
                        icon={Award}
                        label="الشارات المكتسبة"
                        value={`${stats.badges_count}/${stats.total_badges}`}
                        color="primary"
                    />
                    <StatCard
                        icon={BookOpen}
                        label="آية محفوظة"
                        value={stats.total_verses_memorized}
                        color="primary"
                    />
                    <StatCard
                        icon={Flame}
                        label="أيام متتالية"
                        value={stats.current_streak}
                        color="accent"
                    />
                </div>

                {/* Earned Badges */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-accent-100 dark:bg-accent-900/30 flex items-center justify-center">
                                <Trophy className="w-5 h-5 text-accent-600 dark:text-accent-400" />
                            </div>
                            <div>
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    شاراتي
                                </h2>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    {earnedBadges.length} شارة مكتسبة
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        {earnedBadges.length > 0 ? (
                            <motion.div
                                variants={containerVariants}
                                initial="hidden"
                                animate="visible"
                                className="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
                            >
                                {earnedBadges.map((badge) => (
                                    <BadgeCard key={badge.id} badge={badge} earned />
                                ))}
                            </motion.div>
                        ) : (
                            <div className="text-center py-8">
                                <div className="w-16 h-16 mx-auto mb-4 bg-surface-200 dark:bg-dark-300 rounded-full flex items-center justify-center">
                                    <Award className="w-8 h-8 text-text-muted" />
                                </div>
                                <p className="text-text-muted dark:text-text-dark-muted">
                                    لم تحصل على شارات بعد. واصل الحفظ!
                                </p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Locked Badges */}
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-surface-200 dark:bg-dark-300 flex items-center justify-center">
                                <Lock className="w-5 h-5 text-text-muted" />
                            </div>
                            <div>
                                <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                    شارات للفتح
                                </h2>
                                <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                    {lockedBadges.length} شارة متبقية
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <motion.div
                            variants={containerVariants}
                            initial="hidden"
                            animate="visible"
                            className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6"
                        >
                            {lockedBadges.map((badge) => (
                                <BadgeCard key={badge.id} badge={badge} earned={false} />
                            ))}
                        </motion.div>
                    </CardContent>
                </Card>

                {/* Points History */}
                {pointsHistory.length > 0 && (
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <Coins className="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div>
                                    <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                                        سجل النقاط
                                    </h2>
                                    <p className="text-sm text-text-muted dark:text-text-dark-muted">
                                        آخر المعاملات
                                    </p>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="divide-y divide-surface-200 dark:divide-dark-300">
                                {pointsHistory.map((tx) => (
                                    <div key={tx.id} className="flex items-center justify-between py-3">
                                        <div>
                                            <p className="font-medium text-text-primary dark:text-text-dark-primary">
                                                {tx.reason}
                                            </p>
                                            <p className="text-xs text-text-muted dark:text-text-dark-muted">
                                                {tx.date}
                                            </p>
                                        </div>
                                        <span className={`font-bold ${tx.points > 0 ? 'text-success' : 'text-error'}`}>
                                            {tx.points > 0 ? '+' : ''}{tx.points}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
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
                    <Icon className={`w-5 h-5 ${
                        color === 'primary'
                            ? 'text-primary-600 dark:text-primary-400'
                            : 'text-accent-600 dark:text-accent-400'
                    }`} />
                </div>
                <div>
                    <p className="text-xl font-bold text-text-primary dark:text-text-dark-primary">
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

function BadgeCard({ badge, earned }) {
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
            variants={itemVariants}
            whileHover={{ scale: earned ? 1.1 : 1.05 }}
            className="text-center cursor-pointer group"
        >
            <div className={`
                relative w-20 h-20 mx-auto mb-3 rounded-2xl flex items-center justify-center overflow-hidden
                transition-all duration-300
                ${earned
                    ? 'bg-gradient-to-br from-accent-400 to-accent-600 shadow-lg shadow-accent-500/25 hover:shadow-xl hover:shadow-accent-500/40 ring-2 ring-accent-500/20'
                    : 'bg-surface-200 dark:bg-dark-300 grayscale opacity-60 group-hover:opacity-75'
                }
            `}>
                {iconUrl && !imageError ? (
                    <img
                        src={iconUrl}
                        alt={badge.name}
                        className={`w-14 h-14 object-contain ${!earned ? 'opacity-70' : ''}`}
                        onError={() => setImageError(true)}
                    />
                ) : (
                    <div className="flex items-center justify-center text-4xl">
                        {displayIcon}
                    </div>
                )}
                {!earned && (
                    <div className="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-sm rounded-2xl">
                        <Lock className="w-6 h-6 text-white opacity-80" />
                    </div>
                )}
                {earned && (
                    <div className="absolute top-1 right-1">
                        <div className="w-4 h-4 bg-accent-500 rounded-full flex items-center justify-center">
                            <Star className="w-2.5 h-2.5 text-white fill-white" />
                        </div>
                    </div>
                )}
            </div>
            <p className={`text-sm font-bold mb-1 ${
                earned 
                    ? 'text-text-primary dark:text-text-dark-primary' 
                    : 'text-text-muted dark:text-text-dark-muted'
            }`}>
                {badge.name}
            </p>
            {badge.description && (
                <p className={`text-xs mb-2 line-clamp-2 h-8 ${
                    earned
                        ? 'text-text-muted dark:text-text-dark-muted'
                        : 'text-text-muted dark:text-text-dark-muted opacity-75'
                }`}>
                    {badge.description}
                </p>
            )}
            {earned && (
                <div className="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-accent-100 dark:bg-accent-900/30">
                    <Coins className="w-3 h-3 text-accent-600 dark:text-accent-400" />
                    <span className="text-xs text-accent-600 dark:text-accent-400 font-bold">
                        +{badge.points}
                    </span>
                </div>
            )}
            {!earned && badge.points > 0 && (
                <div className="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-surface-200 dark:bg-dark-300">
                    <span className="text-xs text-text-muted dark:text-text-dark-muted">
                        {badge.points} نقطة
                    </span>
                </div>
            )}
        </motion.div>
    );
}
