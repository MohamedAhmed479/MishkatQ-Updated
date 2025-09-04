<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول — مركز مشكاة</title>
    
    <!-- Google Fonts: Cairo for professional Arabic typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Vite Assets (includes Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Arabic RTL Support and Global Styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
        }

        /* Animated Background - matching dashboard */
        .animated-bg {
            background: linear-gradient(-45deg, #0f172a, #1e293b, #334155, #475569);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating particles animation - subtle slate colors */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(51, 65, 85, 0.2);
            animation: float 6s ease-in-out infinite;
        }
        
        .particle:nth-child(even) {
            background: rgba(71, 85, 105, 0.15);
        }

        .particle:nth-child(1) { width: 80px; height: 80px; top: 10%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 60px; height: 60px; top: 20%; right: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 40px; height: 40px; bottom: 30%; left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 100px; height: 100px; bottom: 10%; right: 10%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 50px; height: 50px; top: 50%; left: 5%; animation-delay: 3s; }
        .particle:nth-child(6) { width: 70px; height: 70px; top: 60%; right: 5%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Form animations */
        .form-slide-in {
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Input focus effects - matching dashboard style */
        .input-field {
            transition: all 0.3s ease;
            cursor: text;
        }

        .input-field:focus {
            transform: scale(1.01);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
            cursor: text;
        }

        /* Button hover effects */
        .login-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            cursor: pointer;
        }

        .login-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover:before {
            left: 100%;
        }

        /* Logo pulse animation */
        .logo-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { 
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7), 0 0 0 0 rgba(255, 255, 255, 0.1);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0.3), 0 0 0 20px rgba(255, 255, 255, 0.05);
                transform: scale(1.02);
            }
            100% { 
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0), 0 0 0 0 rgba(255, 255, 255, 0);
                transform: scale(1);
            }
        }

        /* Glass morphism effect - matching dashboard glass cards */
        .glass {
            backdrop-filter: blur(16px) saturate(180%);
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.15), rgba(51, 65, 85, 0.05));
            border: 1px solid rgba(71, 85, 105, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.7);
        }

        /* Error messages animation */
        .error-message {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Success messages animation */
        .success-message {
            animation: slideInFromTop 0.5s ease-out;
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Interactive elements cursor styles */
        a, button, input[type="checkbox"], label[for] {
            cursor: pointer;
        }

        a:hover, button:hover, input[type="checkbox"]:hover, label[for]:hover {
            cursor: pointer;
        }

        /* Disabled elements */
        button:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Logo hover effect */
        .logo-container:hover {
            cursor: default;
        }
    </style>
</head>
<body class="animated-bg min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Floating Particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <!-- Main Login Container -->
    <div class="glass rounded-3xl p-8 md:p-12 w-full max-w-md shadow-2xl form-slide-in">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <!-- Logo -->
            <div class="logo-container mx-auto w-24 h-24 rounded-2xl bg-white/95 backdrop-blur-sm flex items-center justify-center shadow-2xl shadow-black/20 logo-pulse mb-6 border-2 border-emerald-400/30">
                <img src="{{ asset('images/logo.svg') }}" alt="شعار مشكاة" class="w-16 h-16 rounded-xl" />
            </div>
            
            <!-- Title -->
            <h1 class="text-3xl font-bold text-slate-100 mb-2">مرحباً بعودتك</h1>
            <p class="text-slate-400 text-lg">سجل دخولك إلى مركز مشكاة</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="error-message bg-red-500/20 border border-red-500/50 rounded-xl p-4 mb-6 backdrop-blur-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-semibold text-red-400 mb-1">خطأ في تسجيل الدخول</div>
                        @foreach($errors->all() as $error)
                            <div class="text-red-300 text-sm">{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Messages -->
        @if(session('success'))
            <div class="success-message bg-emerald-500/20 border border-emerald-500/50 rounded-xl p-4 mb-6 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-emerald-300 font-medium">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
            @csrf
            
            <!-- Email Field -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-slate-200">البريد الإلكتروني</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        class="input-field block w-full pr-10 pl-4 py-3 border-0 rounded-xl bg-slate-800/30 backdrop-blur-sm text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-emerald-400 focus:outline-none border border-slate-600/50 focus:border-emerald-400/50"
                        placeholder="أدخل بريدك الإلكتروني"
                    >
                </div>
                @error('email')
                    <p class="text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-slate-200">كلمة المرور</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>

                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required
                        class="input-field block w-full pr-10 pl-4 py-3 border-0 rounded-xl bg-slate-800/30 backdrop-blur-sm text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-emerald-400 focus:outline-none border border-slate-600/50 focus:border-emerald-400/50"
                        placeholder="أدخل كلمة المرور"
                    >
                </div>
                @error('password')
                    <p class="text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox" 
                        class="h-4 w-4 text-emerald-500 focus:ring-emerald-400 border-slate-500 rounded bg-slate-800/30 backdrop-blur-sm"
                    >
                    <label for="remember" class="mr-2 block text-sm text-slate-400">تذكرني</label>
                </div>

                @if (Route::has('password.request'))
                    <a class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors" href="{{ route('password.request') }}">
                        نسيت كلمة المرور؟
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <button 
                type="submit" 
                class="login-btn w-full py-3 px-4 rounded-xl text-white font-bold text-lg shadow-lg"
            >
                <span class="relative z-10">تسجيل الدخول</span>
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-slate-500 text-sm">
                © {{ date('Y') }} مركز مشكاة. جميع الحقوق محفوظة.
            </p>
            <div class="mt-2 flex items-center justify-center gap-4 text-xs text-slate-600">
                <span>نسخة النظام 1.0</span>
                <span>•</span>
                <span>نظام إدارة تحفيظ القرآن الكريم</span>
            </div>
        </div>
    </div>

    <!-- JavaScript for Enhanced Features -->
    <script>


        // Form submission animation
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = `
                <div class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>جاري تسجيل الدخول...</span>
                </div>
            `;
            button.disabled = true;
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.error-message, .success-message');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        // Add typing effect for placeholders
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.placeholder = '';
            function typing() {
                if (i < text.length) {
                    element.placeholder += text.charAt(i);
                    i++;
                    setTimeout(typing, speed);
                }
            }
            typing();
        }

        // Initialize typing effects on page load
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            setTimeout(() => {
                typeWriter(emailInput, 'أدخل بريدك الإلكتروني', 50);
            }, 500);
            
            setTimeout(() => {
                typeWriter(passwordInput, 'أدخل كلمة المرور', 50);
            }, 1500);
        });

        // Add focus glow effect
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.input-field');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>
</html>
