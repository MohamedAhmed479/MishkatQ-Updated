<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Record a notification for a user
     *
     * @param User $user
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @return DatabaseNotification
     */
    public function record(User $user, string $type, string $title, string $message, array $data = []): DatabaseNotification
    {
        return $user->notifications()->create([
            'id' => Str::uuid(),
            'type' => $type,
            'data' => array_merge([
                'title' => $title,
                'message' => $message
            ], $data)
        ]);
    }

    /**
     * Record badge achievement notification
     *
     * @param User $user
     * @param array $badge
     * @return DatabaseNotification
     */
    public function recordBadgeAchievement(User $user, array $badge): DatabaseNotification
    {
        return $this->record(
            $user,
            'App\Notifications\BadgeAchievement',
            '🏅 شارة جديدة!',
            " تهانينا! لقد حصلت على شارة {$badge['name']}.",
            [
                'badge_id' => $badge['id'],
                'badge_name' => $badge['name'],
                'points_earned' => $badge['points_value']
            ]
        );
    }

    /**
     * Record points milestone notification
     *
     * @param User $user
     * @param int $points
     * @param int $milestone
     * @return DatabaseNotification
     */
    public function recordPointsMilestone(User $user, int $points, int $milestone): DatabaseNotification
    {
        return $this->record(
            $user,
            'App\Notifications\PointsMilestone',
            '🎯 إنجاز في النقاط!',
            " رائع! لقد وصلت إلى {$milestone}نقطة.",
            [
                'current_points' => $points,
                'milestone' => $milestone
            ]
        );
    }

    /**
     * Record rank change notification
     *
     * @param User $user
     * @param int $oldRank
     * @param int $newRank
     * @return DatabaseNotification
     */
    public function recordRankChange(User $user, int $oldRank, int $newRank): DatabaseNotification
    {
        $improvement = $oldRank - $newRank;
        $message = $improvement > 0
            ? "أحسنت! تقدّمت {$improvement} مركزًا في قائمة المتصدرين! 🏆"
            : "لقد تراجعت {$improvement} مركزًا في قائمة المتصدرين.";

        return $this->record(
            $user,
            'App\Notifications\RankChange',
            '📊 تحديث في ترتيبك',
            $message,
            [
                'old_rank' => $oldRank,
                'new_rank' => $newRank,
                'change' => $improvement
            ]
        );
    }

    /**
     * Record streak achievement notification
     *
     * @param User $user
     * @param int $days
     * @return DatabaseNotification
     */
    public function recordStreakAchievement(User $user, int $days): DatabaseNotification
    {
        return $this->record(
            $user,
            'App\Notifications\StreakAchievement',
            '🔥 إنجاز الاستمرارية!',
            "مذهل! لقد حافظت على سلسلة استمرارية لمدة {$days} يومًا.",
            [
                'streak_days' => $days
            ]
        );
    }

    /**
     * Record daily reminder notification
     *
     * @param User $user
     * @return DatabaseNotification
     */
    public function recordDailyReminder(User $user): DatabaseNotification
    {
        return $this->record(
            $user,
            'App\Notifications\DailyReminder',
            '⏰ تذكير يومي',
            'لا تنسَ إنجاز أهدافك اليومية في الحفظ!',
            [
                'reminder_date' => Carbon::now()->toDateString()
            ]
        );
    }

    /**
     * Record review reminder notification
     *
     * @param User $user
     * @param int $pendingReviews
     * @return DatabaseNotification
     */
    public function recordReviewReminder(User $user, int $pendingReviews): DatabaseNotification
    {
        return $this->record(
            $user,
            'App\Notifications\ReviewReminder',
            '📚 وقت المراجعة!',
            "لديك {$pendingReviews} عنصرًا بحاجة إلى المراجعة.",
            [
                'pending_reviews' => $pendingReviews
            ]
        );
    }
}
