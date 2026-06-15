<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Events\UserRegisteredEvent;
use App\Models\PersonalAccessToken;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\VerifyOtpRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\PasswordResetLinkRequest;
use App\Http\Requests\UserResetPasswordRequest;
use App\Http\Requests\UserEmailVerificationRequest;

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
                $request->only(['name', 'email', 'gender', 'password']),
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

    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return ApiResponse::error('رابط التحقق غير صالح', 403);
            }

            if ($user->hasVerifiedEmail()) {
                return ApiResponse::success('البريد الإلكتروني تم التحقق منه مسبقاً');
            }

            $user->markEmailAsVerified();

            // Log verification success
            $this->auditService->logAuth('email_verified', $user, 'success', "Email verified successfully: {$user->email}");

            return ApiResponse::success('تم التحقق من البريد الإلكتروني بنجاح');
        } catch (\Exception $e) {
            $this->auditService->logAuth('email_verification_failed', null, 'failed', $e->getMessage());
            return ApiResponse::error('فشل التحقق من البريد الإلكتروني', 500, ['error' => $e->getMessage()]);
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

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse{
        $email = $request->input('email');

        try {
            $data = $request->only('email', 'code');
            $responseData = $this->authService->handleVerifyOtp($data);

            if(! $responseData['success']){
                return ApiResponse::error($responseData['message'], $responseData["HttpCode"]);
            }

            // Log successful OTP verification
            $this->auditService->logAuth(
                'otp_verification_successful',
                null,
                'success',
                "OTP verified successfully for: {$email}"
            );

            return ApiResponse::success([
                "email" => $responseData["email"],
                "token" => $responseData["token"],
            ], $responseData["message"], $responseData["HttpCode"]);

        } catch (\Exception $e) {
            // Log failed OTP verification
            $this->auditService->logAuth(
                'otp_verification_failed',
                null,
                'failed',
                "OTP verification failed for: {$email} - {$e->getMessage()}"
            );

            return ApiResponse::error('فشل في التحقق من رمز إعادة التعيين', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function resetPassword(UserResetPasswordRequest $request): JsonResponse
    {
        $email = $request->input('email');

        try {
            $data = $request->only('email', 'password', 'password_confirmation', 'token');
            $responseData = $this->authService->handleResetPassword($data);

            if(! $responseData['success']){
                return ApiResponse::error($responseData['message'], $responseData["HttpCode"]);
            }

            // Log successful password reset
            $this->auditService->logAuth(
                'password_reset_completed',
                null,
                'success',
                "Password reset completed for: {$email}"
            );

            return ApiResponse::success($responseData["data"], $responseData["message"], $responseData["HttpCode"]);

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
