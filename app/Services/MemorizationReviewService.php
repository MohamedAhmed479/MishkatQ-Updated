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
    /**
     * Define default review intervals in days
     * @var array
     */
    protected $defaultIntervals = [
        1,      // First review after 1 day
        3,      // Second review after 3 days
        7,      // Third review after 7 days
        14,     // Fourth review after 14 days
        30,     // Fifth review after 30 days
        60,     // Sixth review after 60 days
        90      //Seventh review after 90 days
    ];

    /**
     * Default Ease Factor
     * @var float
     */
    protected $defaultEaseFactor = 2.5;

    public function __construct(
        protected PlanItemInterface $planItemRepository,
        protected SpacedRepetitionInterface $spacedRepetitionRepository,
        protected RevisionReviewsInterface $revisionReviewsRepository,
        protected MemorizationPlanInterface $memorizationPlanRepository
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
     *
     * Validates ownership of the revision, records the review data, and returns both the review result
     * and upcoming scheduled revisions for the same plan item.
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

        $data =  [
            'review_record' => [
                'id' => $reviewRecord->id,
                'performance_rating' => $reviewRecord->performance_rating,
                'performance_description' => $reviewRecord->getPerformanceDescription(),
                'review_date' => $reviewRecord->review_date->format('Y-m-d H:i:s'),
                'successful' => $reviewRecord->successful
            ],
            'next_reviews' => SpacedRepetition::where('plan_item_id', $revision->plan_item_id)
                ->whereNull('last_reviewed_at')
                ->orderBy('scheduled_date')
                ->get()
                ->map(function ($rep) {
                    return [
                        'id' => $rep->id,
                        'scheduled_date' => $rep->scheduled_date->format('Y-m-d'),
                        'interval_index' => $rep->interval_index
                    ];
                })
        ];

        return ApiResponse::success($data, "تمت تسجيل تقييم المراجعة بنجاح");
    }

    /**
     * Record a review and update the table for future reviews.
     *
     * @param SpacedRepetition $revision
     * @param array $validatedData
     * @return ReviewRecord
     */
    protected function recordReview(SpacedRepetition $revision, array $validatedData): ReviewRecord
    {
        $reviewRecord = $this->revisionReviewsRepository->createOrUpdate($revision->id, $validatedData);


        $this->spacedRepetitionRepository->update($revision->id, [
            'repetition_count' => $revision->repetition_count + 1,
            'last_reviewed_at' => now()
        ]);


        // Reschedule the next review
        $this->rescheduleNextReview($revision, $validatedData["performance_rating"]);

        // Award points for completing the review
        // $this->rewardService->awardReviewPoints($revision->planItem->memorizationPlan->user, $reviewRecord);

        // Check for perfect reviews streak
        $perfectReviewsCount = $this->spacedRepetitionRepository->perfectReviewsCount($revision->id);

//        if ($perfectReviewsCount > 0) {
//            $this->rewardService->awardPerfectReviewPoints(
//                $revision->planItem->memorizationPlan->user,
//                $perfectReviewsCount
//            );
//        }

        return $reviewRecord;
    }

    /**
     * Reschedule the next review based on the rating
     *
     * @param SpacedRepetition $revision
     * @param int $performanceRating
     * @return SpacedRepetition
     */
    protected function rescheduleNextReview(SpacedRepetition $revision, int $performanceRating): SpacedRepetition
    {
        $planItem = $revision->planItem;
        $currentEaseFactor = $revision->ease_factor;

        // تحديد معامل السهولة الجديد بناءً على التقييم
        $newEaseFactor = match (true) {
            $performanceRating >= 4 => $currentEaseFactor + 0.1,
            $performanceRating <= 2 => max(1.3, $currentEaseFactor - 0.2),
            default => $currentEaseFactor
        };

        // حساب الفاصل الزمني التالي
        $nextInterval = $this->calculateNextInterval($revision, $performanceRating, $newEaseFactor);

        // تحديث المراجعة الحالية
        $this->spacedRepetitionRepository->update($revision->id, [
            'ease_factor' => $newEaseFactor
        ]);

        // مراجعة ضعيفة: إعادة مراجعة بعد يوم واحد
        if ($performanceRating <= 2) {
            $this->scheduleNextReview($planItem, $revision->interval_index, 1, $newEaseFactor);
        }

        // نهاية الجدول مع تقييم جيد: الانتقال للمراجعة التالية
        $maxIntervalIndex = $this->spacedRepetitionRepository->getMaxIntervalIndex($planItem->id);
        if ($revision->interval_index >= $maxIntervalIndex && $performanceRating >= 3) {
            $this->scheduleNextReview($planItem, $revision->interval_index + 1, $nextInterval, $newEaseFactor);
        }

        return $revision->refresh();
    }

    /**
     * Schedule a new review
     */
    protected function scheduleNextReview(PlanItem $planItem, int $intervalIndex, int $days, float $easeFactor): void
    {
        $this->spacedRepetitionRepository->create([
            'plan_item_id' => $planItem->id,
            'interval_index' => $intervalIndex,
            'scheduled_date' => Carbon::now()->addDays($days),
            'ease_factor' => $easeFactor,
            'repetition_count' => 0,
            'last_reviewed_at' => null,
        ]);
    }


    /**
     * Calculates the next time interval based on the rating and ease factor.
     *
     * @param SpacedRepetition $repetition
     * @param int $performanceRating
     * @param float $easeFactor
     * @return int
     */
    protected function calculateNextInterval(SpacedRepetition $repetition, int $performanceRating, float $easeFactor)
    {
        if ($performanceRating <= 2) {
            // إذا كان التقييم منخفضاً، نعيد الجدولة بعد يوم أو يومين
            return $performanceRating + 1; // 0->1, 1->2, 2->3
        } elseif ($performanceRating == 3) {
            // إذا كان التقييم متوسطاً، نستخدم نفس الفترة
            $currentInterval = $this->getCurrentInterval($repetition);
            return $currentInterval;
        } else {
            // إذا كان التقييم عالياً، نزيد الفترة بناءً على معامل السهولة
            $currentInterval = $this->getCurrentInterval($repetition);
            return ceil($currentInterval * $easeFactor);
        }
    }

    /**
     * Get the current time period
     *
     * @param SpacedRepetition $revision
     * @return int
     */
    protected function getCurrentInterval(SpacedRepetition $revision): int
    {
        $index = $revision->interval_index - 1;
        return $index >= 0 && $index < count($this->defaultIntervals)
            ? $this->defaultIntervals[$index]
            : end($this->defaultIntervals);
    }
}
