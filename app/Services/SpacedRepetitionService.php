<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Http\Resources\PlanItemResource;
use App\Models\PlanItem;
use App\Models\SpacedRepetition;
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
    protected FSRSService $fsrsService;

    public function __construct(
        SpacedRepetitionInterface $spacedRepetitionRepository,
        PlanItemInterface $planItemRepository,
        IncentiveService $incentiveService,
        FSRSService $fsrsService
    )
    {
        $this->spacedRepetitionRepository = $spacedRepetitionRepository;
        $this->planItemRepository = $planItemRepository;
        $this->incentiveService = $incentiveService;
        $this->fsrsService = $fsrsService;
    }

    /**
     * Default initial stability for new items (in days)
     * This represents the expected time for memory to decay to 90%
     */
    protected float $defaultStability = 1.0;

    /**
     * Default initial difficulty (scale 1-10)
     */
    protected float $defaultDifficulty = 5.0;

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
     * Create initial revision entry for a saved section using FSRS algorithm.
     * Unlike the old fixed-interval approach, FSRS creates only ONE initial
     * review and dynamically schedules subsequent reviews based on performance.
     *
     * @param int $planItemId
     */
    public function generateRevisionScheduleForPlanItem(int $planItemId): void
    {
        $planItem = $this->planItemRepository->find($planItemId);
        $targetDate = Carbon::parse($planItem->target_date);

        // FSRS: Create only the first review scheduled for next day
        // Subsequent reviews will be dynamically scheduled based on performance
        $initialScheduledDate = (clone $targetDate)->addDay();

        $this->spacedRepetitionRepository->create([
            'plan_item_id' => $planItem->id,
            'interval_index' => 1,
            'scheduled_date' => $initialScheduledDate,
            'ease_factor' => $this->defaultDifficulty, // Using as initial difficulty
            'stability' => $this->defaultStability,
            'difficulty' => $this->defaultDifficulty,
            'repetition_count' => 0,
            'last_reviewed_at' => null
        ]);
    }

    /**
     * Get memory statistics for a user's memorized items
     */
    public function getMemoryStatistics(int $userId): array
    {
        $revisions = SpacedRepetition::whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with('reviewRecord') // Eager load to avoid lazy loading
            ->get();

        $items = $revisions->map(function ($revision) {
            $daysElapsed = $revision->last_reviewed_at 
                ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                : 0;

            $stability = $revision->stability ?? $this->defaultStability;
            $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);

            // reviewRecord is HasOne, so check if it exists and is not successful
            $failCount = ($revision->reviewRecord && !$revision->reviewRecord->successful) ? 1 : 0;

            return [
                'id' => $revision->id,
                'plan_item_id' => $revision->plan_item_id,
                'stability' => $stability,
                'difficulty' => $revision->difficulty ?? $this->defaultDifficulty,
                'retrievability' => $retrievability,
                'memory_state' => $this->fsrsService->getMemoryState($stability),
                'fail_count' => $failCount,
                'total_reviews' => $revision->repetition_count,
            ];
        })->toArray();

        return $this->fsrsService->calculateBulkStatistics($items);
    }

    /**
     * Get prioritized revisions sorted by urgency (lowest retrievability first)
     */
    public function getPrioritizedRevisions(int $userId): Collection
    {
        $revisions = SpacedRepetition::whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereNull('last_reviewed_at')
            ->orWhere('scheduled_date', '<=', Carbon::today())
            ->with(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd'])
            ->get();

        // Calculate retrievability for each and sort
        return $revisions->map(function ($revision) {
            $daysElapsed = $revision->last_reviewed_at 
                ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                : 0;

            $stability = $revision->stability ?? $this->defaultStability;
            $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);
            $daysOverdue = max(0, Carbon::today()->diffInDays($revision->scheduled_date, false));

            $revision->retrievability = $retrievability;
            $revision->memory_state = $this->fsrsService->getMemoryState($stability);
            $revision->memory_state_ar = $this->fsrsService->getMemoryStateArabic($stability);
            $revision->priority_score = $this->fsrsService->calculatePriorityScore(
                $retrievability, 
                $stability, 
                $daysOverdue
            );

            return $revision;
        })->sortByDesc('priority_score')->values();
    }

    /**
     * Get heatmap data for memory strength visualization
     */
    public function getMemoryHeatmap(int $userId): array
    {
        $revisions = SpacedRepetition::whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd'])
            ->get();

        $heatmapData = [];

        foreach ($revisions as $revision) {
            $surahId = $revision->planItem->quran_surah_id;
            $surahName = $revision->planItem->quranSurah->name_ar;

            $daysElapsed = $revision->last_reviewed_at 
                ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                : 0;

            $stability = $revision->stability ?? $this->defaultStability;
            $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);

            if (!isset($heatmapData[$surahId])) {
                $heatmapData[$surahId] = [
                    'surah_id' => $surahId,
                    'surah_name' => $surahName,
                    'total_items' => 0,
                    'total_retrievability' => 0,
                    'memory_states' => ['young' => 0, 'mature' => 0, 'mastered' => 0],
                ];
            }

            $heatmapData[$surahId]['total_items']++;
            $heatmapData[$surahId]['total_retrievability'] += $retrievability;
            $memoryState = $this->fsrsService->getMemoryState($stability);
            $heatmapData[$surahId]['memory_states'][$memoryState]++;
        }

        // Calculate average retrievability per surah
        foreach ($heatmapData as &$data) {
            $data['average_retrievability'] = $data['total_items'] > 0 
                ? round($data['total_retrievability'] / $data['total_items'], 4) 
                : 0;
            $data['strength_percentage'] = round($data['average_retrievability'] * 100, 1);
        }

        return array_values($heatmapData);
    }

    /**
     * Identify "leech" items that are consistently failed
     * A leech is an item where the user has failed multiple times
     */
    public function getLeeches(int $userId): Collection
    {
        return SpacedRepetition::whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd', 'reviewRecord'])
            ->get()
            ->filter(function ($revision) {
                // reviewRecord is HasOne - check if it exists and was not successful
                // For leech detection, we use low stability as a proxy for repeated failures
                $stability = $revision->stability ?? 1.0;
                $totalReviews = $revision->repetition_count;
                
                // Consider an item a leech if it has many reviews but still low stability
                if ($totalReviews >= 3 && $stability < 7) {
                    return true;
                }
                
                // Or if the last review was failed
                if ($revision->reviewRecord && !$revision->reviewRecord->successful) {
                    return $totalReviews >= 2;
                }

                return false;
            })
            ->values();
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
