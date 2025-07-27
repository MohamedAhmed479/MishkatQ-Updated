<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\Badge;
use App\Models\MemorizationProgress;
use App\Models\PlanItem;
use App\Models\ReviewRecord;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Repositories\Interfaces\BadgeInterface;
use App\Repositories\Interfaces\PointsTransactionInterface;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\Interfaces\LeaderboardInterface;


class IncentiveService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly BadgeInterface $badgeRepo,
        private readonly PointsTransactionInterface $pointsRepo,
        private readonly UserInterface $userRepo,
        private readonly LeaderboardInterface $leaderboardRepo,
    ) {}

    public function activeBadges(): JsonResponse
    {
        $activeBadges = $this->badgeRepo->getActiveBadges();

        if ($activeBadges->isEmpty()) {
            return ApiResponse::success([], "لم يتم العثور على شارات نشطة");
        }

        return ApiResponse::success($activeBadges, "تم العثور على شارات نشطة");
    }

    public function userBadges(int $userId): JsonResponse
    {
        $user = User::find($userId);
        if (! $user) return ApiResponse::notFound("المستخدم غير موجود");

        $badges = $this->badgeRepo->getUserBadges($userId);

        return ApiResponse::success($badges, "تم جلب شارات المستخدم");
    }

    public function pointsTransactions(int $userId): JsonResponse
    {
        $user = User::find($userId);
        if (! $user) return ApiResponse::notFound("المستخدم غير موجود");


        $transactions = $user->pointsTransactions()
            ->latest('created_at')
            ->get();

        return ApiResponse::success($transactions);
    }

    public function leaderboard(string $periodType = 'monthly', int $limit = 10): JsonResponse
    {
        $leaderboard = $this->leaderboardRepo->getLeaderboard($periodType, $limit);
        return ApiResponse::success($leaderboard);
    }

    public function userStats(int $userId): JsonResponse
    {
        $user = User::find($userId);
        if (! $user) return ApiResponse::notFound("المستخدم غير موجود");

        $stats = [
            'total_points' => $user->getTotalPoints(),
            'current_rank' => $user->getCurrentRank(),
            'badges_count' => $user->badges()->count(),
            'recent_activity' => $user->pointsTransactions()
                ->latest('created_at')
                ->limit(5)
                ->get(),
        ];

        return ApiResponse::success($stats);

    }

    // -----------------------------------------
    /**
     * Award points for completing a memorization item
     */
    public function awardMemorizationPoints(User $user, PlanItem $item): void
    {
        $versesCount = ($item->verse_end_id - $item->verse_start_id) + 1;

        // Base points for memorization (10 points per verse)
        $basePoints = $versesCount * 10;

        // Bonus points for completing a chapter
        $chapterProgress = MemorizationProgress::where('user_id', $user->id)
            ->where('chapter_id', $item->quran_surah_id)
            ->first();

        if ($chapterProgress && $chapterProgress->status === "completed") {
            $basePoints += 50; // Bonus for completing a chapter
        }

        $this->awardPoints(
            $user,
            $basePoints,
            'memorization_completed',
            " حفظ {$versesCount} الايات من {$item->quranSurah->name_ar}",
            $item
        );

        // Check for badges after awarding points
        $this->checkAndAwardBadges($user);
    }

    public function awardPoints(User $user, int $points, string $type, string $description, $transactionable = null): void
    {
        $oldPoints = $user->profile->total_points;
        $newPoints = $oldPoints + $points;

        $this->pointsRepo->createTransaction($user, [
            'points' => $points,
            'activity_type' => $type,
            'description' => $description,
            'transactionable_type' => $transactionable ? get_class($transactionable) : null,
            'transactionable_id' => $transactionable?->id
        ]);

        $this->userRepo->updateTotalPoints($user, $newPoints);

        $this->checkPointsMilestones($user, $oldPoints, $newPoints);
    }

    public function checkAndAwardBadges(User $user): Collection
    {
        $awardedBadges = collect();
        $unawardedBadges = $this->badgeRepo->getUnawardedActiveBadges($user->id);

        $unawardedBadges->each(function (Badge $badge) use ($user, $awardedBadges) {
            if ($this->checkBadgeCriteria($user, $badge)) {
                $user->badges()->attach($badge->id, ['awarded_at' => now()]);
                $awardedBadges->push($badge);

                $this->awardPoints(
                    $user,
                    $badge->points_value,
                    'badge_earned',
                    " حصل على شارة: {$badge->name}",
                    $badge
                );
            }
        });

        return $awardedBadges;
    }

    private function checkPointsMilestones(User $user, int $oldPoints, int $newPoints): void
    {
        $milestones = [100, 500, 1000, 2500, 5000, 10000];

        foreach ($milestones as $milestone) {
            if ($oldPoints < $milestone && $newPoints >= $milestone) {
                $this->notificationService->recordPointsMilestone($user, $newPoints, $milestone);
            }
        }
    }

    /**
     * Award points for completing a review
     */
    public function awardReviewPoints(User $user, ReviewRecord $review): void
    {
        $basePoints = 0;

        // Base points for completing a review
        $basePoints += 5;

        // Bonus points for successful review
        if ($review->successful) {
            $basePoints += 10;

            // Additional bonus based on performance rating
            if ($review->performance_rating >= 4.5) {
                $basePoints += 15; // Perfect review bonus
            } elseif ($review->performance_rating >= 3.5) {
                $basePoints += 10; // Good review bonus
            }
        }

        $this->awardPoints(
            $user,
            $basePoints,
            'review_completed',
            $review->successful
                ? " تم إكمال المراجعة الناجحة مع التصنيف {$review->performance_rating}"
                : " تم إكمال المراجعة مع التقييم {$review->performance_rating}",
            $review
        );

        // Check for badges after awarding points
        $this->checkAndAwardBadges($user);
    }

    protected function checkBadgeCriteria(User $user, Badge $badge): bool
    {
        $criteria = $badge->winning_criteria;

        return match ($criteria['type']) {
            'verses_memorized' => $user->getTotalMemorizedVerses() >= $criteria['threshold'],
            'consecutive_days' => $this->checkConsecutiveDays($user, $criteria['threshold']),
            'total_points' => $user->profile->total_points >= $criteria['threshold'],
            'perfect_reviews' => $this->checkPerfectReviews($user, $criteria['threshold']),
            default => false,
        };
    }

    protected function checkConsecutiveDays(User $user, int $threshold): bool
    {
        $lastActive = $user->last_active_at;
        if (!$lastActive) {
            return false;
        }

        $days = 1;
        $currentDate = $lastActive->copy()->startOfDay();

        while ($days < $threshold) {
            $previousDay = $currentDate->copy()->subDay();
            $hasActivity = $user->memorizationProgress()
                ->whereDate('created_at', $previousDay)
                ->exists();

            if (!$hasActivity) {
                break;
            }

            $days++;
            $currentDate = $previousDay;
        }

        return $days >= $threshold;
    }

    protected function checkPerfectReviews(User $user, int $threshold): bool
    {
        return $user->memorizationProgress()
                ->where('status', 'perfect')
                ->count() >= $threshold;
    }

    /**
     * Award points for perfect reviews
     */
    public function awardPerfectReviewPoints(User $user, int $perfectReviewsCount): void
    {
        // Award points for achieving perfect reviews
        $perfectReviewPoints = match(true) {
            $perfectReviewsCount >= 10 => 100, // 10 perfect reviews
            $perfectReviewsCount >= 5 => 50,   // 5 perfect reviews
            $perfectReviewsCount >= 3 => 20,   // 3 perfect reviews
            default => 0
        };

        if ($perfectReviewPoints > 0) {
            $this->awardPoints(
                $user,
                $perfectReviewPoints,
                'perfect_reviews_achieved',
                " تم تحقيق {$perfectReviewsCount} مراجعة مثالية ",
                null
            );

            // Check for badges after awarding points
            $this->checkAndAwardBadges($user);
        }
    }

    public function awardBadge(User $user, Badge $badge): void
    {
        $userBadges = $user->badges()->pluck('id');
        if (!$userBadges->contains($badge->id)) {
            $user->badges()->attach($badge->id, ['awarded_at' => now()]);

            $this->awardPoints(
                $user,
                $badge->points_value,
                'badge_award',
                "Awarded {$badge->name} badge",
                $badge
            );

            $this->notificationService->recordBadgeAchievement($user, [
                'id' => $badge->id,
                'name' => $badge->name,
                'points_value' => $badge->points_value
            ]);
        }
    }

    public function updateUserRank(User $user): void
    {
        $oldRank = $user->getCurrentRank();
        $higherUsersCount = $this->userRepo->getUsersWithHigherPoints($user->profile->total_points);
        $newRank = $higherUsersCount + 1;

        if ($oldRank !== $newRank) {
            $this->notificationService->recordRankChange($user, $oldRank, $newRank);
        }
    }

    public function recordStreakAchievement(User $user, int $days): void
    {
        $this->notificationService->recordStreakAchievement($user, $days);
    }

    public function updateLeaderboards(string $periodType = 'monthly'): void
    {
        $userPoints = $this->pointsRepo->getUsersTotalPointsBetweenDates($periodType);

        $now = now();
        $start = match ($periodType) {
            'daily' => $now->copy()->startOfDay(),
            'weekly' => $now->copy()->startOfWeek(),
            'monthly' => $now->copy()->startOfMonth(),
            'yearly' => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        $end = match ($periodType) {
            'daily' => $now->copy()->endOfDay(),
            'weekly' => $now->copy()->endOfWeek(),
            'monthly' => $now->copy()->endOfMonth(),
            'yearly' => $now->copy()->endOfYear(),
            default => $now->copy()->endOfMonth(),
        };

        $rank = 1;
        foreach ($userPoints as $userPoint) {
            $this->leaderboardRepo->updateOrCreateEntry([
                'user_id' => $userPoint->user_id,
                'period_type' => $periodType,
                'period_start' => $start,
                'period_end' => $end,
            ], [
                'total_points' => $userPoint->total_points,
                'rank' => $rank++,
            ]);
        }
    }

    /**
     * Award points for maintaining a streak
     */
    public function awardStreakPoints(User $user, int $streakDays): void
    {
        // Award points for maintaining a streak
        $streakPoints = match(true) {
            $streakDays >= 30 => 100, // Monthly streak
            $streakDays >= 7 => 50,   // Weekly streak
            $streakDays >= 3 => 20,   // 3-day streak
            default => 0
        };

        if ($streakPoints > 0) {
            $this->awardPoints(
                $user,
                $streakPoints,
                'streak_maintained',
                "Maintained a {$streakDays}-day streak",
                null
            );

            // Check for badges after awarding points
            $this->checkAndAwardBadges($user);
        }
    }
}
