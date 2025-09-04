@extends('admin.layouts.app')

@section('page-title', 'تفاصيل المستخدم')
@section('page-subtitle', 'عرض جميع معلومات المستخدم: ' . $user->name)

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" 
                   class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <!-- Profile Image or Avatar -->
                    @if($user->profile && $user->profile->profile_image)
                        <div class="w-16 h-16 rounded-xl overflow-hidden">
                            <img src="{{ $user->profile->profile_image }}" alt="{{ $user->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $user->name }}</h1>
                        <p class="text-slate-600 dark:text-slate-400">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors @permClass('users.edit')" @permDisabled('users.edit')>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تعديل المستخدم
                </a>
            </div>
        </div>

        <!-- User Status Card -->
        <div class="card-elegant rounded-xl p-4 mb-6 bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/20 dark:to-blue-900/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $user->profile->total_points ?? 0 }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي النقاط</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $user->profile->verses_memorized_count ?? 0 }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">آية محفوظة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $user->memorizationPlans->count() ?? 0 }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">خطة حفظ</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">نشط</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Basic Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">المعلومات الشخصية</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">الاسم الكامل</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $user->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">البريد الإلكتروني</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 dir-ltr text-left">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">اسم المستخدم</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $user->profile->username ?? 'غير محدد' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">تاريخ التسجيل</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Information Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">إحصائيات الحفظ</h2>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                {{ $user->profile->total_points ?? 0 }}
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">إجمالي النقاط</div>
                        </div>
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                                {{ $user->profile->verses_memorized_count ?? 0 }}
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">آية محفوظة</div>
                        </div>
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                                {{ $user->memorizationPlans->count() }}
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">خطة حفظ</div>
                        </div>
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20">
                            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mb-1">
                                {{ $user->preference->daily_minutes ?? 0 }}
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400">دقيقة يومياً</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Memorization Plans -->
                @if($user->memorizationPlans->count() > 0)
                    <div class="card-elegant rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h2m0-13h6m0 0a2 2 0 012 2v11a2 2 0 01-2 2h-2m0-13V5a2 2 0 00-2-2H9a2 2 0 00-2 2v0"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">خطط الحفظ</h2>
                        </div>

                        <div class="space-y-3">
                            @foreach($user->memorizationPlans->take(5) as $plan)
                                <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full {{ $plan->is_active ? 'bg-green-500' : 'bg-slate-400' }}"></div>
                                        <div>
                                            <div class="font-medium text-slate-800 dark:text-slate-100">
                                                خطة حفظ #{{ $plan->id }}
                                            </div>
                                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                                {{ $plan->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400' }}">
                                            {{ $plan->is_active ? 'نشطة' : 'مكتملة' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- User Preferences Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">التفضيلات</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">التفسير المفضل</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $user->preference->tafsir->name ?? 'غير محدد' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">الدقائق اليومية</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $user->preference->daily_minutes ?? 0 }} دقيقة
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">الجلسات اليومية</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $user->preference->sessions_per_day ?? 0 }} جلسة
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">المستوى الحالي</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                                @php
                                    $level = $user->preference->current_level ?? 'beginner';
                                    $levelText = [
                                        'beginner' => 'مبتدئ',
                                        'intermediate' => 'متوسط',
                                        'advanced' => 'متقدم'
                                    ][$level] ?? 'مبتدئ';
                                    $levelColors = [
                                        'beginner' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'intermediate' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'advanced' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                    ][$level] ?? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                                @endphp
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $levelColors }}">
                                    {{ $levelText }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card-elegant rounded-xl p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 @permClass('users.edit')" @permDisabled('users.edit')>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل المعلومات
                        </a>
                        <button onclick="window.print()" 
                                class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            طباعة التفاصيل
                        </button>
                        <hr class="border-slate-200 dark:border-slate-700">
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline w-full" 
                              onsubmit="return confirm('هل تريد حذف هذا المستخدم نهائياً؟\n\nسيتم حذف:\n- بيانات المستخدم\n- خطط الحفظ المرتبطة\n- السجلات والإحصائيات\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600 dark:text-red-400 w-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                حذف المستخدم
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .no-print, nav, .sidebar, button, a[href*="edit"], a[href*="delete"] {
                display: none !important;
            }
            .card-elegant {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
            }
        }
    </style>
@endsection
