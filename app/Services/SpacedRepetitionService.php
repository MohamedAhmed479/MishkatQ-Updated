<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Http\Resources\PlanItemResource;
use App\Models\PlanItem;
use App\Models\Verse;
use App\Repositories\Eloquent\SpacedRepetitionRepository;
use App\Repositories\Interfaces\PlanItemInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Traits\AyaTafsirTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SpacedRepetitionService
{
    use AyaTafsirTrait;

    public $spacedRepetitionRepository;
    public $planItemRepository;
    protected $incentiveService;

    public function __construct(
        SpacedRepetitionInterface $spacedRepetitionRepository,
        PlanItemInterface $planItemRepository,
        IncentiveService $incentiveService
    )
    {
        $this->spacedRepetitionRepository = $spacedRepetitionRepository;
        $this->planItemRepository = $planItemRepository;
        $this->incentiveService = $incentiveService;
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

    public function postponeRevision(int $revisionId, Carbon $postponeDate)
    {
        $revision = $this->spacedRepetitionRepository->find($revisionId);

        if (! $revision) return ApiResponse::notFound("لم يتم العثور علي هذه المراجعة");


        if (! $this->spacedRepetitionRepository->userCanEditRevision(Auth::id(), $revisionId)) {
            return ApiResponse::unauthorized("غير مسموح لهذا المستخدم بالقيام بهذه العملية");
        }

        $this->spacedRepetitionRepository->update($revision->id, [
            'scheduled_date' => $postponeDate,
        ]);

        $data = [
            'repetition' => [
                'id' => $revision->id,
                'scheduled_date' => $postponeDate->format('Y-m-d')
            ]
        ];

        return ApiResponse::success($data, 'تم تأجيل المراجعة بنجاح');
    }


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
    public function todayRevisions(int $userId): JsonResponse
    {
        $todayRevisions = $this->spacedRepetitionRepository->getTodayRevisionsForUser($userId);

        if($todayRevisions->isEmpty()){
            return ApiResponse::success([], "لا يوجد اي مراجعات اليوم");
        }

        return ApiResponse::success($todayRevisions, "تم استرجاع مراجعات اليوم بنجاح");
    }

    public function lastUncompletedRevisions(int $userId): JsonResponse
    {
        $lastUncompletedRevisions = $this->spacedRepetitionRepository->getLastUncompletedRevisionsForUser($userId);
        if($lastUncompletedRevisions->isEmpty()){
            return ApiResponse::success([], "لايوجد اي مراجعات فائته غير مكتملة");
        }

        return ApiResponse::success($lastUncompletedRevisions, "تم جلب جميع المراجعات الفائته والغير مكتملة");
    }

    public function getRevisionContent(int $revisionId): JsonResponse
    {
        $revision = $this->spacedRepetitionRepository->find($revisionId);
        if(! $revision) return ApiResponse::notFound("لم يتم العثور علي المراجعة");

        $planItem = $revision->planItem;

        $preferredTafsirId = Auth::user()->preference->tafsir_id;

        // Get all verses between start and end
        $verses = Verse::where('chapter_id', $planItem->quran_surah_id)
            ->where('verse_number', '>=', $planItem->verseStart->verse_number)
            ->where('verse_number', '<=', $planItem->verseEnd->verse_number)
            ->with(['words', 'recitations.reciter'])
            ->get();


        return ApiResponse::success([
            'plan_item' => new PlanItemResource($planItem, $verses, $preferredTafsirId),
        ], "تم جلب بيانات المراجعة");
    }

    public function getTodayReviewsForUser(int $userId): ?Collection
    {
        return $this->spacedRepetitionRepository->getTodayRevisionsForUser($userId);
    }
}
