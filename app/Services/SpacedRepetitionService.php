<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\PlanItem;
use App\Repositories\Eloquent\SpacedRepetitionRepository;
use App\Repositories\Interfaces\PlanItemInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
class SpacedRepetitionService
{
    public $spacedRepetitionRepository;
    public $planItemRepository;
    // protected $rewardService;

    public function __construct(SpacedRepetitionInterface $spacedRepetitionRepository, PlanItemInterface $planItemRepository)
    {
        $this->spacedRepetitionRepository = $spacedRepetitionRepository;
        $this->planItemRepository = $planItemRepository;
        // $this->rewardService = $rewardService;
    }


    /**
     * Define default review intervals in days
     * @var array
     */
    protected $defaultIntervals = [
        1,      // First review after 1 day
        3,      // Second review after 3 days
        7,      // Third review after 7 days
        14,     // Fourth review after 14 days
        30,     // Fifth review after 30 days
        60,     // Sixth review after 60 days
        90      //Seventh review after 90 days
    ];

    /**
     * Default Ease Factor
     * @var float
     */
    protected $defaultEaseFactor = 2.5;

    /**
     * Create a revision table for a saved section
     *
     * @param int $planItemId
     */
    public function generateRevisionScheduleForPlanItem(int $planItemId)
    {
        $planItem = $this->planItemRepository->find($planItemId);
        $targetDate = Carbon::parse($planItem->target_date);

        foreach ($this->defaultIntervals as $index => $days) {
            $scheduledDate = (clone $targetDate)->addDays($days);

            $this->spacedRepetitionRepository->create([
                'plan_item_id' => $planItem->id,
                'interval_index' => $index + 1,
                'scheduled_date' => $scheduledDate,
                'ease_factor' => $this->defaultEaseFactor,
                'repetition_count' => 0,
                'last_reviewed_at' => null
            ]);
        }
    }


    /**
     * Get today's user revisions
     *
     * @param int $userId
     * @return Collection
     */
    public function getTodayRevisionsForUser(int $userId): ?Collection
    {
        return $this->spacedRepetitionRepository->getTodayRevisionsForUser($userId);
    }

}
