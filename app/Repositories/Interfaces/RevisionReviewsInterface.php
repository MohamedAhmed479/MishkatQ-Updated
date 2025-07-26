<?php

namespace App\Repositories\Interfaces;

use App\Models\ReviewRecord;
use App\Models\SpacedRepetition;

interface RevisionReviewsInterface
{
    public function hasAReview(int $revisionId): bool;

    public function createOrUpdate(int $revisionId, Array $data): ?ReviewRecord;

//    public function find(int $revisionId): ?ReviewRecord;

    public function getAReview(int $revisionId): ?ReviewRecord;

    public function getCompletedRevisionsCount(int $memorizationPlanId): int;

    public function getSuccessfulRevisionsCount(int $memorizationPlanId): int;


    public function getAverageRating(int $memorizationPlanId): ?float;

}
