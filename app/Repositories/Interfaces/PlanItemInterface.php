<?php

namespace App\Repositories\Interfaces;

use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface PlanItemInterface
{
    public function storeAndReturnPlanItem(Array $planItemData): PlanItem;

    public function storePlanItem(Array $planItemData): void;

    public function getPlanItems(int $memoPlanId);

    public function getTodayPlanItems(int $memoPlanId): ?Collection;

    public function userCanEditPlanItem(int $userId, int $planItemId): bool;

    public function find(int $planItemId): ?PlanItem;

    public function getValidPlanItem(int $planItemId, int $planId): ?PlanItem;

    public function hasPastIncompleteItems(int $planItemId, int $exceptItemId): bool;

    public function markAsComplete(int $planItemId): bool;

    public function todayItem(int $userId): ?PlanItem;

    public function getDetailedUserPlanItem(int $planItemId, int $userId): ?PlanItem;

    public function isExistsForUser(int $planItemId, int $userId): bool;


}
