@extends('admin.layouts.app')

@section('page-title', 'إضافة كلمة')
@section('page-subtitle', 'إضافة كلمة لآية محددة')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إضافة كلمة</h1>
        <a href="{{ route('admin.words.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">العودة</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <form method="POST" action="{{ route('admin.words.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">آية ID</label>
                <input name="verse_id" type="number" min="1" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الموضع</label>
                <input name="position" type="number" min="1" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">النص</label>
                <input name="text" required dir="rtl" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">رابط الصوت (اختياري)</label>
                <input name="audio_url" type="url" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" placeholder="https://...">
            </div>
            <div class="md:col-span-2 flex items-center justify-end gap-3">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">حفظ</button>
                <a href="{{ route('admin.words.index') }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection


