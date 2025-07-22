<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\UserRegisteredEvent;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetLinkRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserEmailVerificationRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserResetPasswordRequest;
use App\Models\PersonalAccessToken;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Password;

class UserAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(StoreUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $this->authService->registerUserWithDeviceAndToken(
                $request->only(['name', 'email', 'password']),
                $request->only(['device_type', 'device_name', 'platform', 'browser', 'ip_address'])
            );

            DB::commit();

            return ApiResponse::created($data, "تم تسجيل المستخدم بنجاح. تم إرسال رسالة التحقق إلى {$data['email']}.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error('فشل التسجيل', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $deviceInfo = $request->only(["device_type", "device_name", "platform", "browser", "ip_address"]);

            return $this->authService->loginWithDevice($credentials, $deviceInfo);

        } catch (\Exception $e) {
            return ApiResponse::error('فشل تسجيل الدخول', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $user =  Auth::user();

            $user->currentAccessToken()->delete();

            return ApiResponse::success(null, 'تم تسجيل الخروج بنجاح.');

        } catch (\Exception $e) {
            return ApiResponse::error('فشل تسجيل الخروج', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendEmailVerificationNotification(): JsonResponse
    {
        try {

            return $this->authService->sendVerificationEmail();

        } catch (\Exception $e) {
            return ApiResponse::error('فشل في ارسال بريد التحقق', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function verifyEmail(UserEmailVerificationRequest $request): JsonResponse
    {
        try {

            return $this->authService->handleEmailVerification();

        } catch (\Exception $e) {
            return ApiResponse::error('فشل في ارسال بريد التحقق', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function passwordResetLink(PasswordResetLinkRequest $request): JsonResponse
    {
        try {

            return $this->authService->handlePasswordResetLink($request->only('email'));

        } catch (\Exception $e) {
            return ApiResponse::error('فشل في ارسال رابط اعاده كلمه المرور', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function resetPassword(UserResetPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->only('email', 'password', 'password_confirmation', 'token');

            return $this->authService->handleResetPassword($data);

        } catch (\Exception $e) {
            return ApiResponse::error('فشل في ارسال رابط اعاده كلمه المرور', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
