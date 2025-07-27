<?php

namespace App\Repositories\Eloquent;

use App\Models\PointsTransaction;
use App\Models\User;
use App\Repositories\Interfaces\PointsTransactionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PointsTransactionRepository implements PointsTransactionInterface
{
    public function createTransaction(User $user, array $data): void
    {
        $user->pointsTransactions()->create($data);
    }

    public function getUserTransactions(int $userId): Collection
    {
        return PointsTransaction::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getRecentUserTransactions(int $userId, int $limit = 5): Collection
    {
        return PointsTransaction::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getUsersTotalPointsBetweenDates(string $periodType): Collection
    {
        $now = now();
        $start = match ($periodType) {
            'daily' => $now->copy()->startOfDay(),
            'weekly' => $now->copy()->startOfWeek(),
            'monthly' => $now->copy()->startOfMonth(),
            'yearly' => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        $end = match ($periodType) {
            'daily' => $now->copy()->endOfDay(),
            'weekly' => $now->copy()->endOfWeek(),
            'monthly' => $now->copy()->endOfMonth(),
            'yearly' => $now->copy()->endOfYear(),
            default => $now->copy()->endOfMonth(),
        };

        return PointsTransaction::whereBetween('created_at', [$start, $end])
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get();
    }
}
