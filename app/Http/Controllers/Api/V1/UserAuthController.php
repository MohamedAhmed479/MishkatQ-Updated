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
use App\Services\AuditService;
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
    protected $auditService;

    public function __construct(AuthService $authService, AuditService $auditService)
    {
        $this->authService = $authService;
        $this->auditService = $auditService;
    }

    public function register(StoreUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $this->authService->registerUserWithDeviceAndToken(
                $request->only(['name', 'email', 'password']),
                $request->only(['device_type', 'device_name', 'platform', 'browser', 'ip_address'])
            );

            // Log successful registration
            $this->auditService->logAuth(
                'register',
                null,
                'success',
                "User registered successfully: {$data['email']}"
            );

            DB::commit();

            return ApiResponse::created($data, "تم تسجيل المستخدم بنجاح. تم إرسال رسالة التحقق إلى {$data['email']}.");

        } catch (\Throwable $e) {
            DB::rollBack();
            
            // Log failed registration
            $this->auditService->logAuth(
                'register',
                null,
                'failed',
                "User registration failed: {$e->getMessage()}"
            );

            return ApiResponse::error('فشل التسجيل', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $email = $request->input('email');
        
        try {
            $credentials = $request->only(['email', 'password']);
            $deviceInfo = $request->only(["device_type", "device_name", "platform", "browser", "ip_address"]);

            $response = $this->authService->loginWithDevice($credentials, $deviceInfo);
            
            // Log successful login
            $user = Auth::user();
            $this->auditService->logAuth(
                'login',
                $user,
                'success',
                "User logged in successfully: {$email}"
            );

            return $response;

        } catch (\Exception $e) {
            // Log failed login
            $this->auditService->logAuth(
                'failed_login',
                null,
                'failed',
                "Failed login attempt for: {$email} - {$e->getMessage()}"
            );

            return ApiResponse::error('فشل تسجيل الدخول', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();

            $user->currentAccessToken()->delete();

            // Log successful logout
            $this->auditService->logAuth(
                'logout',
                $user,
                'success',
                "User logged out successfully: {$user->email}"
            );

            return ApiResponse::success(null, 'تم تسجيل الخروج بنجاح.');

        } catch (\Exception $e) {
            // Log failed logout
            $this->auditService->logAuth(
                'logout',
                Auth::user(),
                'failed',
                "Failed logout attempt: {$e->getMessage()}"
            );

            return ApiResponse::error('فشل تسجيل الخروج', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendEmailVerificationNotification(): JsonResponse
    {
        try {
            $user = Auth::user();
            $response = $this->authService->sendVerificationEmail();

            // Log email verification request
            $this->auditService->logAuth(
                'email_verification_sent',
                $user,
                'success',
                "Email verification sent to: {$user->email}"
            );

            return $response;

        } catch (\Exception $e) {
            // Log failed email verification
            $user = Auth::user();
            $this->auditService->logAuth(
                'email_verification_sent',
                $user,
                'failed',
                "Failed to send email verification: {$e->getMessage()}"
            );

            return ApiResponse::error('فشل في ارسال بريد التحقق', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function verifyEmail(UserEmailVerificationRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->handleEmailVerification($request->validated());
            
            $user = Auth::user();
            // Log successful email verification
            $this->auditService->logAuth(
                'email_verified',
                $user,
                'success',
                "Email verified successfully: {$user->email}"
            );

            return $response;

        } catch (\Exception $e) {
            // Log failed email verification
            $user = Auth::user();
            $this->auditService->logAuth(
                'email_verification_failed',
                $user,
                'failed',
                "Email verification failed: {$e->getMessage()}"
            );

            return ApiResponse::error('فشل في ارسال بريد التحقق', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function passwordResetLink(PasswordResetLinkRequest $request): JsonResponse
    {
        $email = $request->input('email');
        
        try {
            $response = $this->authService->handlePasswordResetLink($request->only('email'));

            // Log password reset link request
            $this->auditService->logAuth(
                'password_reset_link_sent',
                null,
                'success',
                "Password reset link sent to: {$email}"
            );

            return $response;

        } catch (\Exception $e) {
            // Log failed password reset link
            $this->auditService->logAuth(
                'password_reset_link_failed',
                null,
                'failed',
                "Failed to send password reset link to: {$email} - {$e->getMessage()}"
            );

            return ApiResponse::error('فشل في ارسال رابط اعاده كلمه المرور', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function resetPassword(UserResetPasswordRequest $request): JsonResponse
    {
        $email = $request->input('email');
        
        try {
            $data = $request->only('email', 'password', 'password_confirmation', 'code');
            $response = $this->authService->handleResetPassword($data);

            // Log successful password reset
            $this->auditService->logAuth(
                'password_reset_completed',
                null,
                'success',
                "Password reset completed for: {$email}"
            );

            return $response;

        } catch (\Exception $e) {
            // Log failed password reset
            $this->auditService->logAuth(
                'password_reset_failed',
                null,
                'failed',
                "Password reset failed for: {$email} - {$e->getMessage()}"
            );

            return ApiResponse::error('فشل في اعاده تعيين كلمه المرور', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
