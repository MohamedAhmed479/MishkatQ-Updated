<?php

namespace App\Repositories\Interfaces;

use App\Models\SpacedRepetition;
use Illuminate\Support\Collection;

interface SpacedRepetitionInterface
{
    public function create(Array $data): SpacedRepetition;

    public function getTodayRevisionsForUser(int $userId): ?Collection;

    public function getLastUncompletedRevisionsForUser(int $userId): ?Collection;

    public function find(int $revisionId): ?SpacedRepetition;

    public function update(int $revisionId, Array $data): bool;

    public function getMaxIntervalIndex(int $planItemId): int;

    public function perfectReviewsCount(int $revisionId): int;

    public function userCanEditRevision(int $userId, int $revisionId): bool;
}
