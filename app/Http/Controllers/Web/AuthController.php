<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Use web guard (session) for web authentication
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            
            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'يرجى التحقق من بريدك الإلكتروني قبل تسجيل الدخول.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            
            return redirect()->intended('/app/dashboard');
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $deviceInfo = [
                'device_type' => $request->header('User-Agent', 'web'),
                'device_name' => 'Web Browser',
                'platform' => $request->header('Sec-CH-UA-Platform', 'unknown'),
                'browser' => $request->header('User-Agent', 'unknown'),
                'ip_address' => $request->ip(),
            ];

            $data = $this->authService->registerUserWithDeviceAndToken(
                $validated,
                $deviceInfo
            );

            return redirect()->route('user.login')->with('success', 'تم إنشاء حسابك بنجاح! يرجى التحقق من بريدك الإلكتروني قبل تسجيل الدخول.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.',
            ])->withInput();
        }
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('user.password.verify')->with([
                'status' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني',
                'email' => $validated['email'],
            ]);
        }

        return back()->withErrors([
            'email' => 'لم نتمكن من إرسال رابط إعادة تعيين كلمة المرور. يرجى التحقق من البريد الإلكتروني.',
        ])->onlyInput('email');
    }

    public function verifyOtp(Request $request)
    {
        // Ensure OTP is a string, not an array
        $otpInput = $request->input('otp');
        if (is_array($otpInput)) {
            $otpInput = implode('', $otpInput);
        }
        $request->merge(['otp' => $otpInput]);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        try {
            $response = $this->authService->handleVerifyOtp([
                'email' => $validated['email'],
                'code' => (string) $validated['otp'],
            ]);

            $responseData = json_decode($response->getContent(), true);

            // If verification successful, store email and token in session for reset password page
            if ($response->getStatusCode() === 200 && isset($responseData['status']) && $responseData['status'] === true) {
                $request->session()->put([
                    'password_reset_email' => $validated['email'],
                    'password_reset_token' => $responseData['data']['token'] ?? null,
                ]);

                return redirect()->route('user.password.reset')->with('success', 'تم التحقق من الرمز بنجاح. يرجى إدخال كلمة المرور الجديدة.');
            }

            return back()->withErrors([
                'otp' => $responseData['message'] ?? 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
            ])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => 'حدث خطأ أثناء التحقق من الرمز. يرجى المحاولة مرة أخرى.',
            ])->withInput();
        }
    }

    public function showResetPasswordForm(Request $request)
    {
        $email = $request->session()->get('password_reset_email');
        $token = $request->session()->get('password_reset_token');
        
        if (!$email || !$token) {
            return redirect()->route('user.password.request')->withErrors([
                'email' => 'يرجى إدخال البريد الإلكتروني أولاً.',
            ]);
        }

        return Inertia::render('Auth/ResetPassword', [
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'token' => ['required', 'string'],
        ]);

        try {
            $response = $this->authService->handleResetPassword([
                'email' => $validated['email'],
                'token' => $validated['token'],
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
            ]);

            $responseData = json_decode($response->getContent(), true);

            if ($response->getStatusCode() === 200 && isset($responseData['status']) && $responseData['status'] === true) {
                $request->session()->forget(['password_reset_email', 'password_reset_token']);
                
                return redirect()->route('user.login')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
            }

            return back()->withErrors([
                'password' => $responseData['message'] ?? 'فشل في إعادة تعيين كلمة المرور. يرجى المحاولة مرة أخرى.',
            ])->withInput();

        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'password' => 'حدث خطأ أثناء إعادة تعيين كلمة المرور: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function resendVerificationEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'البريد الإلكتروني غير موجود.',
            ])->onlyInput('email');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->withErrors([
                'email' => 'تم التحقق من البريد الإلكتروني بالفعل.',
            ])->onlyInput('email');
        }

        try {
            $user->sendEmailVerificationNotification();
            
            return back()->with('success', 'تم إرسال رابط التحقق بنجاح. يرجى التحقق من بريدك الإلكتروني.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'حدث خطأ أثناء إرسال رابط التحقق. يرجى المحاولة مرة أخرى.',
            ])->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }
}
