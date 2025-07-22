<?php

namespace App\Repositories\Eloquent;

use App\Models\MemorizationPlan;
use App\Models\User;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MemorizationPlanRepository implements MemorizationPlanInterface
{
    protected $model;
    public function __construct(MemorizationPlan $model) {
        $this->model = $model;
    }

    public function isUserHasActivePlan(int $userId): bool
    {
        return User::find($userId)->memorizationPlans()->where("status", "active")->exists();
    }

    public function makeActivePlan(array $planData): MemorizationPlan
    {
        return $this->model->create($planData);
    }

    public function update(int $planId, array $data): bool
    {
        $plan = $this->find($planId);

        if (!$plan) {
            return false;
        }

        return $plan->update($data);
    }

    public function getLastTargetDate(int $memoPlanId): ?Carbon
    {
        $target_date = $this->find($memoPlanId)->planItems()->latest("target_date")->first()->target_date;

        return Carbon::parse($target_date);
    }

    public function find(int $planId): ?MemorizationPlan
    {
        return $this->model->find($planId);
    }

    public function findPlanForUser(int $userId, int $planId): ?MemorizationPlan
    {
        return User::find($userId)->memorizationPlans()->find($planId);
    }

    public function avtivePlan(int $planId): bool
    {
        $plan = $this->find($planId);

        if (!$plan) {
            return false;
        }

        return $plan->update(["status" => "active"]);

    }

    public function pausePlan(int $planId): bool
    {
        $plan = $this->find($planId);

        if (!$plan) {
            return false;
        }

        return $plan->update(["status" => "paused"]);
    }


    public function findActivePlanForUser(int $userId): ?MemorizationPlan
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();
    }
}
