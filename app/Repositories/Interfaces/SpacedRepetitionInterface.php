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

}
