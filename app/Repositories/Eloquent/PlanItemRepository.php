<?php

namespace App\Repositories\Eloquent;

use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\User;
use App\Repositories\Interfaces\PlanItemInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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

    public function getTodayPlanItems(int $memoPlanId): ?Collection
    {
        $memoPlan = MemorizationPlan::find($memoPlanId);

        // Get today's plan items with all necessary relations
        return PlanItem::where('plan_id', $memoPlanId)
            ->whereDate('target_date', Carbon::today())
            ->where('is_completed', false)
            ->with([
                'quranSurah',
                'verseStart.words',
                'verseStart.recitations.reciter',
                'verseEnd.words',
                'verseEnd.recitations.reciter'
            ])
            ->get();
    }

    public function find(int $planItemId): ?PlanItem
    {
        return $this->model->find($planItemId);
    }

    public function userCanEditPlanItem(int $userId, int $planItemId): bool
    {
        $planItem = $this->find($planItemId);
        $memoPlan = $planItem->memorizationPlan;

        if($memoPlan->user_id === $userId){
            return true;
        }
        return false;
    }

    public function getValidPlanItem(int $planItemId, int $planId): ?PlanItem
    {
        return $this->model->where('id', $planItemId)
            ->where('plan_id', $planId)
            ->whereDate('target_date', "<=", Carbon::today())
            ->where('is_completed', false)
            ->first();
    }

    public function hasPastIncompleteItems(int $planId, int $exceptItemId): bool
    {
        return $this->model->where('plan_id', $planId)
            ->where('id', '!=', $exceptItemId)
            ->where('is_completed', false)
            ->whereDate('target_date', '<', Carbon::today())
            ->exists();
    }

    public function markAsComplete(int $planItemId): bool
    {
        return $this->find($planItemId)->update(['is_completed' => true]);
    }

    public function todayItem(int $userId): ?PlanItem
    {
        $activePlan = MemorizationPlan::select("id")
            ->where('user_id', $userId)
            ->where("status", "active")
            ->first();

        if (! $activePlan) {
            return null;
        }

        return $this->model
            ->where("plan_id", $activePlan->id)
            ->whereDate('target_date', Carbon::today())
            ->first();
    }

    public function getDetailedUserPlanItem(int $planItemId, int $userId): ?PlanItem
    {
        return $this->model->whereHas('memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with([
                'quranSurah',
                'verseStart.words',
                'verseStart.recitations.reciter',
                'verseEnd.words',
                'verseEnd.recitations.reciter'
            ])
            ->find($planItemId);
    }

    public function isExistsForUser(int $planItemId, int $userId): bool
    {
        $planItem = $this->find($planItemId);
        if (! $planItem) return false;

        return $planItem->memorizationPlan->user_id === $userId;
    }



}
