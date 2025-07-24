<?php

namespace App\Repositories\Eloquent;

use App\Models\PlanItem;
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
}
