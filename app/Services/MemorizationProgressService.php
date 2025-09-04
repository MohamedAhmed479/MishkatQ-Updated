<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Api\V1\MemorizationProgressController;
use App\Models\Chapter;
use App\Models\MemorizationPlan;
use App\Models\MemorizationProgress;
use App\Models\PlanItem;
use App\Models\ReviewRecord;
use App\Repositories\Eloquent\MemorizationProgressRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemorizationProgressService
{
    public function __construct(
        protected MemorizationProgressRepository $memorizationProgressRepository,
    )
    {
    }

    public function updateMemorizationProgress(PlanItem $planItem): void
    {
        $memoData = [
            "chapter_id" => $planItem->quran_surah_id,
            "verses_memorized" => ($planItem->verse_end_id - $planItem->verse_start_id) + 1,
        ];

        $this->updateProgress($memoData);
    }

    private function updateProgress(array $memoData): void
    {
        $userId = Auth::id();
        $chapter = Chapter::findOr($memoData["chapter_id"], function () {
            return ApiResponse::notFound("السورة غير موجودة");
        });

        $progress = $this->memorizationProgressRepository->findOrCreateProgress($userId, $chapter);
        $progress->verses_memorized += $memoData["verses_memorized"];

        if($progress->total_verses > $progress->verses_memorized) {
            $progress->status = "in_progress";
        } elseif ($progress->total_verses === $progress->verses_memorized) {
            $progress->status = "completed";
        } elseif ($progress->verses_memorized === 0) {
            $progress->status = "not_started";
        }

        $this->memorizationProgressRepository->saveProgress($progress);
    }

    public function calculateProgressStats(MemorizationPlan $plan): array
    {
        $totalItems = $plan->planItems()->count();
        $completedItems = $plan->planItems()->where('is_completed', true)->count();

        $completionPercentage = $totalItems > 0
            ? number_format(($completedItems / $totalItems) * 100, 2, '.', '')
            : 0;

        $reviewStats = ReviewRecord::whereHas('spacedRepetition.planItem', function ($query) use ($plan) {
            $query->where('plan_id', $plan->id);
        })->select(
            DB::raw('COUNT(*) as total_reviews'),
            DB::raw('SUM(CASE WHEN successful THEN 1 ELSE 0 END) as successful_reviews'),
            DB::raw('AVG(performance_rating) as average_rating')
        )->first();

        return [
            'completion' => [
                'total_items' => $totalItems,
                'completed_items' => $completedItems,
                'percentage' => $completionPercentage,
            ],
            'reviews' => [
                'total' => $reviewStats->total_reviews ?? 0,
                'successful' => $reviewStats->successful_reviews ?? 0,
                'success_rate' => $reviewStats->total_reviews > 0
                    ? round(($reviewStats->successful_reviews / $reviewStats->total_reviews) * 100, 2)
                    : 0,
                'average_rating' => round($reviewStats->average_rating ?? 0, 2),
            ],
            'streak' => $this->calculateStreak($plan),
        ];
    }

    private function calculateStreak(MemorizationPlan $plan): array
    {
        $today = Carbon::today();
        $currentStreak = 0;
        $bestStreak = 0;
        $tempStreak = 0;

        $completedItems = $plan->planItems()
            ->where('is_completed', true)
            ->orderBy('target_date', 'desc')
            ->get();

        foreach ($completedItems as $item) {
            $itemDate = Carbon::parse($item->target_date);
            if ($itemDate->isSameDay($today->copy()->subDays($currentStreak))) {
                $currentStreak++;
                $tempStreak++;
            } else {
                break;
            }
        }

        $lastDate = null;
        foreach ($completedItems as $item) {
            $itemDate = Carbon::parse($item->target_date);
            if ($lastDate === null || $itemDate->diffInDays($lastDate) === 1) {
                $tempStreak++;
            } else {
                $bestStreak = max($bestStreak, $tempStreak);
                $tempStreak = 1;
            }
            $lastDate = $itemDate;
        }

        $bestStreak = max($bestStreak, $tempStreak);

        return [
            'current' => $currentStreak,
            'best' => $bestStreak,
        ];
    }

}
