<?php

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface LeaderboardInterface
{
    public function updateOrCreateEntry(array $criteria, array $data): void;

    public function getLeaderboard(string $periodType, int $limit): Collection;
}
