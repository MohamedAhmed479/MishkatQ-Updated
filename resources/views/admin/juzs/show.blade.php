@extends('admin.layouts.app')

@section('page-title', 'تفاصيل الجزء')
@section('page-subtitle', 'معلومات الجزء وحدوده')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">تفاصيل الجزء رقم {{ $juz->juz_number }}</h1>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <a href="{{ route('admin.juzs.edit', $juz) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors touch-manipulation @permClass('juzs.edit')" @permDisabled('juzs.edit')>تعديل</a>
            <a href="{{ route('admin.juzs.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors touch-manipulation">العودة</a>
        </div>
    </div>
                         
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">رقم الجزء</div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $juz->juz_number }}</div>
                </div>
                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white font-extrabold text-lg sm:text-2xl flex items-center justify-center">{{ $juz->juz_number }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">بداية (ID الآية)</div>
            <div class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $juz->start_verse_id }}</div>
            @if($juz->startVerse)
            <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">سورة: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $juz->startVerse->chapter->name_ar ?? '' }}</span> — آية {{ $juz->startVerse->verse_number }}</div>
            @endif
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">نهاية (ID الآية)</div>
            <div class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $juz->end_verse_id }}</div>
            @if($juz->endVerse)
            <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">سورة: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $juz->endVerse->chapter->name_ar ?? '' }}</span> — آية {{ $juz->endVerse->verse_number }}</div>
            @endif
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700 sm:col-span-2 lg:col-span-3">
            <div class="text-sm text-slate-600 dark:text-slate-400">عدد الآيات</div>
            <div class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($juz->verses_count) }}</div>
        </div>
    </div>
</div>
@endsection


