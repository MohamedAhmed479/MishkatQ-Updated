<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\ReadingPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReadingPlanController extends Controller
{
    protected ReadingPlanService $readingPlanService;

    public function __construct(ReadingPlanService $readingPlanService)
    {
        $this->readingPlanService = $readingPlanService;
    }

    /**
     * Get all reading plans for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $status = $request->query('status');
            return $this->readingPlanService->getUserPlans(Auth::user(), $status);
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب الخطط', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a new reading plan
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'type' => 'nullable|in:sequential,custom',
                'target_type' => 'nullable|in:pages,juzs,verses,time',
                'pages_per_day' => 'nullable|integer|min:1|max:100',
                'start_page' => 'nullable|integer|min:1|max:604',
                'end_page' => 'nullable|integer|min:1|max:604|gte:start_page',
                'start_date' => 'nullable|date|after_or_equal:today',
                'end_date' => 'nullable|date|after:start_date',
                'reading_mode' => 'nullable|in:hadr,tadabbur',
                'settings' => 'nullable|array',
                'settings.theme' => 'nullable|in:classic,night,soft_blue,mint',
                'settings.font_size' => 'nullable|integer|min:14|max:48',
                'settings.reciter_id' => 'nullable|integer|exists:reciters,id',
                'settings.auto_scroll' => 'nullable|boolean',
                'settings.show_translation' => 'nullable|boolean',
                'settings.haptic_feedback' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            return $this->readingPlanService->createPlan(Auth::user(), $request->all());
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء إنشاء الخطة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get plan details with today's daily wird
     */
    public function show(int $planId): JsonResponse
    {
        try {
            return $this->readingPlanService->getPlanWithDailyWird($planId);
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب تفاصيل الخطة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get suggested plan templates
     */
    public function suggestions(): JsonResponse
    {
        try {
            return $this->readingPlanService->getSuggestedPlans();
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب الاقتراحات', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark reading progress
     */
    public function markProgress(Request $request, int $planId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_page' => 'nullable|integer|min:1|max:604',
                'end_page' => 'nullable|integer|min:1|max:604|gte:start_page',
                'reading_mode' => 'nullable|in:hadr,tadabbur',
                'duration_minutes' => 'nullable|integer|min:1|max:1440',
                'notes' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            return $this->readingPlanService->markProgress($planId, $request->all());
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء حفظ التقدم', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Pause a reading plan
     */
    public function pause(int $planId): JsonResponse
    {
        try {
            return $this->readingPlanService->pausePlan($planId);
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء إيقاف الخطة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Resume a paused reading plan
     */
    public function resume(int $planId): JsonResponse
    {
        try {
            return $this->readingPlanService->resumePlan($planId);
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء استئناف الخطة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Auto-adjust plan to meet deadlines
     */
    public function autoAdjust(int $planId): JsonResponse
    {
        try {
            return $this->readingPlanService->autoAdjustPlan($planId);
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء تعديل الخطة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update plan settings (theme, reciter, etc.)
     */
    public function updateSettings(Request $request, int $planId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'theme' => 'nullable|in:classic,night,soft_blue,mint',
                'font_size' => 'nullable|integer|min:14|max:48',
                'reciter_id' => 'nullable|integer|exists:reciters,id',
                'auto_scroll' => 'nullable|boolean',
                'show_translation' => 'nullable|boolean',
                'haptic_feedback' => 'nullable|boolean',
                'view_mode' => 'nullable|in:page,verse',
                'script_type' => 'nullable|in:uthmani,imlaei',
                'reading_mode' => 'nullable|in:hadr,tadabbur',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            return $this->readingPlanService->updateSettings($planId, $request->all());
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء حفظ الإعدادات', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get reading statistics for the user
     */
    public function statistics(): JsonResponse
    {
        try {
            return $this->readingPlanService->getStatistics(Auth::user());
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء جلب الإحصائيات', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Start a new Hatmah after completing the current one
     */
    public function startNewHatmah(int $planId): JsonResponse
    {
        try {
            return $this->readingPlanService->startNewHatmah($planId);
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء بدء ختمة جديدة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a reading plan
     */
    public function destroy(int $planId): JsonResponse
    {
        try {
            $plan = \App\Models\ReadingPlan::find($planId);

            if (!$plan || $plan->user_id !== Auth::id()) {
                return ApiResponse::notFound('خطة القراءة غير موجودة');
            }

            $plan->delete();

            return ApiResponse::success(null, 'تم حذف الخطة بنجاح');
        } catch (\Throwable $e) {
            return ApiResponse::error('حدث خطأ أثناء حذف الخطة', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
