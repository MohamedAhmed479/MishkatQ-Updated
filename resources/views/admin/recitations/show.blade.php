@extends('admin.layouts.app')

@section('page-title', 'تفاصيل التسجيل')
@section('page-subtitle', 'عرض بيانات التسجيل الصوتي')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تفاصيل التسجيل #{{ $recitation->id }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.recitations.edit', $recitation) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors">تعديل</a>
            <a href="{{ route('admin.recitations.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">العودة</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">القارئ</div>
            <div class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $recitation->reciter->reciter_name ?? '' }} (ID: {{ $recitation->reciter_id }})</div>
            <div class="mt-3 text-sm text-slate-600 dark:text-slate-400">الآية: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $recitation->verse->chapter->name_ar ?? '' }} — {{ $recitation->verse->verse_number ?? '' }} (ID: {{ $recitation->verse_id }})</span></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">الرابط</div>
            <div class="text-sm text-slate-900 dark:text-slate-100 break-all"><a href="{{ $recitation->audio_url }}" target="_blank" rel="noopener" class="text-emerald-600 hover:text-emerald-700">{{ $recitation->audio_url }}</a></div>
        </div>
    </div>
</div>
@endsection


