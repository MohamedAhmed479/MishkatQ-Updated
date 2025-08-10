<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'لوحة التحكم' }} — مركز مشكاة</title>
    <!-- Google Fonts: Cairo for professional Arabic typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
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
        /* Custom CSS that can't be replaced by Tailwind */
        .sidebar {
            width: 288px;
            backdrop-filter: blur(20px);
        }
        .card-elegant {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .dark .card-elegant {
            background: #1e293b;
            border-color: rgba(51, 65, 85, 0.8);
        }



        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        .mobile-content {
            animation: slideIn 0.3s ease-out;
        }
        @media (max-width: 1023px) {
            .sidebar { display: none !important; }
            .mobile-menu-btn { display: inline-flex !important; }
        }
        @media (min-width: 1024px) {
            .sidebar { display: block !important; }
            .mobile-menu-btn { display: none; }
        }
        @media (max-width: 1023px) {
            .mr-72 { margin-right: 0 !important; }
        }
        .mobile-sidebar.open { display: block; }
    </style>
</head>
<body class="dark bg-gradient-to-br from-slate-900 to-slate-800 font-sans min-h-screen">
<div class="flex min-h-screen">
    <!-- Desktop Sidebar -->
    <aside class="sidebar fixed top-0 bottom-0 right-0 z-10 w-72 bg-gradient-to-b from-slate-800 to-slate-900 border-l border-slate-700/80 shadow-xl">
        <!-- Logo Area -->
        <div class="p-6 border-b border-slate-700/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-500/30">م</div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-slate-100 hover:text-emerald-400 transition-colors">مركز مشكاة</a>
                <p class="text-xs text-slate-400">لوحة التحكم</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 pr-3">القائمة الرئيسية</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium mb-1 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                </svg>
                اللوحة الرئيسية
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium {{ request()->is('admin/users*') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                المستخدمون
            </a>
            <a href="{{ route('admin.badges.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium {{ request()->is('admin/badges*') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                الشارات
            </a>
            <a href="{{ route('admin.devices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium {{ request()->is('admin/devices*') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                الأجهزة
            </a>
        </nav>

        <!-- Logout Area -->
        <div class="absolute bottom-6 left-4 right-4">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-900/20 text-red-400 hover:bg-red-900/30 transition-all duration-300 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen mr-72">
        <!-- Top Header -->
        <header class="sticky top-0 z-20 bg-slate-800/80 backdrop-blur-xl border-b border-slate-700/60 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn inline-flex items-center justify-center p-2 rounded-xl hover:bg-emerald-900/20 transition-colors" onclick="toggleMobileSidebar()">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <!-- Page Title -->
                <div>
                    <h1 class="text-lg font-bold text-slate-100">@yield('page-title', 'اللوحة الرئيسية')</h1>
                    <p class="text-sm text-slate-400">@yield('page-subtitle', '')</p>
                </div>
            </div>
            <!-- Header Actions -->
            <div class="flex items-center gap-3">
                <!-- Admin Profile -->
                <div class="flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-700/50 hover:bg-emerald-900/20 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-sm">{{ substr(auth('admin')->user()->name ?? 'A', 0, 1) }}</div>
                    <div>
                        <div class="text-sm font-semibold text-slate-100">{{ auth('admin')->user()->name ?? 'المشرف' }}</div>
                        <div class="text-xs text-slate-400">مشرف النظام</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert p-4 rounded-xl mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="alert p-4 rounded-xl mb-6 bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <div class="font-medium mb-2">حدثت الأخطاء التالية:</div>
                            <ul class="space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Mobile Sidebar -->
    <div id="mobileSidebar" class="mobile-sidebar fixed inset-0 z-50 hidden">
        <div class="mobile-overlay absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleMobileSidebar()"></div>
        <aside class="mobile-content absolute top-0 bottom-0 right-0 w-80 bg-gradient-to-b from-slate-800 to-slate-900 border-l border-slate-700/80 shadow-2xl">
            <!-- Mobile Header -->
            <div class="p-6 border-b border-slate-700/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-500/30">م</div>
                    <span class="text-lg font-bold text-slate-100">مركز مشكاة</span>
                </div>
                <button onclick="toggleMobileSidebar()" class="inline-flex items-center justify-center p-2 rounded-xl hover:bg-red-900/20 transition-colors">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav class="p-4">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 pr-3">القائمة الرئيسية</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium mb-1 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}" onclick="toggleMobileSidebar()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                    </svg>
                    اللوحة الرئيسية
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium {{ request()->is('admin/users*') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}" onclick="toggleMobileSidebar()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    المستخدمين
                </a>
            </nav>

            <!-- Mobile Logout -->
            <div class="absolute bottom-6 left-4 right-4">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-900/20 text-red-400 hover:bg-red-900/30 transition-all duration-300 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>

<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        sidebar.classList.toggle('open');
    }

    // Force dark mode always
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
    
    // Ensure dark mode stays active even if someone tries to change it
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (!document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.add('dark');
    }
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
</script>

@stack('scripts')
</body>
</html>
