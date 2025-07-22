<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Services\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public $preferenceService;

    public function __construct(UserPreferenceService $preferenceService) {
        $this->preferenceService = $preferenceService;
    }

    public function getUserPreferences(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) return ApiResponse::error('المستخدم غير موجود', 404);

            return $this->preferenceService->handleGetUserPreferences($user);

        } catch(\Exception $e) {
            return ApiResponse::error('فشل جلب التفضيلات', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function updateUserPreferences(UpdateUserPreferenceRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) return ApiResponse::error('المستخدم غير موجود', 404);

            $data = $request->validated();
            $data['preferred_times'] = json_encode($data['preferred_times']);

            return $this->preferenceService->handleUpdateUserPreferences($user, $data);

        } catch(\Exception $e) {
            return ApiResponse::error('فشل تعديل التفضيلات', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

}
