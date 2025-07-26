<?php

namespace App\Repositories\Eloquent;

use App\Models\ReviewRecord;
use App\Models\SpacedRepetition;
use App\Models\User;
use App\Repositories\Interfaces\RevisionReviewsInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Class RevisionReviewsRepository implements RevisionReviewsInterface
{
    public function __construct(
        protected ReviewRecord $model,
    )
    {
    }

    public function find(int $revisionId): ?ReviewRecord
    {
        return $this->model->find($revisionId);
    }

    public function hasAReview(int $revisionId): bool
    {
        return $this->model->where("spaced_repetition_id", $revisionId)->exists();
    }

    public function getAReview(int $revisionId): ?ReviewRecord
    {
        return $this->model->where("spaced_repetition_id", $revisionId)->first();
    }

    public function createOrUpdate(int $revisionId, array $data): ?ReviewRecord
    {
        $reviewData = [
            'review_date' => now(),
            'performance_rating' => $data['performance_rating'],
            'successful' => $data['performance_rating'] >= 3,
            'notes' => $data['notes'] ?? null,
        ];

        if ($this->hasAReview($revisionId)) {
            $reviewRecord = $this->getAReview($revisionId);
            $reviewRecord->update($reviewData);
        } else {
            $reviewData['spaced_repetition_id'] = $revisionId;
            $reviewRecord = ReviewRecord::create($reviewData);
        }

        return $reviewRecord->refresh();
    }

    public function getCompletedRevisionsCount(int $memorizationPlanId): int
    {
        return $this->model
            ->whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($memorizationPlanId) {
                $query->where('id', $memorizationPlanId);
            })
            ->count();
    }

    public function getSuccessfulRevisionsCount(int $memorizationPlanId): int
    {
        return $this->model
            ->where('successful', true)
            ->whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($memorizationPlanId) {
                $query->where('id', $memorizationPlanId);
            })
            ->count();
    }

    public function getAverageRating(int $memorizationPlanId): ?float
    {
        return $this->model
            ->whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($memorizationPlanId) {
                $query->where('id', $memorizationPlanId);
            })
            ->avg('performance_rating');
    }



}
