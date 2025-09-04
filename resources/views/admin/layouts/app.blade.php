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
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
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
            .mr-72 { margin-right: 0 !important; }
        }
        @media (min-width: 1024px) {
            .sidebar { display: block !important; }
            .mobile-menu-btn { display: none; }
        }
        
        /* Mobile Sidebar Improvements */
        .mobile-sidebar.open { display: block; }
        .mobile-sidebar {
            transition: all 0.3s ease-in-out;
        }
        .mobile-content {
            transition: transform 0.3s ease-in-out;
        }
        .mobile-sidebar:not(.open) .mobile-content {
            transform: translateX(100%);
        }
        
        /* Better touch targets for mobile */
        @media (max-width: 768px) {
            .mobile-content {
                width: 100vw;
                max-width: 320px;
            }
            .mobile-nav-item {
                min-height: 48px;
                padding: 12px 16px;
            }
            .mobile-section-header {
                padding: 8px 16px;
                font-size: 0.75rem;
            }
        }
        
        /* Improved mobile navigation */
        .mobile-nav-container {
            max-height: calc(100vh - 140px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Better mobile header */
        @media (max-width: 640px) {
            .mobile-header {
                padding: 1rem;
            }
            .mobile-header h1 {
                font-size: 1.125rem;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 font-sans min-h-screen">
<div class="flex min-h-screen">
    <!-- Desktop Sidebar -->
    <aside class="sidebar fixed top-0 bottom-0 right-0 z-10 w-72 bg-gradient-to-b from-slate-800 to-slate-900 border-l border-slate-700/80 shadow-xl">

        <style>
            .sidebar a,
            .sidebar button {
                cursor: pointer;
            }
        </style>

        <!-- Logo Area -->
        <div class="p-6 border-b border-slate-700/50 flex items-center gap-3">
            <img src="{{ asset('images/logo.svg') }}" alt="شعار مشكاة" class="w-10 h-10 rounded-xl shadow-lg shadow-emerald-500/30" />
            <div>
                @can('dashboard.view')
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-slate-100 hover:text-emerald-400 transition-colors">مركز مشكاة</a>
                @else
                <span class="text-lg font-bold text-slate-100">مركز مشكاة</span>
                @endcan
                <p class="text-xs text-slate-400">لوحة التحكم</p>
            </div>
        </div>

        <!-- Navigation -->
        @php
            $quranActive = request()->is('admin/surahs*') || request()->is('admin/juzs*') || request()->is('admin/verses*') || request()->is('admin/words*') || request()->is('admin/tafsirs*') || request()->is('admin/reciters*') || request()->is('admin/recitations*');
            $memorizationActive = request()->is('admin/memorization-plans*') || request()->is('admin/plan-items*') || request()->is('admin/spaced-repetitions*') || request()->is('admin/review-records*');
            $usersActive = request()->is('admin/users*') || request()->is('admin/devices*') || request()->is('admin/admins*');
            $gamificationActive = request()->is('admin/badges*') || request()->is('admin/leaderboards*') || request()->is('admin/notifications*');
            $systemActive = request()->is('admin/audit-logs*') || request()->is('admin/roles*') || request()->is('admin/permissions*');
        @endphp
        <nav class="p-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 pr-3">القائمة الرئيسية</div>
            @can('dashboard.view')
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium mb-2 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                </svg>
                اللوحة الرئيسية
            </a>
            @endcan

            <!-- Quran Content Section -->
            @if(auth()->user()->can('chapters.view') || auth()->user()->can('juzs.view') || auth()->user()->can('verses.view') || auth()->user()->can('words.view') || auth()->user()->can('tafsirs.view') || auth()->user()->can('reciters.view') || auth()->user()->can('recitations.view'))
            <div class="mt-4" data-section="quran" data-has-active="{{ $quranActive ? '1' : '0' }}">
                <button type="button" id="btn-section-quran" onclick="toggleSection('quran')" class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-700/40 transition-colors {{ $quranActive ? 'text-emerald-400' : 'text-slate-300' }}">
                    <span class="flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        إدارة المحتوى القرآني
                    </span>
                    <svg id="arrow-section-quran" class="w-4 h-4 transition-transform {{ $quranActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul id="section-quran" class="mt-2 space-y-1 {{ $quranActive ? '' : 'hidden' }}">
                    @can('chapters.view')
                    <li>
                        <a href="{{ route('admin.chapters.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/surahs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 016.5 17H20M4 12.5A2.5 2.5 0 016.5 10H20M4 5.5A2.5 2.5 0 016.5 3H20"/></svg>
                            السور
                        </a>
                    </li>
                    @endcan
                    @can('juzs.view')
                    <li>
                        <a href="{{ route('admin.juzs.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/juzs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                            الأجزاء
                        </a>
                    </li>
                    @endcan
                    @can('verses.view')
                    <li>
                        <a href="{{ route('admin.verses.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/verses*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            الآيات
                        </a>
                    </li>
                    @endcan
                    @can('words.view')
                    <li>
                        <a href="{{ route('admin.words.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/words*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m-6 4h18M3 13h12m6-2v2M3 19h18"/></svg>
                            الكلمات
                        </a>
                    </li>
                    @endcan
                    @can('tafsirs.view')
                    <li>
                        <a href="{{ route('admin.tafsirs.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/tafsirs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v7a3 3 0 006 0v-7c0-1.657-1.343-3-3-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 014-4h0a4 4 0 014 4v4"/></svg>
                            التفاسير
                        </a>
                    </li>
                    @endcan
                    @can('reciters.view')
                    <li>
                        <a href="{{ route('admin.reciters.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/reciters*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 018 17h8a4 4 0 012.879 1.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            القراء
                        </a>
                    </li>
                    @endcan
                    @can('recitations.view')
                    <li>
                        <a href="{{ route('admin.recitations.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/recitations*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-2v13M9 19l12-2M9 19L3 17V4l6 2"/></svg>
                            التسجيلات
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @endif

            <!-- Memorization Plans Section -->
            @if(auth()->user()->can('memorization-plans.view') || auth()->user()->can('plan-items.view') || auth()->user()->can('spaced-repetitions.view') || auth()->user()->can('review-records.view'))
            <div class="mt-3" data-section="memorization" data-has-active="{{ $memorizationActive ? '1' : '0' }}">
                <button type="button" id="btn-section-memorization" onclick="toggleSection('memorization')" class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-700/40 transition-colors {{ $memorizationActive ? 'text-emerald-400' : 'text-slate-300' }}">
                    <span class="flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10m-7 6h7"/></svg>
                        إدارة خطط الحفظ
                    </span>
                    <svg id="arrow-section-memorization" class="w-4 h-4 transition-transform {{ $memorizationActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul id="section-memorization" class="mt-2 space-y-1 {{ $memorizationActive ? '' : 'hidden' }}">
                    @can('memorization-plans.view')
                    <li>
                        <a href="{{ route('admin.memorization-plans.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/memorization-plans*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h10"/></svg>
                            خطط الحفظ
                        </a>
                    </li>
                    @endcan
                    @can('plan-items.view')
                    <li>
                        <a href="{{ route('admin.plan-items.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/plan-items*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M4 6h16"/></svg>
                            عناصر الخطة
                        </a>
                    </li>
                    @endcan
                    @can('spaced-repetitions.view')
                    <li>
                        <a href="{{ route('admin.spaced-repetitions.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/spaced-repetitions*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V8H3v11a2 2 0 002 2z"/></svg>
                            مراجعات SRS
                        </a>
                    </li>
                    @endcan
                    @can('review-records.view')
                    <li>
                        <a href="{{ route('admin.review-records.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/review-records*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            تقييمات المراجعة
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @endif

            <!-- Users & Devices Section -->
            @if(auth()->user()->can('users.view') || auth()->user()->can('admins.view') || auth()->user()->can('devices.view'))
            <div class="mt-3" data-section="users" data-has-active="{{ $usersActive ? '1' : '0' }}">
                <button type="button" id="btn-section-users" onclick="toggleSection('users')" class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-700/40 transition-colors {{ $usersActive ? 'text-emerald-400' : 'text-slate-300' }}">
                    <span class="flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1"/></svg>
                        المستخدمون والأجهزة
                    </span>
                    <svg id="arrow-section-users" class="w-4 h-4 transition-transform {{ $usersActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul id="section-users" class="mt-2 space-y-1 {{ $usersActive ? '' : 'hidden' }}">
                    @can('users.view')
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/users*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1"/></svg>
                            المستخدمون
                        </a>
                    </li>
                    @endcan
                    @can('admins.view')
                    <li>
                        <a href="{{ route('admin.admins.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/admins*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0V10m0 10H7m10-10l-2-2m2 2 2-2"/></svg>
                            المشرفون
                        </a>
                    </li>
                    @endcan
                    @can('devices.view')
                    <li>
                        <a href="{{ route('admin.devices.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/devices*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18"/></svg>
                            الأجهزة
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @endif

            <!-- Gamification Section -->
            @if(auth()->user()->can('badges.view') || auth()->user()->can('leaderboards.view') || auth()->user()->can('notifications.view'))
            <div class="mt-3" data-section="gamification" data-has-active="{{ $gamificationActive ? '1' : '0' }}">
                <button type="button" id="btn-section-gamification" onclick="toggleSection('gamification')" class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-700/40 transition-colors {{ $gamificationActive ? 'text-emerald-400' : 'text-slate-300' }}">
                    <span class="flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l5-5 5 5M12 12V3"/></svg>
                        التحفيز والإنجازات
                    </span>
                    <svg id="arrow-section-gamification" class="w-4 h-4 transition-transform {{ $gamificationActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul id="section-gamification" class="mt-2 space-y-1 {{ $gamificationActive ? '' : 'hidden' }}">
                    @can('badges.view')
                    <li>
                        <a href="{{ route('admin.badges.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/badges*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138"/></svg>
                            الشارات
                        </a>
                    </li>
                    @endcan
                    @can('leaderboards.view')
                    <li>
                        <a href="{{ route('admin.leaderboards.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/leaderboards*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3 3 7-7"/></svg>
                            لوحة المتصدرين
                        </a>
                    </li>
                    @endcan
                    @can('notifications.view')
                    <li>
                        <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/notifications*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            التنبيهات
                        </a>
                    </li>
                    @endcan

                </ul>
            </div>
            @endif

            <!-- System Section -->
            @if(auth()->user()->can('audit-logs.view') || auth()->user()->can('roles.view') || auth()->user()->can('permissions.view'))
            <div class="mt-3" data-section="system" data-has-active="{{ $systemActive ? '1' : '0' }}">
                <button type="button" id="btn-section-system" onclick="toggleSection('system')" class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-700/40 transition-colors {{ $systemActive ? 'text-emerald-400' : 'text-slate-300' }}">
                    <span class="flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h8"/></svg>
                        النظام والسجلات
                    </span>
                    <svg id="arrow-section-system" class="w-4 h-4 transition-transform {{ $systemActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul id="section-system" class="mt-2 space-y-1 {{ $systemActive ? '' : 'hidden' }}">
                    @can('audit-logs.view')
                    <li>
                        <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/audit-logs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h8m0 0V3m0 6l-8 8-4-4-6 6"/></svg>
                            سجلات التدقيق
                        </a>
                    </li>
                    @endcan
                    @can('roles.view')
                    <li>
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/roles*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            الأدوار
                        </a>
                    </li>
                    @endcan
                    @can('permissions.view')
                    <li>
                        <a href="{{ route('admin.permissions.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 {{ request()->is('admin/permissions*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            الصلاحيات
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @endif
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
                <!-- Search -->
                <form method="GET" action="{{ url()->current() }}" class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-700/50 focus-within:bg-slate-700/70 transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    <input name="q" value="{{ request('q') }}" type="search" placeholder="ابحث..." class="bg-transparent placeholder-slate-400 text-slate-100 focus:outline-none w-56" />
                </form>

                <!-- Theme Forced Dark (toggle removed) -->



                <!-- Quick link to edit own profile -->
                <a href="{{ route('admin.profile.edit') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-700/50 hover:bg-emerald-900/20 transition-colors" title="تعديل الملف الشخصي">
                    <svg class="w-5 h-5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 13.5V20h6.5l8.036-8.036a2.5 2.5 0 10-3.536-3.536L4 13.5z"/>
                    </svg>
                </a>

                <!-- Admin Profile Dropdown -->
                <div class="relative">
                    <button type="button" onclick="toggleProfileMenu()" class="flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-700/50 hover:bg-emerald-900/20 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-sm">{{ substr(auth('admin')->user()->name ?? 'A', 0, 1) }}</div>
                        <div class="hidden sm:block text-right">
                            <div class="text-sm font-semibold text-slate-100">{{ auth('admin')->user()->name ?? 'المشرف' }}</div>
                            <div class="text-xs text-slate-400">مشرف النظام</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="profileMenu" class="absolute left-0 top-12 w-48 rounded-xl bg-slate-800 border border-slate-700 shadow-xl p-2 hidden">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700/60">اللوحة الرئيسية</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-right px-3 py-2 rounded-lg text-red-400 hover:bg-red-900/20">تسجيل الخروج</button>
                        </form>
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
        <aside class="mobile-content absolute top-0 bottom-0 right-0 w-80 max-w-[90vw] bg-gradient-to-b from-slate-800 to-slate-900 border-l border-slate-700/80 shadow-2xl flex flex-col">
            <!-- Mobile Header -->
            <div class="mobile-header p-4 sm:p-6 border-b border-slate-700/50 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="شعار مشكاة" class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl shadow-lg shadow-emerald-500/30" />
                    <span class="text-base sm:text-lg font-bold text-slate-100">مركز مشكاة</span>
                </div>
                <button onclick="toggleMobileSidebar()" class="inline-flex items-center justify-center p-2 rounded-xl hover:bg-red-900/20 transition-colors touch-manipulation">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav class="mobile-nav-container flex-1 overflow-y-auto p-4">
                <div class="mobile-section-header text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 pr-3">القائمة الرئيسية</div>
                @can('dashboard.view')
                <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 transition-all duration-300 font-medium mb-1 touch-manipulation {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-900/30 text-emerald-400 shadow-sm' : '' }}" onclick="toggleMobileSidebar()">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"/></svg>
                    <span class="text-sm sm:text-base">اللوحة الرئيسية</span>
                </a>
                @endcan

                @if(auth()->user()->can('chapters.view') || auth()->user()->can('juzs.view') || auth()->user()->can('verses.view') || auth()->user()->can('words.view') || auth()->user()->can('tafsirs.view') || auth()->user()->can('reciters.view') || auth()->user()->can('recitations.view'))
                <div class="mt-4">
                    <div class="mobile-section-header px-4 py-2 text-slate-400 text-xs">إدارة المحتوى القرآني</div>
                    <div class="space-y-1">
                        @can('chapters.view')
                        <a href="{{ route('admin.chapters.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/surahs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 016.5 17H20M4 12.5A2.5 2.5 0 016.5 10H20M4 5.5A2.5 2.5 0 016.5 3H20"/></svg>
                            <span class="text-sm sm:text-base">السور</span>
                        </a>
                        @endcan
                        @can('juzs.view')
                        <a href="{{ route('admin.juzs.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/juzs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                            <span class="text-sm sm:text-base">الأجزاء</span>
                        </a>
                        @endcan
                        @can('verses.view')
                        <a href="{{ route('admin.verses.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/verses*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-sm sm:text-base">الآيات</span>
                        </a>
                        @endcan
                        @can('words.view')
                        <a href="{{ route('admin.words.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/words*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m-6 4h18M3 13h12m6-2v2M3 19h18"/></svg>
                            <span class="text-sm sm:text-base">الكلمات</span>
                        </a>
                        @endcan
                        @can('tafsirs.view')
                        <a href="{{ route('admin.tafsirs.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/tafsirs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v7a3 3 0 006 0v-7c0-1.657-1.343-3-3-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 014-4h0a4 4 0 014 4v4"/></svg>
                            <span class="text-sm sm:text-base">التفاسير</span>
                        </a>
                        @endcan
                        @can('reciters.view')
                        <a href="{{ route('admin.reciters.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/reciters*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 018 17h8a4 4 0 012.879 1.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-sm sm:text-base">القراء</span>
                        </a>
                        @endcan
                        @can('recitations.view')
                        <a href="{{ route('admin.recitations.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/recitations*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-2v13M9 19l12-2M9 19L3 17V4l6 2"/></svg>
                            <span class="text-sm sm:text-base">التسجيلات</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif

                @if(auth()->user()->can('memorization-plans.view') || auth()->user()->can('plan-items.view') || auth()->user()->can('spaced-repetitions.view') || auth()->user()->can('review-records.view'))
                <div class="mt-4">
                    <div class="mobile-section-header px-4 py-2 text-slate-400 text-xs">خطط الحفظ</div>
                    <div class="space-y-1">
                        @can('memorization-plans.view')
                        <a href="{{ route('admin.memorization-plans.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/memorization-plans*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h10"/></svg>
                            <span class="text-sm sm:text-base">خطط الحفظ</span>
                        </a>
                        @endcan
                        @can('plan-items.view')
                        <a href="{{ route('admin.plan-items.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/plan-items*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M4 6h16"/></svg>
                            <span class="text-sm sm:text-base">عناصر الخطة</span>
                        </a>
                        @endcan
                        @can('spaced-repetitions.view')
                        <a href="{{ route('admin.spaced-repetitions.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/spaced-repetitions*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V8H3v11a2 2 0 002 2z"/></svg>
                            <span class="text-sm sm:text-base">مراجعات SRS</span>
                        </a>
                        @endcan
                        @can('review-records.view')
                        <a href="{{ route('admin.review-records.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/review-records*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm sm:text-base">تقييمات المراجعة</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif

                @if(auth()->user()->can('users.view') || auth()->user()->can('admins.view') || auth()->user()->can('devices.view'))
                <div class="mt-4">
                    <div class="mobile-section-header px-4 py-2 text-slate-400 text-xs">المستخدمون والأجهزة</div>
                    <div class="space-y-1">
                        @can('users.view')
                        <a href="{{ route('admin.users.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/users*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1"/></svg>
                            <span class="text-sm sm:text-base">المستخدمون</span>
                        </a>
                        @endcan
                        @can('devices.view')
                        <a href="{{ route('admin.devices.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/devices*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18"/></svg>
                            <span class="text-sm sm:text-base">الأجهزة</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif

                @if(auth()->user()->can('badges.view') || auth()->user()->can('leaderboards.view') || auth()->user()->can('notifications.view'))
                <div class="mt-4">
                    <div class="mobile-section-header px-4 py-2 text-slate-400 text-xs">التحفيز والإنجازات</div>
                    <div class="space-y-1">
                        @can('badges.view')
                        <a href="{{ route('admin.badges.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/badges*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138"/></svg>
                            <span class="text-sm sm:text-base">الشارات</span>
                        </a>
                        @endcan
                        @can('leaderboards.view')
                        <a href="{{ route('admin.leaderboards.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/leaderboards*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3 3 7-7"/></svg>
                            <span class="text-sm sm:text-base">لوحة المتصدرين</span>
                        </a>
                        @endcan
                        @can('notifications.view')
                        <a href="{{ route('admin.notifications.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/notifications*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="text-sm sm:text-base">التنبيهات</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif

                @if(auth()->user()->can('audit-logs.view') || auth()->user()->can('roles.view') || auth()->user()->can('permissions.view'))
                <div class="mt-4">
                    <div class="mobile-section-header px-4 py-2 text-slate-400 text-xs">النظام والسجلات</div>
                    <div class="space-y-1">
                        @can('audit-logs.view')
                        <a href="{{ route('admin.audit-logs.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/audit-logs*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h8m0 0V3m0 6l-8 8-4-4-6 6"/></svg>
                            <span class="text-sm sm:text-base">سجلات التدقيق</span>
                        </a>
                        @endcan
                        @can('roles.view')
                        <a href="{{ route('admin.roles.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/roles*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-sm sm:text-base">الأدوار</span>
                        </a>
                        @endcan
                        @can('permissions.view')
                        <a href="{{ route('admin.permissions.index') }}" class="mobile-nav-item flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-emerald-900/20 hover:text-emerald-400 touch-manipulation {{ request()->is('admin/permissions*') ? 'bg-emerald-900/30 text-emerald-400' : '' }}" onclick="toggleMobileSidebar()">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm sm:text-base">الصلاحيات</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif
            </nav>

            <!-- Mobile Logout -->
            <div class="flex-shrink-0 p-4 border-t border-slate-700/50">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-900/20 text-red-400 hover:bg-red-900/30 transition-all duration-300 font-medium touch-manipulation">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="text-sm sm:text-base">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>

<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const body = document.body;
        
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            body.style.overflow = '';
        } else {
            sidebar.classList.add('open');
            body.style.overflow = 'hidden';
        }
    }
    
    // Close mobile sidebar when clicking outside
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('mobileSidebar');
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        
        if (sidebar.classList.contains('open') && 
            !sidebar.contains(event.target) && 
            !mobileMenuBtn.contains(event.target)) {
            toggleMobileSidebar();
        }
    });
    
    // Close mobile sidebar on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const sidebar = document.getElementById('mobileSidebar');
            if (sidebar.classList.contains('open')) {
                toggleMobileSidebar();
            }
        }
    });

    // Collapsible sidebar sections with localStorage
    const STORAGE_KEY = 'mishkat_admin_sidebar_sections';
    function getSectionState() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
        } catch (e) {
            return {};
        }
    }
    function setSectionState(state) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    }
    function toggleSection(sectionId) {
        const list = document.getElementById(`section-${sectionId}`);
        const arrow = document.getElementById(`arrow-section-${sectionId}`);
        if (!list || !arrow) return;
        list.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
        const state = getSectionState();
        state[sectionId] = !list.classList.contains('hidden');
        setSectionState(state);
    }
    function restoreSections() {
        const state = getSectionState();
        document.querySelectorAll('[data-section]').forEach(function(wrapper) {
            const id = wrapper.getAttribute('data-section');
            const hasActive = wrapper.getAttribute('data-has-active') === '1';
            const list = document.getElementById(`section-${id}`);
            const arrow = document.getElementById(`arrow-section-${id}`);
            if (!list || !arrow) return;
            const shouldOpen = state[id] ?? hasActive;
            if (shouldOpen) {
                list.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                list.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });
    }
    document.addEventListener('DOMContentLoaded', restoreSections);

    // Force dark theme only
    const THEME_KEY = 'theme';
    function initTheme() {
        document.documentElement.classList.add('dark');
        localStorage.setItem(THEME_KEY, 'dark');
    }
    document.addEventListener('DOMContentLoaded', initTheme);
</script>

@stack('scripts')
</body>
</html>
