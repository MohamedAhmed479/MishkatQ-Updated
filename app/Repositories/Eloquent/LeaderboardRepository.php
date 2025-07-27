<?php

namespace App\Repositories\Eloquent;

use App\Models\Leaderboard;
use App\Repositories\Interfaces\LeaderboardInterface;
use Illuminate\Support\Collection;

class LeaderboardRepository implements LeaderboardInterface
{
    public function updateOrCreateEntry(array $criteria, array $data): void
    {
        Leaderboard::updateOrCreate($criteria, $data);
    }

    public function getLeaderboard(string $periodType, int $limit): Collection
    {
        $now = now();

        $start = match ($periodType) {
            'daily' => $now->copy()->startOfDay(),
            'weekly' => $now->copy()->startOfWeek(),
            'monthly' => $now->copy()->startOfMonth(),
            'yearly' => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        return Leaderboard::with(['user:id,name,email'])
            ->where('period_type', $periodType)
            ->where('period_start', '>=', $start->copy()->startOfDay())
            ->orderBy('rank')
            ->limit($limit)
            ->get();
    }
}
