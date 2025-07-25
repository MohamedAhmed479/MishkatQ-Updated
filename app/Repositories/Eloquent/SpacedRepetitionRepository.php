<?php

namespace App\Repositories\Eloquent;

use App\Models\PlanItem;
use App\Models\ReviewRecord;
use App\Models\SpacedRepetition;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SpacedRepetitionRepository implements SpacedRepetitionInterface
{
    public $model;

    public function __construct(SpacedRepetition $model)
    {
        $this->model = $model;
    }

    public function create(Array $data): SpacedRepetition
    {
        return $this->model->create($data);
    }

    public function getTodayRevisionsForUser(int $userId): ?Collection
    {
        $today = Carbon::today();

        return $this->model->whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereDate('scheduled_date', $today)
            ->with(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd'])
            ->orderBy('scheduled_date')
            ->get();
    }

    public function find(int $revisionId): ?SpacedRepetition
    {
        return $this->model->find($revisionId);
    }

    public function getLastUncompletedRevisionsForUser(int $userId): ?Collection
    {
        $today = Carbon::today();

        return $this->model->whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereDate('scheduled_date', '<', $today)
            ->whereNull('last_reviewed_at')
            ->with(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd'])
            ->orderBy('scheduled_date')
            ->get();
    }

    public function update(int $revisionId, array $data): bool
    {
        return $this->find($revisionId)->update($data);
    }

    public function getMaxIntervalIndex(int $planItemId): int
    {
        return $this->model->where('plan_item_id', $planItemId)->max('interval_index');
    }

    public function perfectReviewsCount(int $revisionId): int
    {
        $revision = $this->find($revisionId);
        return ReviewRecord::whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($revision) {
            $query->where('user_id', $revision->planItem->memorizationPlan->user_id);
        })
            ->where('successful', true)
            ->where('performance_rating', '>=', 4.5)
            ->count();
    }

    public function userCanEditRevision(int $userId, int $revisionId): bool
    {
        $revision = $this->find($revisionId);

        return $revision->planItem->memorizationPlan->user_id === $userId;
    }
}
