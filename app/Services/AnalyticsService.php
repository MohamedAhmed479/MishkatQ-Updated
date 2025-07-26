<?php

namespace App\Services;

use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\ReviewRecord;
use App\Models\SpacedRepetition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get overall progress analytics for a user
     *
     * @param User $user
     * @return array
     */
    public function getUserProgressAnalytics(User $user): array
    {
        $activePlan = $user->memorizationPlans()->where('status', 'active')->first();

        if (!$activePlan) {
            return [
                'success' => false,
                'message' => 'لم يتم العثور على خطة نشطة'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'overall_progress' => $this->calculateOverallProgress($activePlan),
                'performance_metrics' => $this->getPerformanceMetrics($activePlan),
                'consistency_metrics' => $this->getConsistencyMetrics($activePlan),
                'recent_activity' => $this->getRecentActivity($activePlan),
                'surah_wise_progress' => $this->getSurahWiseProgress($activePlan),
            ]
        ];
    }

    /**
     * Calculate overall progress percentage
     *
     * @param MemorizationPlan $plan
     * @return array
     */
    private function calculateOverallProgress(MemorizationPlan $plan): array
    {
        $totalItems = $plan->planItems()->count();
        $completedItems = $plan->planItems()->where('is_completed', true)->count();

        $progressPercentage = $totalItems > 0 ? ($completedItems / $totalItems) * 100 : 0;

        return [
            'total_items' => $totalItems,
            'completed_items' => $completedItems,
            'progress_percentage' => round($progressPercentage, 2),
            'remaining_items' => $totalItems - $completedItems
        ];
    }

    /**
     * Get performance metrics
     *
     * @param MemorizationPlan $plan
     * @return array
     */
    private function getPerformanceMetrics(MemorizationPlan $plan): array
    {
        $reviews = ReviewRecord::whereHas('spacedRepetition', function ($query) use ($plan) {
            $query->whereHas('planItem', function ($q) use ($plan) {
                $q->where('plan_id', $plan->id);
            });
        })->get();

        $totalReviews = $reviews->count();
        $successfulReviews = $reviews->where('successful', true)->count();

        return [
            'total_reviews' => $totalReviews,
            'successful_reviews' => $successfulReviews,
            'success_rate' => $totalReviews > 0 ? round(($successfulReviews / $totalReviews) * 100, 2) : 0,
            'average_rating' => $totalReviews > 0 ? round($reviews->avg('performance_rating'), 2) : 0,
            'recent_trend' => $this->calculateRecentTrend($reviews)
        ];
    }

    /**
     * Get consistency metrics
     *
     * @param MemorizationPlan $plan
     * @return array
     */
    private function getConsistencyMetrics(MemorizationPlan $plan): array
    {
        $last30Days = Carbon::now()->subDays(30);

        $dailyCompletions = PlanItem::where('plan_id', $plan->id)
            ->where('is_completed', true)
            ->where('target_date', '>=', $last30Days)
            ->select(DB::raw('DATE(target_date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $currentStreak = $this->calculateCurrentStreak($dailyCompletions);
        $bestStreak = $this->calculateBestStreak($dailyCompletions);

        return [
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'completion_rate' => $this->calculateCompletionRate($dailyCompletions),
            'average_daily_completions' => $this->calculateAverageDailyCompletions($dailyCompletions)
        ];
    }

    /**
     * Get recent activity
     *
     * @param MemorizationPlan $plan
     * @return array
     */
    private function getRecentActivity(MemorizationPlan $plan): array
    {
        $last7Days = Carbon::now()->subDays(7);

        return [
            'recent_revisions' => $this->getRecentRevisions($plan, $last7Days),
            'upcoming_revisions' => $this->getUpcomingRevisions($plan),
            'recent_completions' => $this->getRecentCompletions($plan, $last7Days)
        ];
    }

    /**
     * Get surah-wise progress
     *
     * @param MemorizationPlan $plan
     * @return array
     */
    private function getSurahWiseProgress(MemorizationPlan $plan): array
    {
        $surahProgress = PlanItem::where('plan_id', $plan->id)
            ->select('quran_surah_id',
                DB::raw('count(*) as total_items'),
                DB::raw('sum(case when is_completed = 1 then 1 else 0 end) as completed_items'))
            ->groupBy('quran_surah_id')
            ->get();

        return $surahProgress->map(function ($item) {
            return [
                'surah_id' => $item->quran_surah_id,
                'total_items' => $item->total_items,
                'completed_items' => $item->completed_items,
                'progress_percentage' => round(($item->completed_items / $item->total_items) * 100, 2)
            ];
        })->toArray();
    }

    /**
     * Calculate recent trend
     *
     * @param Collection $reviews
     * @return array
     */
    private function calculateRecentTrend(Collection $reviews): array
    {
        $last7Days = Carbon::now()->subDays(7);
        $recentReviews = $reviews->where('created_at', '>=', $last7Days);

        $dailySuccessRates = $recentReviews->groupBy(function ($review) {
            return $review->created_at->format('Y-m-d');
        })->map(function ($dayReviews) {
            $successful = $dayReviews->where('successful', true)->count();
            return $dayReviews->count() > 0 ? number_format(($successful / $dayReviews->count()) * 100, 2, '.', '') : 0;
        });

        return [
            'daily_success_rates' => $dailySuccessRates->toArray(),
            'trend_direction' => $this->calculateTrendDirection($dailySuccessRates)
        ];
    }

    /**
     * Calculate trend direction
     *
     * @param Collection $dailyRates
     * @return string
     */
    private function calculateTrendDirection(Collection $dailyRates): string
    {
        if ($dailyRates->count() < 2) {
            return 'حيادي';
        }

        $values = $dailyRates->values()->toArray();
        $firstHalf = array_slice($values, 0, ceil(count($values) / 2));
        $secondHalf = array_slice($values, ceil(count($values) / 2));

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        if ($secondAvg > $firstAvg + 5) {
            return 'تحسين';
        } elseif ($secondAvg < $firstAvg - 5) {
            return 'انحدار';
        }

        return 'مستقر';
    }

    /**
     * Calculate current streak
     *
     * @param Collection $dailyCompletions
     * @return int
     */
    private function calculateCurrentStreak(Collection $dailyCompletions): int
    {
        $streak = 0;
        $currentDate = Carbon::now();

        while (true) {
            $dateStr = $currentDate->format('Y-m-d');
            $hasCompletion = $dailyCompletions->contains('date', $dateStr);

            if (!$hasCompletion) {
                break;
            }

            $streak++;
            $currentDate->subDay();
        }

        return $streak;
    }

    /**
     * Calculate best streak
     *
     * @param Collection $dailyCompletions
     * @return int
     */
    private function calculateBestStreak(Collection $dailyCompletions): int
    {
        $bestStreak = 0;
        $currentStreak = 0;
        $dates = $dailyCompletions->pluck('date')->sort()->values();

        for ($i = 0; $i < $dates->count(); $i++) {
            if ($i > 0) {
                $prevDate = Carbon::parse($dates[$i - 1]);
                $currentDate = Carbon::parse($dates[$i]);

                if ($currentDate->diffInDays($prevDate) === 1) {
                    $currentStreak++;
                } else {
                    $currentStreak = 1;
                }
            } else {
                $currentStreak = 1;
            }

            $bestStreak = max($bestStreak, $currentStreak);
        }

        return $bestStreak;
    }

    /**
     * Calculate completion rate
     *
     * @param Collection $dailyCompletions
     * @return float
     */
    private function calculateCompletionRate(Collection $dailyCompletions): float
    {
        $totalDays = 30;
        $daysWithCompletions = $dailyCompletions->count();

        return round(($daysWithCompletions / $totalDays) * 100, 2);
    }

    /**
     * Calculate average daily completions
     *
     * @param Collection $dailyCompletions
     * @return float
     */
    private function calculateAverageDailyCompletions(Collection $dailyCompletions): float
    {
        if ($dailyCompletions->isEmpty()) {
            return 0;
        }

        return round($dailyCompletions->avg('count'), 2);
    }

    /**
     * Get recent revisions
     *
     * @param MemorizationPlan $plan
     * @param Carbon $since
     * @return array
     */
    private function getRecentRevisions(MemorizationPlan $plan, Carbon $since): array
    {
        return ReviewRecord::whereHas('spacedRepetition', function ($query) use ($plan) {
            $query->whereHas('planItem', function ($q) use ($plan) {
                $q->where('plan_id', $plan->id);
            });
        })
            ->where('created_at', '>=', $since)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'date' => $review->created_at->format('Y-m-d H:i:s'),
                    'successful' => $review->successful,
                    'performance_rating' => $review->performance_rating
                ];
            })
            ->toArray();
    }

    /**
     * Get upcoming revisions
     *
     * @param MemorizationPlan $plan
     * @return array
     */
    private function getUpcomingRevisions(MemorizationPlan $plan): array
    {
        return SpacedRepetition::with('planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd') // ✅ تحميل العلاقات كلها
        ->whereHas('planItem', function ($query) use ($plan) {
            $query->where('plan_id', $plan->id);
        })
            ->where('scheduled_date', '>=', Carbon::now())
            ->whereNull('last_reviewed_at')
            ->orderBy('scheduled_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($repetition) {
                return [
                    'id' => $repetition->id,
                    'scheduled_date' => $repetition->scheduled_date->format('Y-m-d H:i:s'),
                    'plan_item' => optional($repetition->planItem)->getDescription()
                ];
            })
            ->toArray();
    }


    /**
     * Get recent completions
     *
     * @param MemorizationPlan $plan
     * @param Carbon $since
     * @return array
     */
    private function getRecentCompletions(MemorizationPlan $plan, Carbon $since): array
    {
        return PlanItem::with(['quranSurah', 'verseStart', 'verseEnd']) // ✅ هنا الحل
        ->where('plan_id', $plan->id)
            ->where('is_completed', true)
            ->where('target_date', '>=', $since)
            ->orderBy('target_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'date' => $item->target_date->format('Y-m-d H:i:s'),
                    'description' => $item->getDescription()
                ];
            })
            ->toArray();
    }
}
