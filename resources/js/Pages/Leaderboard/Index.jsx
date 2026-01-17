import { Head, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Trophy, Medal, Crown, User } from 'lucide-react';
import MainLayout from '@/Layouts/MainLayout';
import Card, { CardContent, CardHeader } from '@/Components/UI/Card';

const periods = [
    { value: 'daily', label: 'اليوم' },
    { value: 'weekly', label: 'الأسبوع' },
    { value: 'monthly', label: 'الشهر' },
    { value: 'yearly', label: 'السنة' },
];

export default function LeaderboardIndex({ leaderboard, userRank, period }) {
    const handlePeriodChange = (newPeriod) => {
        router.get('/app/leaderboard', { period: newPeriod }, { preserveState: true });
    };

    return (
        <MainLayout title="لوحة الصدارة">
            <Head title="لوحة الصدارة" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-text-primary dark:text-text-dark-primary">
                        لوحة الصدارة
                    </h1>
                    <p className="text-text-muted dark:text-text-dark-muted mt-1">
                        تنافس مع الآخرين واحصل على المركز الأول
                    </p>
                </div>

                {/* Period Tabs */}
                <div className="flex gap-2 overflow-x-auto pb-2">
                    {periods.map((p) => (
                        <button
                            key={p.value}
                            onClick={() => handlePeriodChange(p.value)}
                            className={`
                                px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap
                                ${period === p.value
                                    ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/25'
                                    : 'bg-surface-200 dark:bg-dark-300 text-text-secondary dark:text-text-dark-secondary hover:bg-surface-300 dark:hover:bg-dark-200'
                                }
                            `}
                        >
                            {p.label}
                        </button>
                    ))}
                </div>

                {/* User Rank Card */}
                {userRank && (
                    <Card gradient className="p-6">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-4">
                                <div className="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                                    <User className="w-7 h-7" />
                                </div>
                                <div>
                                    <p className="text-white/80 text-sm">مركزك الحالي</p>
                                    <p className="text-3xl font-bold">#{userRank.rank}</p>
                                </div>
                            </div>
                            <div className="text-left">
                                <p className="text-white/80 text-sm">نقاطك</p>
                                <p className="text-2xl font-bold">{userRank.total_points}</p>
                            </div>
                        </div>
                    </Card>
                )}

                {/* Top 3 Podium */}
                {leaderboard.length >= 3 && (
                    <div className="flex items-end justify-center gap-4 py-8">
                        {/* 2nd Place */}
                        <PodiumCard
                            rank={2}
                            name={leaderboard[1]?.user_name}
                            points={leaderboard[1]?.total_points}
                            isCurrentUser={leaderboard[1]?.is_current_user}
                        />

                        {/* 1st Place */}
                        <PodiumCard
                            rank={1}
                            name={leaderboard[0]?.user_name}
                            points={leaderboard[0]?.total_points}
                            isCurrentUser={leaderboard[0]?.is_current_user}
                        />

                        {/* 3rd Place */}
                        <PodiumCard
                            rank={3}
                            name={leaderboard[2]?.user_name}
                            points={leaderboard[2]?.total_points}
                            isCurrentUser={leaderboard[2]?.is_current_user}
                        />
                    </div>
                )}

                {/* Full Leaderboard */}
                <Card>
                    <CardHeader>
                        <h2 className="font-bold text-text-primary dark:text-text-dark-primary">
                            الترتيب الكامل
                        </h2>
                    </CardHeader>
                    <CardContent>
                        {leaderboard.length > 0 ? (
                            <div className="divide-y divide-surface-200 dark:divide-dark-300">
                                {leaderboard.slice(3).map((entry, index) => (
                                    <LeaderboardRow
                                        key={entry.user_id}
                                        rank={entry.rank}
                                        name={entry.user_name}
                                        points={entry.total_points}
                                        isCurrentUser={entry.is_current_user}
                                    />
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <div className="w-16 h-16 mx-auto mb-4 bg-surface-200 dark:bg-dark-300 rounded-full flex items-center justify-center">
                                    <Trophy className="w-8 h-8 text-text-muted" />
                                </div>
                                <p className="text-text-muted dark:text-text-dark-muted">
                                    لا توجد بيانات لهذه الفترة
                                </p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
}

function PodiumCard({ rank, name, points, isCurrentUser }) {
    const heights = { 1: 'h-32', 2: 'h-24', 3: 'h-20' };
    const colors = {
        1: 'from-accent-400 to-accent-600',
        2: 'from-gray-300 to-gray-400',
        3: 'from-amber-600 to-amber-700'
    };
    const icons = {
        1: <Crown className="w-6 h-6" />,
        2: <Medal className="w-5 h-5" />,
        3: <Medal className="w-5 h-5" />
    };

    return (
        <motion.div
            initial={{ y: 50, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: rank * 0.1 }}
            className="text-center"
        >
            <div className={`
                w-16 h-16 mx-auto mb-2 rounded-full flex items-center justify-center
                ${isCurrentUser
                    ? 'ring-4 ring-primary-400 ring-offset-2 dark:ring-offset-dark-500'
                    : ''
                }
                bg-gradient-to-br ${colors[rank]}
            `}>
                <span className="text-white font-bold">{icons[rank]}</span>
            </div>
            <p className={`font-medium text-sm mb-1 ${isCurrentUser ? 'text-primary-600 dark:text-primary-400' : 'text-text-primary dark:text-text-dark-primary'}`}>
                {name}
            </p>
            <p className="text-xs text-text-muted dark:text-text-dark-muted mb-2">
                {points} نقطة
            </p>
            <div className={`
                w-20 mx-auto rounded-t-xl bg-gradient-to-br ${colors[rank]} ${heights[rank]}
                flex items-center justify-center
            `}>
                <span className="text-white text-2xl font-bold">{rank}</span>
            </div>
        </motion.div>
    );
}

function LeaderboardRow({ rank, name, points, isCurrentUser }) {
    return (
        <motion.div
            initial={{ x: 20, opacity: 0 }}
            animate={{ x: 0, opacity: 1 }}
            className={`
                flex items-center gap-4 py-3
                ${isCurrentUser ? 'bg-primary-50 dark:bg-primary-900/20 -mx-4 px-4 rounded-xl' : ''}
            `}
        >
            <span className="w-8 text-center font-bold text-text-muted dark:text-text-dark-muted">
                {rank}
            </span>
            <div className="w-10 h-10 rounded-full bg-surface-200 dark:bg-dark-300 flex items-center justify-center">
                <User className="w-5 h-5 text-text-muted" />
            </div>
            <div className="flex-1">
                <p className={`font-medium ${isCurrentUser ? 'text-primary-600 dark:text-primary-400' : 'text-text-primary dark:text-text-dark-primary'}`}>
                    {name}
                    {isCurrentUser && <span className="text-xs mr-2">(أنت)</span>}
                </p>
            </div>
            <span className="font-bold text-primary-600 dark:text-primary-400">
                {points}
            </span>
        </motion.div>
    );
}
