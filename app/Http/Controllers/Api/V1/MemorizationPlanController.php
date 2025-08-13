<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewPlanRequest;
use App\Services\MemorizationPlanService;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemorizationPlanController extends Controller
{
    protected $memorizationPlanService;
    protected $auditService;

    public function __construct(MemorizationPlanService $memorizationPlanService, AuditService $auditService)
    {
        $this->memorizationPlanService = $memorizationPlanService;
        $this->auditService = $auditService;
    }

    /**
     * Display a paginated list of the authenticated user's memorization plans.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user_plans = $user->memorizationPlans()->paginate(5);

            // Log plans viewing
            $this->auditService->log(
                'view_plans',
                "User viewed memorization plans",
                null,
                null,
                null,
                'low',
                'data',
                false,
                ['plans_count' => count($user_plans)]
            );

            if (count($user_plans) > 0) {
                return ApiResponse::withPagination($user_plans);
            }

            return ApiResponse::success(null, "ليس لديك خطط");
        } catch (\Throwable $e) {
            // Log error
            $this->auditService->log(
                'view_plans_failed',
                "Failed to view memorization plans: {$e->getMessage()}",
                null,
                null,
                null,
                'medium',
                'data',
                false,
                null,
                'failed'
            );

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

            // Log successful plan creation
            $this->auditService->log(
                'create_plan',
                "Memorization plan created successfully",
                null,
                null,
                $request->validated(),
                'medium',
                'data',
                false,
                [
                    'plan_type' => $request->input('plan_type'),
                    'duration_days' => $request->input('duration_days')
                ]
            );

            DB::commit();

            return $response;
        } catch (\Throwable $e) {
            DB::rollBack();
            
            // Log failed plan creation
            $this->auditService->log(
                'create_plan_failed',
                "Failed to create memorization plan: {$e->getMessage()}",
                null,
                null,
                null,
                'high',
                'data',
                false,
                ['request_data' => $request->validated()],
                'failed'
            );

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
            $response = $this->memorizationPlanService->planDetailsAndHisItems($planId);

            // Log plan details viewing
            $this->auditService->log(
                'view_plan_details',
                "User viewed plan details for plan ID: {$planId}",
                null,
                null,
                null,
                'low',
                'data',
                false,
                ['plan_id' => $planId]
            );

            return $response;

        } catch (\Throwable $e) {
            // Log error
            $this->auditService->log(
                'view_plan_details_failed',
                "Failed to view plan details for plan ID: {$planId} - {$e->getMessage()}",
                null,
                null,
                null,
                'medium',
                'data',
                false,
                ['plan_id' => $planId],
                'failed'
            );

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
                // Log unauthorized access attempt
                $this->auditService->logSecurity(
                    'unauthorized_plan_access',
                    "Unauthorized attempt to delete plan ID: {$planId}",
                    'high'
                );
                
                return ApiResponse::error("الخطة غير موجودة", 404);
            }

            // Store plan data before deletion for audit
            $planData = $plan->toArray();
            
            $plan->delete();

            // Log successful plan deletion
            $this->auditService->logDataOperation(
                'deleted',
                $plan,
                $planData,
                null,
                'success'
            );

            return ApiResponse::success(null, "تم حذف الخطة بنجاح");
        } catch (\Throwable $e) {
            // Log failed deletion
            $this->auditService->log(
                'delete_plan_failed',
                "Failed to delete plan ID: {$planId} - {$e->getMessage()}",
                null,
                null,
                null,
                'high',
                'data',
                false,
                ['plan_id' => $planId],
                'failed'
            );

            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function pauseMemorizationPlan(int $planId): JsonResponse
    {
        try {
            $response = $this->memorizationPlanService->handlePauseMemorizationPlan($planId);

            // Log plan pause
            $this->auditService->log(
                'pause_plan',
                "Memorization plan paused: {$planId}",
                null,
                null,
                null,
                'medium',
                'data',
                false,
                ['plan_id' => $planId, 'action' => 'pause']
            );

            return $response;

        } catch (\Throwable $e) {
            // Log failed pause
            $this->auditService->log(
                'pause_plan_failed',
                "Failed to pause plan ID: {$planId} - {$e->getMessage()}",
                null,
                null,
                null,
                'medium',
                'data',
                false,
                ['plan_id' => $planId],
                'failed'
            );

            return ApiResponse::error("لقد حدث خطأ ما أثناء إيقاف الخطة مؤقتًا.", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function activateMemorizationPlan(int $planId): JsonResponse
    {
        try {
            $response = $this->memorizationPlanService->handleActivateMemorizationPlan($planId);

            // Log plan activation
            $this->auditService->log(
                'activate_plan',
                "Memorization plan activated: {$planId}",
                null,
                null,
                null,
                'medium',
                'data',
                false,
                ['plan_id' => $planId, 'action' => 'activate']
            );

            return $response;

        } catch (\Throwable $e) {
            // Log failed activation
            $this->auditService->log(
                'activate_plan_failed',
                "Failed to activate plan ID: {$planId} - {$e->getMessage()}",
                null,
                null,
                null,
                'medium',
                'data',
                false,
                ['plan_id' => $planId],
                'failed'
            );

            return ApiResponse::error("لقد حدث خطأ ما أثناء التنشيط.", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

}
