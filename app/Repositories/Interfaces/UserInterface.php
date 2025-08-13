<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function markEmailAsVerified(User $user): bool;

    public function updateTotalPoints(User $user, int $newPoints): void;

    public function getUsersWithHigherPoints(int $points): int;
}
