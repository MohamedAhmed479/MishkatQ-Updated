@extends('admin.layouts.app')

@section('page-title', 'تفاصيل الآية')
@section('page-subtitle', 'عرض نص الآية ومعلوماتها')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تفاصيل الآية {{ $verse->verse_key }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.verses.edit', $verse) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors">تعديل</a>
            <a href="{{ route('admin.verses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">العودة</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">السورة</div>
            <div class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $verse->chapter->name_ar ?? '' }} ({{ $verse->chapter_id }})</div>
            <div class="mt-3 text-sm text-slate-600 dark:text-slate-400">رقم الآية: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->verse_number }}</span></div>
            <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">الصفحة: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->page_number }}</span> — الجزء: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->juz_number }}</span></div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">المفتاح</div>
            <div class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $verse->verse_key }}</div>
            <div class="mt-3 text-sm text-slate-600 dark:text-slate-400">سجدة: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->sajda ? 'نعم' : 'لا' }}</span></div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 lg:col-span-2">
            <div class="text-sm text-slate-600 dark:text-slate-400 mb-2">النص العثماني</div>
            <div class="text-2xl leading-loose text-slate-900 dark:text-slate-100" dir="rtl">{{ $verse->text_uthmani }}</div>
        </div>

        @if($verse->text_imlaei)
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 lg:col-span-2">
            <div class="text-sm text-slate-600 dark:text-slate-400 mb-2">النص الإملائي</div>
            <div class="text-xl leading-loose text-slate-900 dark:text-slate-100" dir="rtl">{{ $verse->text_imlaei }}</div>
        </div>
        @endif
    </div>
</div>
@endsection


