@extends('admin.layouts.app')

@section('page-title', 'إضافة آية')
@section('page-subtitle', 'إنشاء آية جديدة')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إضافة آية</h1>
        <a href="{{ route('admin.verses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">العودة</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <form method="POST" action="{{ route('admin.verses.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">السورة</label>
                <select name="chapter_id" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    @foreach($chapters as $c)
                        <option value="{{ $c->id }}">{{ $c->id }} — {{ $c->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">رقم الآية</label>
                <input name="verse_number" type="number" min="1" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">المفتاح</label>
                <input name="verse_key" required placeholder="مثال: 1:1" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">النص العثماني</label>
                <textarea name="text_uthmani" required rows="3" dir="rtl" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">النص الإملائي (اختياري)</label>
                <textarea name="text_imlaei" rows="3" dir="rtl" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">رقم الصفحة</label>
                <input name="page_number" type="number" min="1" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الجزء</label>
                <input name="juz_number" type="number" min="1" max="30" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الحزب</label>
                <input name="hizb_number" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الربع</label>
                <input name="rub_number" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">سجدة؟</label>
                <select name="sajda" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="">—</option>
                    <option value="1">نعم</option>
                    <option value="0">لا</option>
                </select>
            </div>
            <div class="md:col-span-2 flex items-center justify-end gap-3">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">حفظ</button>
                <a href="{{ route('admin.verses.index') }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection


