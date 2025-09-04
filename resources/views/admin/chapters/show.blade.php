@extends('admin.layouts.app')

@section('title', 'تفاصيل السورة')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">تفاصيل السورة</h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">معلومات مفصلة عن السورة: {{ $chapter->name_ar }}</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <a href="{{ route('admin.chapters.edit', $chapter) }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 touch-manipulation @permClass('chapters.edit')" @permDisabled('chapters.edit')>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span class="hidden sm:inline">تعديل السورة</span>
                <span class="sm:hidden">تعديل</span>
            </a>
            <a href="{{ route('admin.chapters.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200 touch-manipulation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="hidden sm:inline">العودة للسور</span>
                <span class="sm:hidden">العودة</span>
            </a>
        </div>
    </div>

    <!-- Chapter Header Card -->
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl p-4 sm:p-8 mb-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl sm:text-3xl font-bold">{{ $chapter->id }}</span>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-2">{{ $chapter->name_ar }}</h2>
                    @if($chapter->name_en)
                        <p class="text-lg sm:text-xl text-emerald-100 mb-3">{{ $chapter->name_en }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                        @if($chapter->revelation_place === 'makkah')
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/20 rounded-full text-sm font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                مكية
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/20 rounded-full text-sm font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                مدنية
                            </span>
                        @endif
                        @if($chapter->revelation_order)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/20 rounded-full text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="hidden sm:inline">ترتيب النزول: {{ $chapter->revelation_order }}</span>
                                <span class="sm:hidden">النزول: {{ $chapter->revelation_order }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-center sm:text-right">
                <div class="text-3xl sm:text-4xl font-bold">{{ $chapter->verses_count ?: '?' }}</div>
                <div class="text-emerald-100 text-sm sm:text-base">آية</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 truncate">عدد الآيات الفعلي</p>
                    <p class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($actualVersesCount) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 truncate">تقدم الحفظ</p>
                    <p class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($memorizationProgressCount) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="p-2 sm:p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 truncate">خطط الحفظ</p>
                    <p class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($planItemsCount) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chapter Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">المعلومات الأساسية</h3>
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between py-2 sm:py-3 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-sm sm:text-base text-slate-600 dark:text-slate-400">رقم السورة:</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">{{ $chapter->id }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 sm:py-3 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-sm sm:text-base text-slate-600 dark:text-slate-400">الاسم العربي:</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100 text-right">{{ $chapter->name_ar }}</span>
                    </div>
                    @if($chapter->name_en)
                    <div class="flex items-center justify-between py-2 sm:py-3 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-sm sm:text-base text-slate-600 dark:text-slate-400">الاسم الإنجليزي:</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100 text-right">{{ $chapter->name_en }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-2 sm:py-3 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-sm sm:text-base text-slate-600 dark:text-slate-400">مكان النزول:</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">
                            @if($chapter->revelation_place === 'makkah')
                                <span class="text-green-600 dark:text-green-400">مكية</span>
                            @else
                                <span class="text-purple-600 dark:text-purple-400">مدنية</span>
                            @endif
                        </span>
                    </div>
                    @if($chapter->revelation_order)
                    <div class="flex items-center justify-between py-2 sm:py-3 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-sm sm:text-base text-slate-600 dark:text-slate-400">ترتيب النزول:</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">{{ $chapter->revelation_order }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-2 sm:py-3">
                        <span class="text-sm sm:text-base text-slate-600 dark:text-slate-400">عدد الآيات:</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">{{ $chapter->verses_count ?: 'غير محدد' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">إجراءات سريعة</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.chapters.verses', $chapter) }}" 
                       class="flex items-center gap-3 w-full p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors duration-200 touch-manipulation @permClass('chapters.view')" @permDisabled('chapters.view')>
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-right flex-1 min-w-0">
                            <p class="font-medium text-slate-900 dark:text-slate-100">عرض الآيات</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ number_format($actualVersesCount) }} آية</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.chapters.memorization-progress', $chapter) }}" 
                       class="flex items-center gap-3 w-full p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors duration-200 touch-manipulation @permClass('chapters.view')" @permDisabled('chapters.view')>
                        <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right flex-1 min-w-0">
                            <p class="font-medium text-slate-900 dark:text-slate-100">تقدم الحفظ</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ number_format($memorizationProgressCount) }} مستخدم</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.chapters.memorization-plans', $chapter) }}" 
                       class="flex items-center gap-3 w-full p-3 sm:p-4 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg transition-colors duration-200 touch-manipulation @permClass('chapters.view')" @permDisabled('chapters.view')>
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="text-right flex-1 min-w-0">
                            <p class="font-medium text-slate-900 dark:text-slate-100">خطط الحفظ</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ number_format($planItemsCount) }} خطة</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Verses -->
    @if($recentVerses->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">آخر الآيات</h3>
                <a href="{{ route('admin.chapters.verses', $chapter) }}" 
                   class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium touch-manipulation @permClass('chapters.view')" @permDisabled('chapters.view')>
                    عرض جميع الآيات
                </a>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">رقم الآية</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">نص الآية</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">عدد الكلمات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($recentVerses as $verse)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                            <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-slate-100">
                                {{ $verse->verse_number }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">
                                {{ Str::limit($verse->text_uthmani, 100) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">
                                {{ $verse->words_count ?: 'غير محدد' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-3">
                @foreach($recentVerses as $verse)
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-sm font-bold">
                            {{ $verse->verse_number }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $verse->words_count ?: 'غير محدد' }} كلمة
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed" dir="rtl" lang="ar">
                        {{ Str::limit($verse->text_uthmani, 80) }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Danger Zone -->
    <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
            <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-lg flex-shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-2">منطقة الخطر</h3>
                <p class="text-sm sm:text-base text-red-800 dark:text-red-200 mb-4">
                    حذف السورة سيؤدي إلى إزالة جميع البيانات المرتبطة بها نهائياً. لا يمكن التراجع عن هذا الإجراء.
                </p>
                <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" class="inline @permClass('chapters.delete')" 
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه السورة؟ هذا الإجراء لا يمكن التراجع عنه.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" @permDisabled('chapters.delete')
                            class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 touch-manipulation">
                        <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        حذف السورة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
