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

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function markEmailAsVerified(User $user): bool
    {
        return $user->markEmailAsVerified();
    }

    public function updateTotalPoints(User $user, int $newPoints): void
    {
        // total_points is stored in user_profiles table, not users table
        if ($user->profile) {
            $user->profile->update(['total_points' => $newPoints]);
        } else {
            // Create profile if it doesn't exist
            UserProfile::create([
                'user_id' => $user->id,
                'username' => $user->name ?? 'user_' . $user->id,
                'total_points' => $newPoints,
                'verses_memorized_count' => 0,
            ]);
        }
    }

    public function getUsersWithHigherPoints(int $points): int
    {
        return UserProfile::where('total_points', '>', $points)->count();
    }

    public function update(int $id, array $data)
    {
        $user = $this->findById($id);
        if (!$user) {
            return null;
        }
        $user->update($data);
        return $user;
    }

    public function findByProvider(string $provider, string $providerId)
    {
        return User::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }
}
