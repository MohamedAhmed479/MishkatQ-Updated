<?php

namespace App\Repositories\Interfaces;

use App\Models\MemorizationPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
interface MemorizationPlanInterface
{
    // public function getAllForSpecificUser(User $user): Collection;

    public function find(int $planId): ?MemorizationPlan;

    public function findActivePlanForUser(int $userId): ?MemorizationPlan;

    public function findPlanForUser(int $userId, int $planId): ?MemorizationPlan;

    public function isUserHasActivePlan(int $userId): bool;

    public function makeActivePlan(Array $planData): MemorizationPlan;

    /**
     * Update a specific memorization plan.
     *
     * @param int $planId
     * @param array $data
     * @return bool
     */
    public function update(int $planId, array $data): bool;

    public function getLastTargetDate(int $memoPlan): ?Carbon;

    public function avtivePlan(int $planId): bool;

    public function pausePlan(int $planId): bool;


}
