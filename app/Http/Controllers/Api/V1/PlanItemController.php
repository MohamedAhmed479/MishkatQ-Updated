<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PlanItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PlanItemController extends Controller
{
    protected PlanItemService $planItemService;

    public function __construct(PlanItemService $planItemService)
    {

        $this->planItemService = $planItemService;
    }

    public function markAsCompleted(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_id' => 'required|integer|exists:plan_items,id'
            ]);

            return $this->planItemService->handleMarkAsCompleted($validated['item_id']);

        } catch (ValidationException $e) {
            return ApiResponse::error("خطأ في التحقق من البيانات", 422, [
                'errors' => $e->errors(),
            ]);
        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function getTodayItem(): JsonResponse
    {
        try {

            return $this->planItemService->getTodayItem(Auth::id());

        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getContentForSpecificItem(int $planItemId): JsonResponse
    {
        try {

            return $this->planItemService->getPlanItemContent($planItemId);

        } catch (\Throwable $e) {
            return ApiResponse::error("حدث خطأ ما", 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
