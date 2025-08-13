@extends('admin.layouts.app')

@section('page-title', 'لوحة التحكم الذكية')
@section('page-subtitle', 'نظرة شاملة على منصة تحفيظ القرآن الكريم')

@section('content')
<div class="space-y-8">
    <!-- Hero Analytics Section -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 p-6 lg:p-8 text-white shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-black/20 to-transparent"></div>
        <div class="absolute top-0 right-0 w-64 h-64 lg:w-96 lg:h-96 bg-white/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 lg:w-96 lg:h-96 bg-white/5 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>

        <div class="relative z-10">
            <!-- Header -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-6 lg:mb-8">
                <div class="flex items-center gap-3 lg:gap-4 mb-4 lg:mb-0">
                    <div class="h-16 w-16 lg:h-20 lg:w-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="h-8 w-8 lg:h-10 lg:w-10" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-4xl font-bold text-white">مرحباً بك في المنصة الذكية</h1>
                        <p class="text-lg lg:text-xl text-white/80 mt-1 lg:mt-2">نظام متطور لإدارة تحفيظ القرآن الكريم</p>
                    </div>
                </div>

                <div class="text-right text-white/90 text-sm">
                    <div class="mb-1">📅 {{ now()->format('l, F j, Y') }}</div>
                    <div>⏰ آخر تحديث: {{ now()->format('g:i A') }}</div>
                </div>
            </div>

            <!-- Hero KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
                @foreach(($kpis ?? []) as $index => $kpi)
                @if($index < 4)
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 lg:p-4 border border-white/20 hover:bg-white/20 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-lg lg:text-2xl font-bold">{{ $kpi['value'] ?? '—' }}</div>
                            <div class="text-xs lg:text-sm text-white/80">{{ $kpi['label'] ?? '—' }}</div>
                        </div>
                        <div class="text-xl lg:text-3xl">
                            @if($index === 0) 👥
                            @elseif($index === 1) 📖
                            @elseif($index === 2) 📈
                            @else 🔄
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- Filters & Actions -->
            <div class="flex flex-col sm:flex-row gap-3 items-center justify-center">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <select name="range" class="w-full sm:w-auto px-3 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm focus:outline-none focus:ring-2 focus:ring-white/50">
                        @foreach(($filterOptions['ranges'] ?? []) as $key => $label)
                        <option value="{{ $key }}" {{ ($filters['range'] ?? 'last_30_days') === $key ? 'selected' : '' }} style="color: #1f2937;">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white/20 backdrop-blur-sm px-4 lg:px-6 py-2 lg:py-3 text-sm text-white hover:bg-white/30 transition-all duration-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        تحديث البيانات
                    </button>
                </form>

                <a href="{{ route('admin.dashboard.export-reviews') }}?range={{ $filters['range'] ?? 'last_30_days' }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white/20 backdrop-blur-sm px-4 lg:px-6 py-2 lg:py-3 text-sm text-white hover:bg-white/30 transition-all duration-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    تصدير التقرير
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 animate-fade-in">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-emerald-800 dark:text-emerald-200">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 animate-fade-in">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-red-800 dark:text-red-200">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">
        <!-- Today's Reviews -->
        <div class="group bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-4 lg:p-6 border border-blue-200 dark:border-blue-800 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-blue-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 lg:h-6 lg:w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">اليوم</span>
            </div>
            <div class="space-y-2">
                <div class="text-xl lg:text-2xl font-bold text-blue-900 dark:text-blue-100">
                    {{ \App\Models\ReviewRecord::whereDate('review_date', today())->count() }}
                </div>
                <div class="text-sm text-blue-700 dark:text-blue-300">مراجعات اليوم</div>
                <div class="text-xs text-blue-600 dark:text-blue-400">
                    +{{ \App\Models\ReviewRecord::whereDate('review_date', today())->where('successful', true)->count() }} ناجحة
                </div>
            </div>
        </div>

        <!-- Average Session Duration -->
        <div class="group bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-2xl p-4 lg:p-6 border border-emerald-200 dark:border-emerald-800 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-emerald-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 lg:h-6 lg:w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 px-2 py-1 rounded-full">متوسط</span>
            </div>
            <div class="space-y-2">
                <div class="text-xl lg:text-2xl font-bold text-emerald-900 dark:text-emerald-100">
                    25 دقيقة
                </div>
                <div class="text-sm text-emerald-700 dark:text-emerald-300">متوسط مدة الجلسة</div>
                <div class="text-xs text-emerald-600 dark:text-emerald-400">آخر 30 يوم</div>
            </div>
        </div>

        <!-- Performance Improvement -->
        <div class="group bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-2xl p-4 lg:p-6 border border-purple-200 dark:border-purple-800 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-purple-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 lg:h-6 lg:w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full">تقدم</span>
            </div>
            <div class="space-y-2">
                <div class="text-xl lg:text-2xl font-bold text-purple-900 dark:text-purple-100">
                    +{{ number_format(\App\Models\ReviewRecord::where('review_date', '>=', now()->subWeek())->avg('performance_rating') ?? 0, 1) }}%
                </div>
                <div class="text-sm text-purple-700 dark:text-purple-300">تحسن الأداء</div>
                <div class="text-xs text-purple-600 dark:text-purple-400">هذا الأسبوع</div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="group bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl p-4 lg:p-6 border border-amber-200 dark:border-amber-800 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 lg:h-6 lg:w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <span class="text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 px-2 py-1 rounded-full">تقييم</span>
            </div>
            <div class="space-y-2">
                <div class="text-xl lg:text-2xl font-bold text-amber-900 dark:text-amber-100">
                    {{ number_format(\App\Models\ReviewRecord::avg('performance_rating') ?? 4.5, 1) }}/5
                </div>
                <div class="text-sm text-amber-700 dark:text-amber-300">متوسط التقييم</div>
                <div class="text-xs text-amber-600 dark:text-amber-400">جميع المراجعات</div>
            </div>
        </div>
    </div>

    <!-- Leaderboard Section -->
    @if(count($leaderboardTop ?? []) > 0)
    <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 border border-amber-200 dark:border-amber-800 p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-amber-900 dark:text-amber-100">🏆 لوحة الصدارة</h3>
                    <p class="text-sm text-amber-700 dark:text-amber-300">أفضل الطلاب المتفوقين</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <input type="hidden" name="range" value="{{ $filters['range'] ?? 'last_30_days' }}">
                <select name="lb_period" class="px-3 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/30 border border-amber-300 dark:border-amber-700 text-amber-800 dark:text-amber-200 text-sm">
                    <option value="daily" {{ ($lbPeriod ?? 'monthly') === 'daily' ? 'selected' : '' }}>يومي</option>
                    <option value="monthly" {{ ($lbPeriod ?? 'monthly') === 'monthly' ? 'selected' : '' }}>شهري</option>
                    <option value="yearly" {{ ($lbPeriod ?? 'monthly') === 'yearly' ? 'selected' : '' }}>سنوي</option>
                </select>
                <button type="submit" class="px-3 py-1 rounded-lg bg-amber-600 text-white text-xs hover:bg-amber-700 cursor-pointer">تحديث</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @php $medals = ['🥇', '🥈', '🥉']; @endphp
            @foreach($leaderboardTop as $index => $leader)
            <div class="rounded-xl border border-amber-200 dark:border-amber-700 p-4 bg-gradient-to-br from-amber-50 to-white dark:from-amber-800 dark:to-amber-900 hover:shadow-lg transition-all duration-300 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-200 dark:bg-amber-700 flex items-center justify-center text-lg">
                            {{ $medals[$index] ?? $leader['rank'] }}
                        </div>
                        <div>
                            <div class="font-semibold text-amber-900 dark:text-amber-100">{{ $leader['name'] }}</div>
                            <div class="text-xs text-amber-600 dark:text-amber-400">الرتبة: {{ $leader['rank'] }}</div>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <span class="text-amber-600 dark:text-amber-400 text-xs">النقاط</span>
                        <span class="px-2 py-1 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 font-semibold">{{ number_format($leader['points']) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Advanced Performance Analytics -->
    <div class="space-y-8">
        <!-- Section Header -->
        <div class="text-center">
            <div class="inline-flex items-center gap-3 mb-4">
                <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-xl animate-pulse">
                    <svg class="h-9 w-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                📊 تحليلات الأداء المتقدمة
            </h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto text-lg">
                نظرة شاملة ومعمقة على أداء المنصة وسلوك المستخدمين مبنية على البيانات الحقيقية
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full mx-auto mt-4"></div>
        </div>

        <!-- Main Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6">
            <!-- Activity Patterns Card -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-transparent group-hover:from-blue-300/30 transition-all duration-300"></div>
                <div class="absolute top-4 right-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300">
                    <svg class="h-20 w-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold mb-1 group-hover:scale-110 transition-transform duration-300">{{ $analytics['mostActiveHour'] }}:00</div>
                            <div class="text-white/80 text-sm">أكثر ساعة نشاطاً</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center group-hover:translate-x-1 transition-transform duration-300">
                            <span class="text-white/90">عدد المراجعات</span>
                            <span class="font-bold text-lg">{{ $analytics['mostActiveHourCount'] }}</span>
                        </div>
                        <div class="h-px bg-white/20 group-hover:bg-white/40 transition-colors duration-300"></div>
                        <div class="flex justify-between items-center group-hover:translate-x-1 transition-transform duration-300">
                            <span class="text-white/90">أكثر يوم نشاطاً</span>
                            <span class="font-semibold">{{ $analytics['mostActiveDay'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-white/30 to-white/10 group-hover:h-2 transition-all duration-300"></div>
            </div>

            <!-- Performance Trends Card -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-400/20 to-transparent group-hover:from-emerald-300/30 transition-all duration-300"></div>
                <div class="absolute top-4 right-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300">
                    <svg class="h-20 w-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="m1 21 8-8 4 4 8-8v8h-8v-4l-4 4-4-4v8z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold mb-1 group-hover:scale-110 transition-transform duration-300">{{ $analytics['currentSuccessRate'] }}%</div>
                            <div class="text-white/80 text-sm">معدل النجاح</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center group-hover:translate-x-1 transition-transform duration-300">
                            <span class="text-white/90">التغيير</span>
                            <span class="font-bold text-lg {{ $analytics['successRateTrend'] >= 0 ? 'text-emerald-200' : 'text-red-200' }}">
                                {{ $analytics['successRateTrend'] >= 0 ? '+' : '' }}{{ $analytics['successRateTrend'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden group-hover:h-3 transition-all duration-300">
                            <div class="bg-white h-full rounded-full transition-all duration-1000 group-hover:bg-emerald-200" style="width: {{ $analytics['currentSuccessRate'] }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-white/30 to-white/10 group-hover:h-2 transition-all duration-300"></div>
            </div>

            <!-- Student Engagement Card -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-transparent group-hover:from-purple-300/30 transition-all duration-300"></div>
                <div class="absolute top-4 right-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300">
                    <svg class="h-20 w-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63c-.34-1.02-1.31-1.73-2.39-1.73-.37 0-.73.08-1.06.23l-2.84 1.27c-.58.26-.91.89-.67 1.5l.84 2.14-2.87 3.03L5.1 12.8c-.58-.46-1.42-.35-1.88.23-.46.58-.35 1.42.23 1.88l7.31 5.81c.29.23.65.35 1.02.35.22 0 .44-.05.65-.14l2.9-1.29c.47-.21.77-.67.77-1.18V15h2v7h-4z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold mb-1 group-hover:scale-110 transition-transform duration-300">{{ $analytics['engagementRate'] }}%</div>
                            <div class="text-white/80 text-sm">معدل المشاركة</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center group-hover:translate-x-1 transition-transform duration-300">
                            <span class="text-white/90">متوسط يومي</span>
                            <span class="font-bold text-lg">{{ $analytics['dailyAverage'] }}</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden group-hover:h-3 transition-all duration-300">
                            <div class="bg-white h-full rounded-full transition-all duration-1000 group-hover:bg-purple-200" style="width: {{ $analytics['engagementRate'] }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-white/30 to-white/10 group-hover:h-2 transition-all duration-300"></div>
            </div>

            <!-- Total Reviews Card -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-400/20 to-transparent group-hover:from-amber-300/30 transition-all duration-300"></div>
                <div class="absolute top-4 right-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300">
                    <svg class="h-20 w-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold mb-1 group-hover:scale-110 transition-transform duration-300">{{ number_format($analytics['totalReviews']) }}</div>
                            <div class="text-white/80 text-sm">إجمالي المراجعات</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center group-hover:translate-x-1 transition-transform duration-300">
                            <span class="text-white/90">معدل يومي</span>
                            <span class="font-bold text-lg">{{ $analytics['dailyAverage'] }}</span>
                        </div>
                        <div class="flex items-center gap-2 group-hover:translate-x-1 transition-transform duration-300">
                            <div class="h-2 w-2 rounded-full bg-white animate-pulse group-hover:scale-150 transition-transform duration-300"></div>
                            <span class="text-white/90 text-sm">نشاط مستمر</span>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-white/30 to-white/10 group-hover:h-2 transition-all duration-300"></div>
            </div>
        </div>

        <!-- Top Chapters Performance -->
        <div class="rounded-3xl bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-slate-900 dark:via-slate-800 dark:to-indigo-900 border border-slate-200 dark:border-slate-700 p-8 shadow-2xl backdrop-blur-sm">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 flex items-center justify-center shadow-xl group-hover:rotate-6 transition-transform duration-300">
                        <svg class="h-9 w-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">📖 أكثر السور حفظاً</h3>
                        <p class="text-slate-600 dark:text-slate-400">ترتيب السور حسب عدد الآيات المحفوظة مع نسب الإنجاز</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-slate-500 dark:text-slate-400">إجمالي السور</div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ count($analytics['topChapters']) }}</div>
                </div>
            </div>

            @if(count($analytics['topChapters']) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($analytics['topChapters'] as $index => $chapter)
                @php
                    $colors = [
                        ['from' => 'from-amber-400', 'to' => 'to-orange-500', 'bg' => 'from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20', 'border' => 'border-amber-200 dark:border-amber-700', 'text' => 'text-amber-800 dark:text-amber-200'],
                        ['from' => 'from-gray-400', 'to' => 'to-slate-500', 'bg' => 'from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20', 'border' => 'border-gray-200 dark:border-gray-700', 'text' => 'text-gray-800 dark:text-gray-200'],
                        ['from' => 'from-red-400', 'to' => 'to-pink-500', 'bg' => 'from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20', 'border' => 'border-red-200 dark:border-red-700', 'text' => 'text-red-800 dark:text-red-200'],
                        ['from' => 'from-blue-400', 'to' => 'to-indigo-500', 'bg' => 'from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20', 'border' => 'border-blue-200 dark:border-blue-700', 'text' => 'text-blue-800 dark:text-blue-200'],
                        ['from' => 'from-purple-400', 'to' => 'to-violet-500', 'bg' => 'from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20', 'border' => 'border-purple-200 dark:border-purple-700', 'text' => 'text-purple-800 dark:text-purple-200']
                    ];
                    $color = $colors[$index % count($colors)];
                @endphp

                <div class="relative group rounded-2xl bg-gradient-to-br {{ $color['bg'] }} border {{ $color['border'] }} p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 cursor-pointer backdrop-blur-sm">
                    <!-- Rank Badge -->
                    <div class="absolute -top-4 -right-4 z-10">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br {{ $color['from'] }} {{ $color['to'] }} text-white font-bold flex items-center justify-center shadow-xl text-lg transform group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                            @if($index === 0) 🥇
                            @elseif($index === 1) 🥈
                            @elseif($index === 2) 🥉
                            @else {{ $index + 1 }}
                            @endif
                        </div>
                    </div>

                    <!-- Chapter Info -->
                    <div class="pt-4 mb-6">
                        <h4 class="text-xl font-bold {{ $color['text'] }} mb-3 group-hover:text-opacity-90 transition-colors group-hover:translate-x-1">{{ $chapter['name'] }}</h4>
                        <div class="flex items-center justify-between group-hover:translate-x-1 transition-transform duration-300">
                            <span class="text-sm {{ $color['text'] }} opacity-80 font-medium">{{ number_format($chapter['verses']) }} آية محفوظة</span>
                            <span class="text-2xl font-bold {{ $color['text'] }} group-hover:scale-110 transition-transform duration-300">{{ $chapter['progress'] }}%</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="relative mb-4">
                        <div class="w-full bg-white/40 dark:bg-slate-800/40 rounded-full h-4 overflow-hidden shadow-inner group-hover:h-5 transition-all duration-300">
                            <div class="bg-gradient-to-r {{ $color['from'] }} {{ $color['to'] }} h-full rounded-full transition-all duration-1000 ease-out transform group-hover:scale-x-105 group-hover:shadow-lg" style="width: {{ min(100, $chapter['progress']) }}%"></div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-bold text-white drop-shadow-lg group-hover:text-sm transition-all duration-300">{{ $chapter['progress'] }}% مكتمل</span>
                        </div>
                    </div>

                    <!-- Additional Stats -->
                    <div class="pt-4 border-t border-white/30 dark:border-slate-700/30 group-hover:border-white/50 transition-colors duration-300">
                        <div class="flex justify-between items-center text-sm {{ $color['text'] }} opacity-75 group-hover:opacity-90 group-hover:translate-x-1 transition-all duration-300">
                            <span class="font-medium">المركز: {{ $index + 1 }}</span>
                            <span class="font-medium">{{ number_format($chapter['verses']) }} آية</span>
                        </div>
                    </div>

                    <!-- Hover Glow Effect -->
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br {{ $color['from'] }} {{ $color['to'] }} opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-20">
                <div class="mx-auto h-32 w-32 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 flex items-center justify-center mb-8 shadow-xl animate-pulse">
                    <svg class="h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-600 dark:text-slate-400 mb-4">لا توجد بيانات حفظ متاحة</h3>
                <p class="text-slate-500 dark:text-slate-500 max-w-md mx-auto leading-relaxed">ابدأ بإضافة خطط الحفظ لرؤية الإحصائيات والتحليلات المفصلة هنا</p>
                <div class="mt-6">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        إضافة خطة حفظ جديدة
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Advanced KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach(($kpis ?? []) as $index => $kpi)
        @php
            $colorClasses = [
                ['bg' => 'from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20', 'border' => 'border-blue-200 dark:border-blue-800', 'icon_bg' => 'bg-blue-500/20', 'text' => 'text-blue-600', 'from' => 'from-blue-500', 'to' => 'to-indigo-500'],
                ['bg' => 'from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20', 'border' => 'border-emerald-200 dark:border-emerald-800', 'icon_bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-600', 'from' => 'from-emerald-500', 'to' => 'to-green-500'],
                ['bg' => 'from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20', 'border' => 'border-purple-200 dark:border-purple-800', 'icon_bg' => 'bg-purple-500/20', 'text' => 'text-purple-600', 'from' => 'from-purple-500', 'to' => 'to-violet-500'],
                ['bg' => 'from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20', 'border' => 'border-amber-200 dark:border-amber-800', 'icon_bg' => 'bg-amber-500/20', 'text' => 'text-amber-600', 'from' => 'from-amber-500', 'to' => 'to-orange-500']
            ];
            $color = $colorClasses[$index % count($colorClasses)];

            $icons = [
                '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>',
                '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
                '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
            ];
            $icon = $icons[$index % count($icons)];
        @endphp

        <div class="group relative overflow-hidden rounded-2xl {{ $color['bg'] }} border border-slate-200 dark:border-slate-700 p-6 transition-all duration-300 hover:shadow-xl hover:scale-105 cursor-pointer shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-white/50 to-transparent dark:from-white/5 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $color['icon_bg'] }} {{ $color['text'] }}">
                        {!! $icon !!}
                    </div>
                    @if(isset($kpi['delta']))
                    <div class="flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium {{ $kpi['delta'] >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                        @if($kpi['delta'] >= 0)
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"/>
                        </svg>
                        @else
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"/>
                        </svg>
                        @endif
                        {{ abs((float)$kpi['delta']) }}%
                    </div>
                    @endif
                </div>

                <div class="space-y-2">
                    <div class="text-2xl font-bold text-slate-900 dark:text-slate-100 group-hover:scale-105 transition-transform duration-300">
                        {{ number_format((float)$kpi['value']) }}{{ $kpi['unit'] ?? '' }}
                    </div>
                    <div class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $kpi['label'] }}</div>
                    @if(isset($kpi['description']))
                    <div class="text-xs text-slate-500 dark:text-slate-500">{{ $kpi['description'] }}</div>
                    @endif
                </div>

                @if(isset($kpi['progress']))
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 mb-1">
                        <span>التقدم</span>
                        <span>{{ $kpi['progress'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r {{ $color['from'] }} {{ $color['to'] }} rounded-full transition-all duration-500 animate-pulse" style="width: {{ $kpi['progress'] }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Interactive Charts Section -->
    @if(!empty($charts) && count($charts) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($charts as $chart)
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $chart['title'] ?? 'مخطط بياني' }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $chart['description'] ?? 'عرض البيانات الإحصائية' }}</p>
                </div>
                @if(isset($chart['period']) && !empty($chart['period']))
                <span class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 px-2 py-1 rounded-full">{{ $chart['period'] }}</span>
                @endif
            </div>

            <div class="relative h-64">
                <canvas id="{{ $chart['id'] ?? 'chart-' . $loop->index }}" class="w-full h-full"></canvas>
            </div>

            @if(isset($chart['summary']) && is_array($chart['summary']) && count($chart['summary']) > 0)
            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                <div class="grid grid-cols-3 gap-4 text-center">
                    @foreach($chart['summary'] as $stat)
                    <div>
                        <div class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $stat['value'] ?? '0' }}</div>
                        <div class="text-xs text-slate-600 dark:text-slate-400">{{ $stat['label'] ?? 'إحصائية' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <!-- Charts Placeholder when no data -->
    <div class="rounded-2xl bg-gradient-to-br from-slate-50 to-gray-50 dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700 p-8 shadow-lg text-center">
        <div class="mx-auto h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-6">
            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-600 dark:text-slate-400 mb-3">📊 الرسوم البيانية</h3>
        <p class="text-slate-500 dark:text-slate-500">سيتم عرض الرسوم البيانية التفاعلية هنا عند توفر البيانات</p>
    </div>
    @endif

    <!-- Student Analytics & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Top Students -->
        <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border border-emerald-200 dark:border-emerald-800 p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-12 w-12 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-emerald-900 dark:text-emerald-100">⭐ أفضل الطلاب</h3>
                    <p class="text-sm text-emerald-700 dark:text-emerald-300">الطلاب المتفوقين</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach(($topStudents ?? []) as $index => $student)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-white/50 dark:bg-slate-800/50 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-md transition-all duration-300">
                    <div class="w-8 h-8 rounded-lg {{ $index < 3 ? 'bg-gradient-to-br from-amber-400 to-orange-500' : 'bg-slate-200 dark:bg-slate-700' }} flex items-center justify-center text-white text-sm font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student['name'] }}</div>
                        <div class="text-sm text-slate-500">{{ $student['saved'] }} آية محفوظة</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-lg {{ $index < 3 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $student['score'] }}</div>
                        <div class="text-xs text-slate-500">نقطة</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- At Risk Students -->
        <div class="rounded-2xl bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border border-red-200 dark:border-red-800 p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-red-500/20 flex items-center justify-center">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">⚠️ طلاب يحتاجون متابعة</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">الطلاب المعرضون لخطر الانقطاع</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <div class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></div>
                    <span class="text-xs text-red-600 dark:text-red-400">{{ count($atRiskStudents ?? []) }} طالب</span>
                </div>
            </div>

            <div class="space-y-3">
                @foreach(($atRiskStudents ?? []) as $student)
                <div class="p-3 rounded-xl bg-white/50 dark:bg-slate-800/50 border border-red-200/50 dark:border-red-700/50 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center">
                            <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $student['name'] }}</div>
                            <div class="text-sm text-slate-500">آخر نشاط: {{ $student['last_activity'] }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $student['risk'] === 'مرتفع' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : ($student['risk'] === 'متوسط' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400') }}">
                            خطر {{ $student['risk'] }}
                        </span>
                        <button class="ml-auto p-1 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activities & Quick Actions -->
        <div class="rounded-2xl bg-gradient-to-br from-slate-50 to-gray-50 dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700 p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-slate-500/20 flex items-center justify-center">
                        <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">📋 المراجعات الأخيرة</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">آخر {{ count($recentReviews ?? []) }} مراجعة</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700">
                            <tr>
                                <th class="px-4 py-4 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">الطالب</th>
                                <th class="px-4 py-4 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">المقطع</th>
                                <th class="px-4 py-4 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">التقييم</th>
                                <th class="px-4 py-4 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">النتيجة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse(($recentReviews ?? []) as $review)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-200 group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/50 dark:to-indigo-900/50 flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ substr($review['user'] ?? '—', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $review['user'] ?? '—' }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $review['date'] ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-slate-900 dark:text-slate-100 font-medium">{{ $review['segment'] ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= ($review['performance'] ?? 0) ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600' }} transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        @endfor
                                        <span class="ml-2 text-xs text-slate-500 dark:text-slate-400">{{ $review['performance'] ?? 0 }}/5</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ ($review['successful'] ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        @if($review['successful'] ?? false)
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        ناجحة
                                        @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        تحتاج مراجعة
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div class="text-slate-500 dark:text-slate-400">لا توجد مراجعات حديثة</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Tools -->
        <div class="rounded-2xl bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border border-indigo-200 dark:border-indigo-800 p-4 lg:p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-4 lg:mb-6">
                <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-indigo-500/20 flex items-center justify-center">
                    <svg class="h-5 w-5 lg:h-6 lg:w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg lg:text-xl font-bold text-indigo-900 dark:text-indigo-100">🚀 إجراءات سريعة</h3>
                    <p class="text-sm text-indigo-700 dark:text-indigo-300">أدوات الإدارة السريعة</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
                <a href="#" class="group flex items-center gap-3 p-3 lg:p-4 rounded-xl bg-white/60 dark:bg-slate-800/60 border border-indigo-200/50 dark:border-indigo-700/50 hover:shadow-lg hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all duration-300">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-right">
                        <div class="font-medium text-purple-900 dark:text-purple-100 text-sm lg:text-base">إعدادات النظام</div>
                        <div class="text-xs lg:text-sm text-purple-600 dark:text-purple-400">تخصيص المنصة</div>
                    </div>
                    <svg class="h-4 w-4 text-purple-600 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="#" class="group flex items-center gap-3 p-3 lg:p-4 rounded-xl bg-white/60 dark:bg-slate-800/60 border border-amber-200/50 dark:border-amber-700/50 hover:shadow-lg hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all duration-300">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v2H4a2 2 0 01-2-2V5c0-1.1.9-2 2-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V10"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-right">
                        <div class="font-medium text-amber-900 dark:text-amber-100 text-sm lg:text-base">إرسال تذكير</div>
                        <div class="text-xs lg:text-sm text-amber-600 dark:text-amber-400">تذكير جماعي للطلاب</div>
                    </div>
                    <svg class="h-4 w-4 text-amber-600 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="#" class="group flex items-center gap-3 p-3 lg:p-4 rounded-xl bg-white/60 dark:bg-slate-800/60 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all duration-300">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-right">
                        <div class="font-medium text-emerald-900 dark:text-emerald-100 text-sm lg:text-base">إدارة المستخدمين</div>
                        <div class="text-xs lg:text-sm text-emerald-600 dark:text-emerald-400">إضافة وتعديل الحسابات</div>
                    </div>
                    <svg class="h-4 w-4 text-emerald-600 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="#" class="group flex items-center gap-3 p-3 lg:p-4 rounded-xl bg-white/60 dark:bg-slate-800/60 border border-rose-200/50 dark:border-rose-700/50 hover:shadow-lg hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all duration-300">
                    <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-right">
                        <div class="font-medium text-rose-900 dark:text-rose-100 text-sm lg:text-base">تقارير مفصلة</div>
                        <div class="text-xs lg:text-sm text-rose-600 dark:text-rose-400">إحصائيات شاملة</div>
                    </div>
                    <svg class="h-4 w-4 text-rose-600 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Enhanced Charts Configuration
    const chartsData = @json($charts ?? []);

    // Global Chart Configuration
    Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#e2e8f0';
    Chart.defaults.backgroundColor = 'rgba(59, 130, 246, 0.1)';

    console.log('Charts Data:', chartsData); // Debug

    // Initialize Charts
    if (chartsData && Array.isArray(chartsData) && chartsData.length > 0) {
        chartsData.forEach((chart, index) => {
            const ctx = document.getElementById(chart.id);
            if (!ctx) {
                console.warn(`Canvas element ${chart.id} not found`);
                return;
            }

            const chartConfig = {
                type: chart.type || 'line',
                data: {
                    labels: chart.labels || [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: chart.type === 'doughnut',
                            position: 'bottom',
                            labels: { padding: 15, usePointStyle: true }
                        }
                    }
                }
            };

            // Configure based on chart type
            if (chart.type === 'doughnut') {
                chartConfig.data.datasets.push({
                    data: chart.data || [],
                    backgroundColor: chart.colors || ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0'],
                    borderWidth: 0
                });
            } else {
                // Line chart
                const colors = {
                    velocityChart: { border: 'rgb(16, 185, 129)', bg: 'rgba(16, 185, 129, 0.1)' },
                    adherenceChart: { border: 'rgb(59, 130, 246)', bg: 'rgba(59, 130, 246, 0.1)' },
                    default: { border: 'rgb(99, 102, 241)', bg: 'rgba(99, 102, 241, 0.1)' }
                };

                const color = colors[chart.id] || colors.default;

                chartConfig.data.datasets.push({
                    label: chart.title,
                    data: chart.data || [],
                    borderColor: color.border,
                    backgroundColor: color.bg,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                });

                chartConfig.options.scales = {
                    y: {
                        beginAtZero: true,
                        max: chart.id === 'adherenceChart' ? 100 : undefined,
                        grid: { color: 'rgba(0,0,0,0.1)' }
                    },
                    x: { grid: { display: false } }
                };
            }

            try {
                new Chart(ctx, chartConfig);
                console.log(`Chart ${chart.id} initialized successfully`);
            } catch (error) {
                console.error(`Error initializing chart ${chart.id}:`, error);
            }
        });
    } else {
        console.warn('No charts data available or invalid format');
    }

    // Add floating animation CSS
    const floatElement = document.querySelector('.animate-float');
    if (floatElement) {
        floatElement.style.animation = 'float 3s ease-in-out infinite';
    }

    // Add keyframes for float animation if not exists
    if (!document.querySelector('#float-keyframes')) {
        const style = document.createElement('style');
        style.id = 'float-keyframes';
        style.textContent = `
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
        `;
        document.head.appendChild(style);
    }

    // Auto-refresh data every 5 minutes
    if (typeof refreshInterval === 'undefined') {
        const refreshInterval = setInterval(() => {
            // Reload current page to refresh data
            window.location.reload();
        }, 300000); // 5 minutes

        // Show toast notification for data refresh
        setTimeout(() => {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in';
            toast.textContent = 'تم تحديث البيانات تلقائياً';
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }, 30000); // Update every 30 seconds
    }
</script>
@endpush
@endsection
