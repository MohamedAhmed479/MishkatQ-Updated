<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserPreferenceService
{
    public function handleGetUserPreferences(User $user): JsonResponse
    {
        $user_preference = $user->preference;

        if ($user_preference) return ApiResponse::success(new UserPreferenceResource($user_preference));

        return ApiResponse::error('تفصيلات المستخدم غير موجودة', 404);
    }

    public function handleUpdateUserPreferences(User $user, Array $data): JsonResponse
    {
        $user_preference = $user->preference;

        if (!$user_preference) return ApiResponse::error('تفصيلات المستخدم غير موجودة', 404);

        $user_preference->update($data);

        return ApiResponse::success(
            new UserPreferenceResource($user_preference),
            "تم تحديث تفضيلات المستخدم بنجاح"
        );
    }
}
