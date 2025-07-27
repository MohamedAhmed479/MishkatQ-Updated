<?php

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface BadgeInterface
{
    public function getActiveBadges(): Collection;

    public function getUserBadges(int $userId): Collection;

    public function getUserBadgesWithAwardDate(int $userId): Collection;

    public function getUnawardedActiveBadges(int $userId): Collection;
}
