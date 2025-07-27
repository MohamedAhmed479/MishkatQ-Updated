<?php

namespace App\Repositories\Eloquent;

use App\Models\Badge;
use App\Models\User;
use App\Repositories\Interfaces\BadgeInterface;
use Illuminate\Support\Collection;

class BadgeRepository implements BadgeInterface
{
    public function getActiveBadges(): Collection
    {
        return Badge::where('is_active', true)->get();
    }

    public function getUserBadges(int $userId): Collection
    {
        $user = User::find($userId);

        return $user->badges()
            ->withPivot('awarded_at')
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'points_value' => $badge->points_value,
                    'awarded_at' => $badge->pivot->awarded_at,
                ];
            });
    }

    public function getUserBadgesWithAwardDate(int $userId): Collection
    {
        return Badge::whereHas('users', fn($q) => $q->where('user_id', $userId))
            ->with(['users' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->select('badge_id', 'awarded_at');
            }])
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'points_value' => $badge->points_value,
                    'awarded_at' => $badge->users->first()?->pivot?->awarded_at,
                ];
            });
    }

    public function getUnawardedActiveBadges(int $userId): Collection
    {
        $user = User::with('badges')->find($userId);
        $awardedBadgeIds = $user->badges->pluck('id');

        return Badge::where('is_active', true)
            ->whereNotIn('id', $awardedBadgeIds)
            ->get();
    }
}
