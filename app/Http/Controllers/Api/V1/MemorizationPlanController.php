<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewPlanRequest;
use App\Services\MemorizationPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemorizationPlanController extends Controller
{
    protected $memorizationPlanService;

    public function __construct(MemorizationPlanService $memorizationPlanService)
    {
        $this->memorizationPlanService = $memorizationPlanService;
    }

    /**
     * Display a paginated list of the authenticated user's memorization plans.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $user_plans = Auth::user()->memorizationPlans()->paginate(5);

            if (count($user_plans) > 0) {
                return ApiResponse::withPagination($user_plans);
            }

            return ApiResponse::success(null, "ليس لديك خطط");
        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handles the creation of a new memorization plan for the authenticated user.
     *
     * Validates the incoming request, checks for the authenticated user,
     * and delegates the creation of the plan to the MemorizationPlanService.
     * The process is wrapped in a database transaction to ensure data integrity.
     *
     * @param StoreNewPlanRequest $request  The validated request containing plan input data.
     *
     * @return JsonResponse  A response indicating the result of the operation (success or error).
     */
    public function store(StoreNewPlanRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) return ApiResponse::error('المستخدم غير موجود', 404);

            DB::beginTransaction();

            $response = $this->memorizationPlanService->makeNewMemoPlanWithHisItems($user, $request->validated());

            DB::commit();

            return $response;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error("حدث خطأ اثناء انشاء الخطة", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retrieve detailed information and associated items for the given memorization plan.
     *
     * @param \App\Models\MemorizationPlan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlanDetails(int $planId): JsonResponse
    {
        try {

            return $this->memorizationPlanService->planDetailsAndHisItems($planId);

        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete the specified memorization plan if it belongs to the authenticated user.
     *
     * @param \App\Models\MemorizationPlan $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePlan(int $planId): JsonResponse
    {
        try {
            $plan = $this->memorizationPlanService->memorizationPlanRepository->find($planId);

            if (!$plan || $plan->user_id !== Auth::id()) {
                return ApiResponse::error("الخطة غير موجودة", 404);
            }

            $plan->delete();

            return ApiResponse::success(null, "تم حذف الخطة بنجاح");
        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function pauseMemorizationPlan(int $planId): JsonResponse
    {
        try {

            return $this->memorizationPlanService->handlePauseMemorizationPlan($planId);

        } catch (\Throwable $e) {
            return ApiResponse::error("لقد حدث خطأ ما أثناء إيقاف الخطة مؤقتًا.", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function activateMemorizationPlan(int $planId): JsonResponse
    {
        try {

            return $this->memorizationPlanService->handleActivateMemorizationPlan($planId);

        } catch (\Throwable $e) {
            return ApiResponse::error("لقد حدث خطأ ما أثناء التنشيط.", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

}
