<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Http\Requests\recordPerformanceRequest;
use App\Models\PlanItem;
use App\Models\ReviewRecord;
use App\Models\SpacedRepetition;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\PlanItemInterface;
use App\Repositories\Interfaces\RevisionReviewsInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MemorizationReviewService
{
    public function __construct(
        protected PlanItemInterface $planItemRepository,
        protected SpacedRepetitionInterface $spacedRepetitionRepository,
        protected RevisionReviewsInterface $revisionReviewsRepository,
        protected MemorizationPlanInterface $memorizationPlanRepository,
        protected IncentiveService $incentiveService,
        protected FSRSService $fsrsService,
    ) {

    }

    public function getUserReviewStatistics(int $userId, int $planId): JsonResponse
    {
        $plan = $this->memorizationPlanRepository->findPlanForUser($userId, $planId);
        if(!$plan) return ApiResponse::notFound("لم يتم العثور علي الخظه");


        $averageRating = $this->revisionReviewsRepository->getAverageRating($planId);

        $overdueReviews = $this->spacedRepetitionRepository->getOverdueRevisionsCount($planId);

        $successfulRevisions = $this->revisionReviewsRepository->getSuccessfulRevisionsCount($planId);

        $completedRevisions = $this->revisionReviewsRepository->getCompletedRevisionsCount($planId);

        $successRate = $completedRevisions > 0 ? ($successfulRevisions / $completedRevisions) * 100 : 0;

        return ApiResponse::success([
            'last_review_date' => $this->spacedRepetitionRepository->lastRevisionAt($planId)->diffForHumans(),
            'completed_reviews' => $completedRevisions,
            'successful_reviews' => $successfulRevisions,
            'average_rating' => round($averageRating, 2),
            'overdue_reviews' => $overdueReviews,
            'success_rate' => round($successRate, 2) . "%",
        ], "احصائيات الخطة");

    }

    /**
     * Record the performance of a user's revision attempt for a specific spaced repetition item.
     * Uses the FSRS algorithm to dynamically calculate the next review date based on memory model.
     *
     * @param array $validatedData The validated performance data (e.g., performance_rating)
     * @param int $revisionId The ID of the spaced repetition record to update
     * @return JsonResponse
     */
    public function recordPerformance(Array $validatedData, $revisionId): JsonResponse
    {
        $revision = $this->spacedRepetitionRepository->find($revisionId);

        if (! $revision) return ApiResponse::notFound("المراجعة غير موجودة", 404);

        if($revision->scheduled_date > Carbon::now()->toDateTimeString()) return ApiResponse::error("لم يحن موعد هذه المراجعة بعد");

        if(! $this->planItemRepository->userCanEditPlanItem(Auth::id(), $revision->plan_item_id)) {
            return ApiResponse::unauthorized("لا يُسمح بالوصول إلى هذه المراجعة");
        }

        $reviewRecord = $this->recordReview(
            $revision,
            $validatedData,
        );

        // Refresh revision to get updated FSRS data
        $revision->refresh();

        // Get FSRS memory state info
        $stability = $revision->stability ?? 1.0;
        $memoryState = $this->fsrsService->getMemoryStateArabic($stability);

        $data =  [
            'review_record' => [
                'id' => $reviewRecord->id,
                'performance_rating' => $reviewRecord->performance_rating,
                'performance_description' => $reviewRecord->getPerformanceDescription(),
                'review_date' => $reviewRecord->review_date->format('Y-m-d H:i:s'),
                'successful' => $reviewRecord->successful
            ],
            'fsrs_state' => [
                'stability' => round($stability, 2),
                'difficulty' => round($revision->difficulty ?? 5.0, 2),
                'memory_state' => $memoryState,
                'next_interval_days' => $this->fsrsService->calculateNextInterval($stability),
            ],
            'next_reviews' => SpacedRepetition::where('plan_item_id', $revision->plan_item_id)
                ->whereNull('last_reviewed_at')
                ->orderBy('scheduled_date')
                ->get()
                ->map(function ($rep) {
                    $stability = $rep->stability ?? 1.0;
                    return [
                        'id' => $rep->id,
                        'scheduled_date' => $rep->scheduled_date->format('Y-m-d'),
                        'interval_index' => $rep->interval_index,
                        'memory_state' => $this->fsrsService->getMemoryStateArabic($stability),
                    ];
                })
        ];

        return ApiResponse::success($data, "تمت تسجيل تقييم المراجعة بنجاح");
    }

    /**
     * Record a review and update memory state using FSRS algorithm.
     *
     * @param SpacedRepetition $revision
     * @param array $validatedData
     * @return ReviewRecord
     */
    protected function recordReview(SpacedRepetition $revision, array $validatedData): ReviewRecord
    {
        $reviewRecord = $this->revisionReviewsRepository->createOrUpdate($revision->id, $validatedData);

        // Process review using FSRS algorithm
        $fsrsResult = $this->fsrsService->processReview(
            $revision->stability,
            $revision->difficulty,
            $validatedData['performance_rating'],
            $revision->last_reviewed_at
        );

        // Update current revision with new FSRS state
        $this->spacedRepetitionRepository->update($revision->id, [
            'repetition_count' => $revision->repetition_count + 1,
            'last_reviewed_at' => now(),
            'stability' => $fsrsResult['stability'],
            'difficulty' => $fsrsResult['difficulty'],
            'ease_factor' => $fsrsResult['difficulty'], // Keep for backward compatibility
        ]);

        // Schedule next review based on FSRS calculation
        $this->scheduleNextReviewWithFSRS(
            $revision->planItem,
            $revision->interval_index + 1,
            $fsrsResult
        );

        // Award points for completing the review
        $this->incentiveService->awardReviewPoints($revision->planItem->memorizationPlan->user, $reviewRecord);

        // Check for perfect reviews streak
        $perfectReviewsCount = $this->spacedRepetitionRepository->perfectReviewsCount($revision->id);

        if ($perfectReviewsCount > 0) {
            $this->incentiveService->awardPerfectReviewPoints(
                $revision->planItem->memorizationPlan->user,
                $perfectReviewsCount
            );
        }

        // Award mastery badges if item reaches mastered state
        if ($fsrsResult['memory_state'] === 'mastered') {
            $this->checkAndAwardMasteryBadge($revision);
        }

        return $reviewRecord;
    }

    /**
     * Schedule next review using FSRS algorithm results.
     * Creates a new SpacedRepetition entry with optimally calculated date.
     */
    protected function scheduleNextReviewWithFSRS(PlanItem $planItem, int $intervalIndex, array $fsrsResult): void
    {
        $this->spacedRepetitionRepository->create([
            'plan_item_id' => $planItem->id,
            'interval_index' => $intervalIndex,
            'scheduled_date' => $fsrsResult['scheduled_date'],
            'ease_factor' => $fsrsResult['difficulty'],
            'stability' => $fsrsResult['stability'],
            'difficulty' => $fsrsResult['difficulty'],
            'repetition_count' => 0,
            'last_reviewed_at' => null,
        ]);
    }

    /**
     * Check if user has mastered an entire Surah and award badge
     */
    protected function checkAndAwardMasteryBadge(SpacedRepetition $revision): void
    {
        $planItem = $revision->planItem;
        $userId = $planItem->memorizationPlan->user_id;
        $surahId = $planItem->quran_surah_id;

        // Check if all items for this Surah are mastered
        $allMastered = SpacedRepetition::whereHas('planItem', function ($query) use ($userId, $surahId) {
            $query->where('quran_surah_id', $surahId)
                ->whereHas('memorizationPlan', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
        })
            ->get()
            ->every(function ($rep) {
                $stability = $rep->stability ?? 0;
                return $this->fsrsService->getMemoryState($stability) === 'mastered';
            });

        if ($allMastered) {
            Log::info("User {$userId} has mastered Surah {$surahId}");
            // The IncentiveService will handle badge awarding based on criteria
        }
    }

    /**
     * Get enhanced review statistics with FSRS memory metrics
     */
    public function getEnhancedReviewStatistics(int $userId, int $planId): JsonResponse
    {
        $plan = $this->memorizationPlanRepository->findPlanForUser($userId, $planId);
        if(!$plan) return ApiResponse::notFound("لم يتم العثور علي الخطة");

        $averageRating = $this->revisionReviewsRepository->getAverageRating($planId);
        $overdueReviews = $this->spacedRepetitionRepository->getOverdueRevisionsCount($planId);
        $successfulRevisions = $this->revisionReviewsRepository->getSuccessfulRevisionsCount($planId);
        $completedRevisions = $this->revisionReviewsRepository->getCompletedRevisionsCount($planId);
        $successRate = $completedRevisions > 0 ? ($successfulRevisions / $completedRevisions) * 100 : 0;

        // Get FSRS-based memory statistics
        $revisions = SpacedRepetition::whereHas('planItem.memorizationPlan', function ($query) use ($planId) {
            $query->where('id', $planId);
        })->get();

        $youngCount = 0;
        $matureCount = 0;
        $masteredCount = 0;
        $totalRetrievability = 0;

        foreach ($revisions as $revision) {
            $stability = $revision->stability ?? 1.0;
            $daysElapsed = $revision->last_reviewed_at 
                ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                : 0;
            
            $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);
            $totalRetrievability += $retrievability;

            $state = $this->fsrsService->getMemoryState($stability);
            match ($state) {
                'young' => $youngCount++,
                'mature' => $matureCount++,
                'mastered' => $masteredCount++,
            };
        }

        $itemCount = $revisions->count();
        $avgRetrievability = $itemCount > 0 ? $totalRetrievability / $itemCount : 0;

        return ApiResponse::success([
            'last_review_date' => $this->spacedRepetitionRepository->lastRevisionAt($planId)?->diffForHumans() ?? 'لا يوجد',
            'completed_reviews' => $completedRevisions,
            'successful_reviews' => $successfulRevisions,
            'average_rating' => round($averageRating, 2),
            'overdue_reviews' => $overdueReviews,
            'success_rate' => round($successRate, 2) . "%",
            'memory_health' => [
                'average_retrievability' => round($avgRetrievability * 100, 1) . "%",
                'young_items' => $youngCount,
                'mature_items' => $matureCount,
                'mastered_items' => $masteredCount,
                'total_items' => $itemCount,
            ],
        ], "احصائيات الخطة المتقدمة");
    }
}
