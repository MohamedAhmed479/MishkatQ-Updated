@extends('admin.layouts.app')

@section('page-title', 'تفاصيل الشارة')
@section('page-subtitle', 'عرض جميع معلومات الشارة: ' . $badge->name)

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.badges.index') }}" 
                   class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <!-- Badge Icon -->
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                        @if($badge->icon)
                            <div class="text-white text-2xl font-bold">{{ substr($badge->name, 0, 1) }}</div>
                        @else
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $badge->name }}</h1>
                        <p class="text-slate-600 dark:text-slate-400">{{ $badge->description }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.badges.edit', $badge) }}" 
                   class="flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تعديل الشارة
                </a>
            </div>
        </div>

        <!-- Badge Status Card -->
        <div class="card-elegant rounded-xl p-4 mb-6 bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/20 dark:to-blue-900/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                            {{ $badge->points_value }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">نقطة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $totalAwarded }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">مستخدم حصل عليها</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            @php
                                $criteria = $badge->winning_criteria;
                            @endphp
                            {{ $criteria['threshold'] ?? 0 }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            @php
                                $typeLabels = [
                                    'verses_memorized' => 'آية محفوظة',
                                    'consecutive_days' => 'يوم متتالي',
                                    'total_points' => 'نقطة',
                                    'perfect_reviews' => 'مراجعة مثالية'
                                ];
                            @endphp
                            {{ $typeLabels[$criteria['type']] ?? 'معيار' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $badge->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                    <span class="{{ $badge->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                        {{ $badge->is_active ? 'نشطة' : 'غير نشطة' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Badge Information Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">معلومات الشارة</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">اسم الشارة</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $badge->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">رمز الشارة</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $badge->icon ?: 'غير محدد' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">قيمة النقاط</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                                <span class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    {{ $badge->points_value }} نقطة
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">تاريخ الإنشاء</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $badge->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">وصف الشارة</label>
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                            {{ $badge->description }}
                        </div>
                    </div>
                </div>

                <!-- Criteria Information Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">شروط الحصول على الشارة</h2>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 rounded-lg p-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                                {{ $criteria['threshold'] ?? 0 }}
                            </div>
                            <div class="text-lg font-medium text-slate-800 dark:text-slate-100">
                                @php
                                    $criteriaLabels = [
                                        'verses_memorized' => 'عدد الآيات المحفوظة',
                                        'consecutive_days' => 'الأيام المتتالية',
                                        'total_points' => 'إجمالي النقاط',
                                        'perfect_reviews' => 'المراجعات المثالية'
                                    ];
                                @endphp
                                {{ $criteriaLabels[$criteria['type']] ?? 'معيار غير محدد' }}
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">
                                يحصل المستخدم على هذه الشارة عند تحقيق هذا المعيار
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Awards -->
                @if($recentAwards->count() > 0)
                    <div class="card-elegant rounded-xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">آخر المستخدمين الحاصلين على الشارة</h2>
                            </div>
                            @if($totalAwarded > 5)
                                <a href="{{ route('admin.badges.awarded-users', $badge) }}" 
                                   class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium">
                                    عرض الكل ({{ $totalAwarded }})
                                </a>
                            @endif
                        </div>

                        <div class="space-y-3">
                            @foreach($recentAwards as $user)
                                <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800 dark:text-slate-100">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ $user->pivot->awarded_at ? \Carbon\Carbon::parse($user->pivot->awarded_at)->format('d/m/Y H:i') : '' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">إحصائيات</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                {{ $totalAwarded }}
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">مجموع المنح</div>
                        </div>
                        
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                                {{ $badge->points_value * $totalAwarded }}
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">مجموع النقاط الممنوحة</div>
                        </div>

                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                                {{ $badge->is_active ? 'نشطة' : 'غير نشطة' }}
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">حالة الشارة</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card-elegant rounded-xl p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.badges.edit', $badge) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل الشارة
                        </a>
                        
                        @if($totalAwarded > 0)
                            <a href="{{ route('admin.badges.awarded-users', $badge) }}" 
                               class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 w-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                عرض جميع المستخدمين
                            </a>
                        @endif

                        <!-- Toggle Status -->
                        <form method="POST" action="{{ route('admin.badges.toggle-status', $badge) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ $badge->is_active ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400' }} w-full">
                                @if($badge->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                    </svg>
                                    إلغاء تفعيل الشارة
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    تفعيل الشارة
                                @endif
                            </button>
                        </form>

                        <hr class="border-slate-200 dark:border-slate-700">
                        
                        <form method="POST" action="{{ route('admin.badges.destroy', $badge) }}" class="w-full" 
                              onsubmit="return confirm('هل تريد حذف هذه الشارة نهائياً؟\n\nسيتم حذف:\n- الشارة وجميع بياناتها\n- سجلات حصول المستخدمين عليها\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600 dark:text-red-400 w-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                حذف الشارة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
