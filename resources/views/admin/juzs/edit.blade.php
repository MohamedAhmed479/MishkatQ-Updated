@extends('admin.layouts.app')

@section('page-title', 'تعديل جزء')
@section('page-subtitle', 'تعديل بيانات الجزء')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">تعديل جزء</h1>
        <a href="{{ route('admin.juzs.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors touch-manipulation">العودة</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <form method="POST" action="{{ route('admin.juzs.update', $juz) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">رقم الجزء</label>
                    <input name="juz_number" type="number" min="1" max="30" value="{{ old('juz_number', $juz->juz_number) }}" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">بداية (آية ID)</label>
                    <input name="start_verse_id" type="number" value="{{ old('start_verse_id', $juz->start_verse_id) }}" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">نهاية (آية ID)</label>
                    <input name="end_verse_id" type="number" value="{{ old('end_verse_id', $juz->end_verse_id) }}" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">عدد الآيات (اختياري)</label>
                    <input name="verses_count" type="number" min="1" value="{{ old('verses_count', $juz->verses_count) }}" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base" placeholder="سيتم احتسابه تلقائياً إذا تركته فارغاً">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 touch-manipulation">حفظ</button>
                <a href="{{ route('admin.juzs.index') }}" class="flex-1 sm:flex-none px-6 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200 touch-manipulation text-center">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection


