<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

            // Log the user in after registration using web guard
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                Auth::guard('web')->login($user, $request->boolean('remember'));
            }

            return redirect()->route('user.dashboard')->with('success', 'تم إنشاء حسابك بنجاح!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.',
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
