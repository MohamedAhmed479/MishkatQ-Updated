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

}
