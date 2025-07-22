<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\Tafsir;
use App\Models\Verse;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\PlanItemInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PlanItemService
{
    public $memorizationPlanRepository;
    protected $planItemRepository;

    protected $spacedRepetitionService;

    /**
     * Constructor to initialize the service with required repositories.
     *
     * @param MemorizationPlanInterface $memorizationPlanRepository  Repository for managing memorization plans.
     * @param PlanItemInterface $planItemRepository  Repository for managing daily plan items.
     */
    public function __construct(
        MemorizationPlanInterface $memorizationPlanRepository,
        PlanItemInterface $planItemRepository,
        SpacedRepetitionService $spacedRepetitionService
    )
    {
        $this->memorizationPlanRepository = $memorizationPlanRepository;
        $this->planItemRepository = $planItemRepository;
        $this->spacedRepetitionService = $spacedRepetitionService;
    }

    public function handleMarkAsCompleted(int $planItemId): JsonResponse
    {
        $user = Auth::user();

        if(! $this->planItemRepository->userCanEditPlanItem($user->id, $planItemId)) {
            return ApiResponse::unauthorized();
        }

        $activePlan = $user->actvePlan();
        if (!$activePlan) {
            return ApiResponse::notFound('لم يتم العثور على خطة حفظ نشطة.');
        }

        $planItem = $this->planItemRepository->getValidPlanItem($planItemId, $activePlan->id);
        if (!$planItem) {
            return ApiResponse::error(
                'لم يتم العثور على عنصر صالح لوضع علامة عليه كمكتمل.'
            );
        }

        if ($this->planItemRepository->hasPastIncompleteItems($activePlan->id, $planItem->id)) {
            return ApiResponse::error(
                'يرجى إكمال العناصر غير المكتملة السابقة قبل المتابعة.'
            );
        }

        if(! $this->planItemRepository->markAsComplete($planItem->id)) {
            return ApiResponse::error("حدث خطأ ما");
        }

        $this->spacedRepetitionService->generateRevisionScheduleForPlanItem($planItem->id);

        // Update the user's overall memorization progress statistics
        // $this->updateMemorizationProgress($item);

        // Award points for completing the memorization
        // $this->rewardService->awardMemorizationPoints($user, $item);

        // Calculate and return updated progress metrics
        // $updatedProgress = $this->calculateProgressStats($activePlan);

        return ApiResponse::success([
            // 'updated_progress' => $updatedProgress
        ], 'تم وضع علامة على العنصر على أنه مكتمل بنجاح.');
    }


    /**
     * Update the user's memorization progress
     */
    private function updateMemorizationProgress(PlanItem $item): void
    {
        $memo_data = [
            "chapter_id" => $item->quran_surah_id,
            "verses_memorized" => ($item->verse_end_id - $item->verse_start_id) + 1,
        ];

        MemorizationProgressController::updateProgress($memo_data);
    }

    public function getAllContentToday(): JsonResponse
    {
        $user = Auth::user();
        $tafsirId = $user->preference->tafsir_id;

        if (!$this->memorizationPlanRepository->isUserHasActivePlan($user->id)) {
            return ApiResponse::error('لم يتم العثور على خطة حفظ نشطة.', 404);
        }

        $activePlan = $this->memorizationPlanRepository->findActivePlanForUser($user->id);

        $todayItems = $this->planItemRepository->getTodayPlanItems($activePlan->id);


        // Calculate progress statistics
        // $progressStats = $this->calculateProgressStats($activePlan);

        // Format the response data
        $responseData = [
            'plan' => [
                'id' => $activePlan->id,
                'name' => $activePlan->name,
                'start_date' => $activePlan->start_date,
                'end_date' => $activePlan->end_date,
            ],
            // 'progress' => $progressStats,
            'today_content' => $todayItems->map(function ($item) use ($tafsirId) {
                // Get all verses between start and end
                $verses = Verse::where('chapter_id', $item->quranSurah->id)
                    ->where('verse_number', '>=', $item->verseStart->verse_number)
                    ->where('verse_number', '<=', $item->verseEnd->verse_number)
                    ->with(['words', 'recitations.reciter'])
                    ->get();

                return [
                    'id' => $item->id,
                    'surah' => [
                        'id' => $item->quranSurah->id,
                        'name_ar' => $item->quranSurah->name_ar,
                        'name_en' => $item->quranSurah->name_en,
                    ],
                    'verses' => $verses->map(function ($verse) use ($item, $tafsirId) {
                        return [
                            'number' => $verse->verse_number,
                            'text_uthmani' => $verse->text_uthmani,
                            'text_imlaei' => $verse->text_imlaei,
                            'tafsir' => $this->getAyaTafsir($tafsirId, $item->quranSurah->id, $verse->verse_number),
                            'words' => $verse->words->map(function ($word) {
                                return [
                                    'position' => $word->position,
                                    'text' => $word->text,
                                ];
                            }),
                            'recitations' => $verse->recitations->map(function ($recitation) {
                                return [
                                    'reciter_name' => $recitation->reciter->reciter_name,
                                    'audio_url' => "https://verses.quran.foundation/" . $recitation->audio_url,
                                ];
                            }),
                        ];
                    }),
                    'target_date' => $item->target_date,
                    'sequence' => $item->sequence,
                ];
            }),
        ];

        return ApiResponse::success($responseData, 'Daily memorization content retrieved successfully.');
    }

    /**
     * Get tafsir (interpretation) for a specific verse
     *
     * @param int|null $tafsir_id The ID of the tafsir source
     * @param int $chapter The chapter (surah) ID
     * @param int $aya_number The verse number
     * @return string|null The tafsir text or null if not available
     */
    private function getAyaTafsir($tafsirId, $chapter, $aya_number)
    {
        // If no tafsir_id is provided, return null
        if (!$tafsirId) {
            return null;
        }

        try {
            $api_url = "api.quran-tafseer.com/tafseer";
            $endpoint = "$tafsirId/$chapter/$aya_number";

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->baseUrl('http://' . $api_url)
                ->get($endpoint);

            if (!$response->successful()) {
                // Log the error but don't throw exception to prevent breaking the app
                \Illuminate\Support\Facades\Log::error("Tafsir API error: HTTP " . $response->status() . " for tafsir_id: $tafsirId, chapter: $chapter, verse: $aya_number");
                return null;
            }

            $data = $response->json();

            // Check if the expected data structure exists
            if (isset($data['text']) && !empty($data['text'])) {
                return $data['text'];
            } else {
                \Illuminate\Support\Facades\Log::warning("Tafsir API returned unexpected data structure for tafsir_id: $tafsir_id, chapter: $chapter, verse: $aya_number");
                return null;
            }
        } catch (\Exception $e) {
            // Log the exception but return null to prevent breaking the app
            \Illuminate\Support\Facades\Log::error("Error fetching tafsir: " . $e->getMessage());
            return null;
        }
    }

}
