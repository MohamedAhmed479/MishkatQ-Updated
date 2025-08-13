<?php

namespace App\Services;

use App\Events\UserRegisteredEvent;
use App\Helpers\ApiResponse;
use App\Models\Device;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function registerUserWithDeviceAndToken($userData, $deviceData)
    {
        $user = $this->storeUser($userData);

        event(new UserRegisteredEvent($user));

        $tokenResult = $user->createToken("user-registration", ['*'], now()->addDays(5));

        $deviceData["user_id"] = $user->id;
        $deviceData["token_id"] = $tokenResult->accessToken->id;

        $device = $this->storeUserDevice($deviceData);

        return [
            "name" => $user->name,
            "email" => $user->email,
            "email_verified_at" => $user->email_verified_at ?? null,
            "created_at" => $user->created_at,
            "token" => $tokenResult->plainTextToken,
            "profile" => [
                "username" => $user->profile->username,
                "profile_image" => $user->profile->profile_image,
                "verses_memorized_count" => $user->profile->verses_memorized_count,
            ],
            "preferences" => [
                "daily_minutes" => $user->preference->daily_minutes,
                "sessions_per_day" => $user->preference->sessions_per_day,
                "preferred_times" => $user->preference->preferred_times,
                "current_level" => $user->preference->current_level,
                "tafsir_id" => $user->preference->tafsir_id ?? null,
                "tafsir_name" => $user->preference->tafsir ? $user->preference->tafsir->name : null,
            ],
            "deviceInfo" => [
                "device_type" => $device->device_type,
                "device_name" => $device->device_name,
                "platform" => $device->platform,
                "browser" => $device->browser,
                "ip_address" => $device->ip_address,
            ],
        ];
    }

    public function storeUser(Array $userData): User
    {
        $userData['password'] = Hash::make($userData['password']);

        return $this->userRepository->create($userData);

    }

    public function storeUserDevice(Array $userDeviceData): Device
    {
        return Device::create($userDeviceData);
    }

    public function loginWithDevice(Array $credentials, Array $deviceInfo)
    {
        if(! $this->isValidCredentials($credentials)) {
            return ApiResponse::unauthorized("بيانات اعتماد غير صالحة");
        }

        $user = $this->userRepository->findByEmail($credentials['email']);

        $tokenResult = $user->createToken("user-login", ['*'], now()->addDays(10));

        $deviceInfo["user_id"] = $user->id;
        $deviceInfo["token_id"] = $tokenResult->accessToken->id;

        if (! $this->deviceExistsForUser($user->id, $deviceInfo)) {
            $device = $this->storeUserDevice($deviceInfo);
        } else {
            $device = $this->getUserDevice($user->id, $deviceInfo);
        }

        $data = [
            "name" => $user->name,
            "email" => $user->email,
            "email_verified_at" => $user->email_verified_at ?? null,
            "created_at" => $user->created_at,
            "token" => $tokenResult->plainTextToken,
            "profile" => [
                "username" => $user->profile->username,
                "profile_image" => $user->profile->profile_image,
                "verses_memorized_count" => $user->profile->verses_memorized_count,
            ],
            "preferences" => [
                "daily_minutes" => $user->preference->daily_minutes,
                "sessions_per_day" => $user->preference->sessions_per_day,
                "preferred_times" => $user->preference->preferred_times,
                "current_level" => $user->preference->current_level,
                "tafsir_id" => $user->preference->tafsir_id ?? null,
                "tafsir_name" => $user->preference->tafsir ? $user->preference->tafsir->name : null,
            ],
            "deviceInfo" => [
                "device_type" => $device->device_type,
                "device_name" => $device->device_name,
                "platform" => $device->platform,
                "browser" => $device->browser,
                "ip_address" => $device->ip_address,
            ],
        ];

        return ApiResponse::success($data, "تم تسجيل الدخول بنجاح");
    }

    public function isValidCredentials(Array $credentials): bool
    {
        $user = $this->userRepository->findByEmail($credentials['email']);
        if (!$user) {
            return false;
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            return false;
        }

        return true;
    }

    public function deviceExistsForUser(int $userId, Array $deviceInfo): bool
    {
        return Device::where('user_id', $userId)
            ->where('device_type', $deviceInfo['device_type'])
            ->where('device_name', $deviceInfo['device_name'])
            ->where('platform', $deviceInfo['platform'])
            ->where('browser', $deviceInfo['browser'])
            ->exists();
    }

    public function getUserDevice(int $userId, Array $deviceInfo): Device
    {
        return Device::where('user_id', $userId)
            ->where('device_type', $deviceInfo['device_type'])
            ->where('device_name', $deviceInfo['device_name'])
            ->where('platform', $deviceInfo['platform'])
            ->where('browser', $deviceInfo['browser'])
            ->first();
    }

    public function sendVerificationEmail(): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'تم التحقق من البريد الإلكتروني بالفعل');
        }

        $user->sendEmailVerificationNotification();

        return ApiResponse::success(null, 'تم إرسال رمز التحقق بنجاح');
    }

    public function handleEmailVerification(array $data = []): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'تم التحقق من البريد الإلكتروني بالفعل');
        }

        $cachedCode = \Illuminate\Support\Facades\Cache::get("email_verification_code_user_{$user->id}");

        if (!$cachedCode || !hash_equals((string)$cachedCode, (string)($data['code'] ?? ''))) {
            return ApiResponse::error('رمز التحقق غير صالح أو منتهي الصلاحية', 422);
        }

        if ($this->userRepository->markEmailAsVerified($user)) {
            // Invalidate the code after successful verification
            \Illuminate\Support\Facades\Cache::forget("email_verification_code_user_{$user->id}");

            event(new Verified($user));

            return ApiResponse::success(null, 'تم التحقق من البريد الإلكتروني بنجاح');
        }

        return ApiResponse::error('غير قادر على التحقق من البريد الإلكتروني', 500);
    }

    public function handlePasswordResetLink(Array $email): JsonResponse
    {
        $status = Password::sendResetLink($email);

        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponse::success(
                null,
                'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني'
            );
        }

        return ApiResponse::error(
            'غير قادر على إرسال رابط إعادة تعيين كلمة المرور',
            422
        );
    }

    public function handleResetPassword(Array $data):  JsonResponse
    {
        $email = $data['email'] ?? '';
        $code = $data['code'] ?? '';

        $cachedCode = \Illuminate\Support\Facades\Cache::get("password_reset_code_{$email}");
        $cachedToken = \Illuminate\Support\Facades\Cache::get("password_reset_token_{$email}");

        if (!$cachedCode || !hash_equals((string)$cachedCode, (string)$code)) {
            return ApiResponse::error('رمز إعادة التعيين غير صالح أو منتهي الصلاحية', 422);
        }

        $payload = [
            'email' => $email,
            'token' => $cachedToken,
            'password' => $data['password'] ?? '',
            'password_confirmation' => $data['password_confirmation'] ?? '',
        ];

        $status = Password::reset(
            $payload,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Invalidate reset code/token after success
            \Illuminate\Support\Facades\Cache::forget("password_reset_code_{$email}");
            \Illuminate\Support\Facades\Cache::forget("password_reset_token_{$email}");

            return ApiResponse::success(
                null,
                'تم إعادة تعيين كلمة المرور بنجاح'
            );
        }

        return ApiResponse::error(
            'غير قادر على إعادة تعيين كلمة المرور',
            422
        );
    }
}
