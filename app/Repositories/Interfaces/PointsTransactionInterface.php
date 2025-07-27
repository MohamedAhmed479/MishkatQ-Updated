<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;

interface PointsTransactionInterface
{
    public function createTransaction(User $user, array $data): void;

    public function getUserTransactions(int $userId): Collection;

    public function getRecentUserTransactions(int $userId, int $limit = 5): Collection;

    public function getUsersTotalPointsBetweenDates(string $periodType): Collection;
}
