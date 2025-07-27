<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Api\V1\MemorizationProgressController;
use App\Http\Resources\PlanItemResource;
use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\ReviewRecord;
use App\Models\Tafsir;
use App\Models\Verse;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\PlanItemInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Traits\AyaTafsirTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlanItemService
{
    use AyaTafsirTrait;

    public function __construct(
        public readonly MemorizationPlanInterface $memorizationPlanRepository,
        public readonly PlanItemInterface $planItemRepository,
        protected readonly SpacedRepetitionService $spacedRepetitionService,
        protected readonly MemorizationProgressService $memorizationProgressService,
        protected readonly IncentiveService $incentiveService
    )
    {
    }


    public function getPlanItemContent(int $planItemId): JsonResponse
    {
        $user = Auth::user();
        $tafsirId = $user->preference->tafsir_id;

        if (!$this->memorizationPlanRepository->isUserHasActivePlan($user->id)) {
            return ApiResponse::error('لم يتم العثور على خطة حفظ نشطة.', 404);
        }

        $planItem = $this->planItemRepository->getDetailedUserPlanItem($planItemId, $user->id);

        if (!$planItem) {
            return ApiResponse::error("لم يتم العثور على عنصر الخطة.", 404);
        }

        // Get all verses between start and end
        $verses = Verse::where('chapter_id', $planItem->quran_surah_id)
            ->where('verse_number', '>=', $planItem->verseStart->verse_number)
            ->where('verse_number', '<=', $planItem->verseEnd->verse_number)
            ->with(['words', 'recitations.reciter'])
            ->get();

        return ApiResponse::success([
            'plan_item' => new PlanItemResource($planItem, $verses, $tafsirId),
        ], 'تم استرجاع محتوى الحفظ اليومي بنجاح.');
    }

    public function handleMarkAsCompleted(int $planItemId): JsonResponse
    {
        $user = Auth::user();

        if(! $this->planItemRepository->userCanEditPlanItem($user->id, $planItemId)) {
            return ApiResponse::unauthorized();
        }

        $activePlan = $user->activePlan();
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
         $this->memorizationProgressService->updateMemorizationProgress($planItem);

        // Award points for completing the memorization
        $this->incentiveService->awardMemorizationPoints($user, $planItem);

        // Calculate and return updated progress metrics
         $updatedProgress = $this->memorizationProgressService->calculateProgressStats($activePlan);

        return ApiResponse::success([
            'updated_progress' => $updatedProgress
        ], 'تم وضع علامة على العنصر على أنه مكتمل بنجاح.');
    }

    public function getTodayItem(int $userId): JsonResponse
    {
        $todayItem = $this->planItemRepository->todayItem($userId);

        if($todayItem) return ApiResponse::success($todayItem, "تم جلب عنصر الحفظ لهذا اليوم");

        return ApiResponse::success([], "لايوجد اي عنصر للحفظ اليوم");
    }

    public function getschedulesRevisionsForPlanItem(int $planItemId): JsonResponse
    {
        if(! $this->planItemRepository->isExistsForUser($planItemId, Auth::id())) {
            return ApiResponse::notFound("لم يتم العثور علي عنصر الحفظ");
        }

        $planItem = $this->planItemRepository->find($planItemId);

        $repetitions = $planItem->spacedRepetitions()
            ->orderBy('scheduled_date')
            ->get();

        $data = [
            'plan_item' => $planItem,
            'repetitions' => $repetitions ?? [],
        ];

        if ($repetitions->isEmpty()) return ApiResponse::success($data, "لم يتم العثور علي اي مراجعات لعنصر الحفظ بعد");

        return ApiResponse::success($data, "تم جلب مراجعات عنصر الحفظ بنجاح");
    }

}
