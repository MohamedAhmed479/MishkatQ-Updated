<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Analytics",
 *     description="API Endpoints for user analytics and progress tracking"
 * )
 */
class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }


    public function getProgressAnalytics(): JsonResponse
    {
        $user = Auth::user();

        $analytics = $this->analyticsService->getUserProgressAnalytics($user);

        if (!$analytics['success']) {
            return ApiResponse::error($analytics['message'], 404);
        }

        return ApiResponse::success($analytics['data'], "تم جلب تحليلات الخطة الحاليه النشطه");
    }
}
