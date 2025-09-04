@extends('admin.layouts.app')

@section('page-title', 'تفاصيل الكلمة')
@section('page-subtitle', 'عرض معلومات الكلمة وربطها بالآية')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تفاصيل الكلمة #{{ $word->id }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.words.edit', $word) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors @permClass('words.edit')" @permDisabled('words.edit')>تعديل</a>
            <a href="{{ route('admin.words.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">العودة</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">النص</div>
            <div class="text-2xl font-semibold text-slate-900 dark:text-slate-100" dir="rtl">{{ $word->text }}</div>
            <div class="mt-3 text-sm text-slate-600 dark:text-slate-400">الموضع: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $word->position }}</span></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">الآية</div>
            <div class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $word->verse->chapter->name_ar ?? '' }} — آية {{ $word->verse->verse_number ?? '' }} (ID: {{ $word->verse_id }})</div>
            <div class="mt-3 text-sm text-slate-600 dark:text-slate-400 break-all">رابط الصوت: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $word->audio_url ?: '-' }}</span></div>
        </div>
    </div>
</div>
@endsection


