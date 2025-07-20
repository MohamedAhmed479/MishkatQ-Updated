<?php

namespace App\Services;

use App\Events\UserRegisteredEvent;
use App\Helpers\ApiResponse;
use App\Models\Device;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
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

        return User::create($userData);

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

        $user = User::where('email', $credentials['email'])->first();

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
        $user = User::where('email', $credentials['email'])->first();
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

        return ApiResponse::success(null, 'تم إرسال رابط التحقق بنجاح');
    }

    public function handleEmailVerification(): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'تم التحقق من البريد الإلكتروني بالفعل');
        }

        if ($user->markEmailAsVerified()) {
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
                'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني'
            );
        }

        return ApiResponse::error(
            'غير قادر على إرسال رابط إعادة تعيين كلمة المرور',
            422
        );
    }

    public function handleResetPassword(Array $data):  JsonResponse
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
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
