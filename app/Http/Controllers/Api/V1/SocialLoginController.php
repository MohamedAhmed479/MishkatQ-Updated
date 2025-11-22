<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\AuditService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class SocialLoginController extends Controller
{
    protected $authService;
    protected $auditService;

    public function __construct(AuthService $authService, AuditService $auditService)
    {
        $this->authService = $authService;
        $this->auditService = $auditService;
    }

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider): JsonResponse
    {
        try {
            DB::beginTransaction();

            $providerUser = Socialite::driver($provider)->stateless()->user();

            // Build userData to pass to AuthService
            $userData = [
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $providerUser->getId(),
                'provider_token' => $providerUser->token ?? null,
                'gender' => $providerUser->user['gender'] ?? null,
            ];

            // Build device info from headers or defaults
            $deviceInfo = [
                'device_type' => request()->header('Device-Type', 'unknown'),
                'device_name' => request()->header('Device-Name', 'unknown'),
                'platform' => request()->header('Platform', 'unknown'),
                'browser' => request()->header('Browser', 'unknown'),
                'ip_address' => request()->ip(),
            ];

            $data = $this->authService->loginOrRegisterSocialUser($userData, $deviceInfo);

            DB::commit();

            // audit
            $this->auditService->logAuth(
                'social_login',
                null,
                'success',
                "User logged in with {$provider}: {$userData['email']}"
            );

            return ApiResponse::success($data, "تم تسجيل الدخول بنجاح باستخدام {$provider}");

        } catch (Exception $e) {
            DB::rollBack();

            $this->auditService->logAuth(
                'social_login_failed',
                null,
                'failed',
                "Social login failed for {$provider}: {$e->getMessage()}"
            );

            return ApiResponse::error('فشل تسجيل الدخول عبر ' . $provider, 500, ['error' => $e->getMessage()]);
        }
    }
}
