@extends('admin.layouts.app')

@section('page-title', 'تعديل آية')
@section('page-subtitle', 'تحديث بيانات الآية')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">تعديل آية</h1>
        <a href="{{ route('admin.verses.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors touch-manipulation">العودة</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <form method="POST" action="{{ route('admin.verses.update', $verse) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">السورة</label>
                    <select name="chapter_id" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                        @foreach($chapters as $c)
                            <option value="{{ $c->id }}" {{ (string) old('chapter_id', $verse->chapter_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->id }} — {{ $c->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">رقم الآية</label>
                    <input name="verse_number" type="number" min="1" value="{{ old('verse_number', $verse->verse_number) }}" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">المفتاح</label>
                    <input name="verse_key" value="{{ old('verse_key', $verse->verse_key) }}" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">النص العثماني</label>
                    <textarea name="text_uthmani" required rows="3" dir="rtl" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">{{ old('text_uthmani', $verse->text_uthmani) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">النص الإملائي (اختياري)</label>
                    <textarea name="text_imlaei" rows="3" dir="rtl" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">{{ old('text_imlaei', $verse->text_imlaei) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">رقم الصفحة</label>
                    <input name="page_number" type="number" min="1" value="{{ old('page_number', $verse->page_number) }}" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الجزء</label>
                    <input name="juz_number" type="number" min="1" max="30" value="{{ old('juz_number', $verse->juz_number) }}" required class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الحزب</label>
                    <input name="hizb_number" type="number" min="0" value="{{ old('hizb_number', $verse->hizb_number) }}" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الربع</label>
                    <input name="rub_number" type="number" min="0" value="{{ old('rub_number', $verse->rub_number) }}" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">سجدة؟</label>
                    <select name="sajda" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-base">
                        <option value="">—</option>
                        <option value="1" {{ old('sajda', (int) $verse->sajda) === 1 ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ old('sajda', (int) $verse->sajda) === 0 ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 touch-manipulation">حفظ</button>
                <a href="{{ route('admin.verses.index') }}" class="flex-1 sm:flex-none px-6 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200 touch-manipulation text-center">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection


