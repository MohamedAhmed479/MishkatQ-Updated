<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use App\Services\IncentiveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class IncentiveController extends Controller
{
    public function __construct(
        private readonly IncentiveService $incentiveService
    ) {}


    public function getBadges(): JsonResponse
    {
        try {

            return $this->incentiveService->activeBadges();

        } catch (\Throwable $exception) {
            return ApiResponse::error($exception->getMessage(), "حدث خطا ما");
        }
    }

    public function getUserBadges(int $userId): JsonResponse
    {
        try {

            return $this->incentiveService->userBadges($userId);

        } catch (\Throwable $exception) {
            return ApiResponse::error($exception->getMessage(), "حدث خطا ما");
        }
    }

    public function getPointsHistory(int $userId): JsonResponse
    {
        try {

            return $this->incentiveService->pointsTransactions($userId);

        } catch (\Throwable $exception) {
            return ApiResponse::error($exception->getMessage(), "حدث خطا ما");
        }
    }


    public function getLeaderboard(Request $request): JsonResponse
    {
        try {
            $type = $request->input('type', 'monthly');
            $limit = $request->input('limit', 10);

            if (!in_array($type, ['daily', 'weekly', 'monthly', 'yearly'])) {
                return ApiResponse::error("نوع لوحة المتصدرين غير صالح");
            }

            return $this->incentiveService->leaderboard($type, $limit);

        } catch (\Throwable $exception) {
            return ApiResponse::error($exception->getMessage(), "حدث خطا ما");
        }
    }


    public function getUserStats(int $userId): JsonResponse
    {
        try {

            return $this->incentiveService->userStats($userId);

        } catch (\Throwable $exception) {
            return ApiResponse::error($exception->getMessage(), "حدث خطا ما");
        }
    }
}
