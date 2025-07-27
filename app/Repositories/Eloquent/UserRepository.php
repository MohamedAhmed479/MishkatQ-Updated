<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Interfaces\UserInterface;

class UserRepository implements UserInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function updateTotalPoints(User $user, int $newPoints): void
    {
        $user->update(['total_points' => $newPoints]);
    }

    public function getUsersWithHigherPoints(int $points): int
    {
        return UserProfile::where('total_points', '>', $points)->count();
    }
}
