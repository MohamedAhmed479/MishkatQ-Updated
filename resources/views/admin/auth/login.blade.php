<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول — مركز مشكاة</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        body { font-family: 'Cairo', sans-serif; }
        @keyframes subtleFloat { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-6px) } }
        @keyframes fadeSlide { 0% {opacity: 0; transform: translateY(10px)} 100% {opacity: 1; transform: translateY(0)} }
    </style>
</head>
<body class="min-h-screen bg-slate-900 relative overflow-hidden text-slate-100">

    <!-- خلفية متدرجة مع دوائر مضيئة -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-emerald-500/20 blur-3xl animate-[subtleFloat_8s_ease-in-out_infinite]"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-cyan-500/20 blur-3xl animate-[subtleFloat_10s_ease-in-out_infinite]"></div>

    <div class="container mx-auto px-4 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-4xl grid lg:grid-cols-2 gap-8 animate-[fadeSlide_1s_ease-out]">
            
            <!-- اللوحة الترحيبية -->
            <div class="hidden lg:flex flex-col justify-center p-8">
                <div class="inline-flex items-center gap-3 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-emerald-500/30">م</div>
                    <div>
                        <div class="text-2xl font-bold">مركز مشكاة</div>
                        <div class="text-sm text-slate-400">لوحة التحكم — وصول آمن وموثوق</div>
                    </div>
                </div>

                <h1 class="text-3xl font-black tracking-tight mb-4">أهلاً بك في لوحة التحكم</h1>
                <p class="text-slate-300 leading-8">
                    قم بإدارة المحتوى القرآني، المستخدمين، والأداء العام للنظام من خلال تجربة حديثة ومتناسقة. أمانك أولويتنا.
                </p>

                <div class="mt-10 grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/60">
                        <div class="text-emerald-400 font-bold text-xl">سريع</div>
                        <div class="text-slate-400 text-xs mt-1">أداء محسّن</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/60">
                        <div class="text-emerald-400 font-bold text-xl">آمن</div>
                        <div class="text-slate-400 text-xs mt-1">جلسات محمية</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/60">
                        <div class="text-emerald-400 font-bold text-xl">حديث</div>
                        <div class="text-slate-400 text-xs mt-1">تصميم أنيق</div>
                    </div>
                </div>
            </div>

            <!-- بطاقة تسجيل الدخول -->
            <div class="flex items-center justify-center">
                <div class="login-card w-full max-w-md rounded-3xl p-8 border border-white/10 bg-white/5 backdrop-blur-2xl shadow-2xl">
                    
                    <!-- الشعار العائم -->
                    <div class="absolute -top-6 -start-6 w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-500/30 animate-[subtleFloat_6s_ease-in-out_infinite]">م</div>

                    <div class="mb-6 pt-2">
                        <h2 class="text-2xl font-extrabold">تسجيل الدخول</h2>
                        <p class="text-sm text-slate-400 mt-1">ادخل بيانات حساب المشرف للوصول إلى اللوحة</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-5 rounded-xl border border-red-800/50 bg-red-900/30 text-red-200 text-sm p-3">
                            <div class="font-semibold mb-1">تعذر تسجيل الدخول</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-1">البريد الإلكتروني</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                class="w-full rounded-xl border border-slate-700/60 bg-slate-900/60 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 px-4 py-3 transition"
                                placeholder="name@example.com" />
                            @error('email')
                                <div class="text-red-300 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-1">كلمة المرور</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required
                                    class="w-full rounded-xl border border-slate-700/60 bg-slate-900/60 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 px-4 py-3 pr-12 transition"
                                    placeholder="••••••••" />
                                <button type="button" id="togglePassword" class="absolute inset-y-0 left-2 px-2 text-slate-400 hover:text-slate-200 cursor-pointer" aria-label="إظهار/إخفاء كلمة المرور">
                                    <svg id="iconEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-red-300 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="inline-flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
                            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                                class="rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500/60" />
                            <span>تذكرني</span>
                        </label>

                        <button type="submit"
                            class="w-full rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-500 text-white font-bold py-3 shadow-lg shadow-emerald-600/20 transition-transform hover:scale-[1.02] active:scale-[.99] flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 11c0-1.657 0-2.486.293-3.121a3 3 0 011.586-1.586C14.514 6 15.343 6 17 6h1a3 3 0 013 3v6a3 3 0 01-3 3h-1c-1.657 0-2.486 0-3.121-.293a3 3 0 01-1.586-1.586C12 15.486 12 14.657 12 13M8 17H7a3 3 0 01-3-3V8a3 3 0 013-3h1" />
                            </svg>
                            تسجيل الدخول
                        </button>
                    </form>

                    <div class="pt-6 text-center text-xs text-slate-500">
                        بدخولك توافق على سياسات مركز مشكاة. جميع الحقوق محفوظة.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // إظهار/إخفاء كلمة المرور
        document.getElementById('togglePassword')?.addEventListener('click', () => {
            const input = document.getElementById('password');
            const eyePath = document.getElementById('eyePath');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            if (eyePath) eyePath.setAttribute('opacity', isText ? '1' : '0.5');
        });
    </script>
</body>
</html>
