<?php

namespace App\Repositories\Eloquent;

use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\User;
use App\Repositories\Interfaces\PlanItemInterface;
use Illuminate\Database\Eloquent\Collection;

class PlanItemRepository implements PlanItemInterface
{
    protected $model;

    public function __construct(PlanItem $model) {
        $this->model = $model;
    }

    public function storePlanItem(Array $planItemData): void
    {
        $this->model->create($planItemData);
    }

    public function storeAndReturnPlanItem(array $planItemData): PlanItem
    {
        return $this->model->create($planItemData);
    }

    public function getPlanItems(int $memoPlanId)
    {
        $memoPlan = MemorizationPlan::find($memoPlanId);

        return $memoPlan->planItems()
            ->orderBy('sequence')
            ->with(['verseStart.chapter', 'verseEnd.chapter']);
    }


}
