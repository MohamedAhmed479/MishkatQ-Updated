@extends('admin.layouts.app')

@section('page-title', 'إدارة الكلمات')
@section('page-subtitle', 'عرض وبحث وتعديل كلمات الآيات')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">إدارة الكلمات</h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">كلمات الآيات مع موضعها</p>
        </div>
        <a href="{{ route('admin.words.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors touch-manipulation @permClass('words.create')" @permDisabled('words.create')>إضافة كلمة</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <form method="GET" action="{{ route('admin.words.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">بحث نصي</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="الكلمة"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 touch-manipulation">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">السورة</label>
                <select name="chapter_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 touch-manipulation">
                    <option value="">جميع السور</option>
                    @foreach($chapters as $c)
                        <option value="{{ $c->id }}" {{ (string) request('chapter_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->id }} — {{ $c->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">آية ID</label>
                <input type="number" name="verse_id" value="{{ $verseId }}" min="1" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 touch-manipulation">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">ترتيب حسب</label>
                <select name="sort_by" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 touch-manipulation">
                    <option value="id" {{ $sortBy==='id' ? 'selected' : '' }}>ID</option>
                    <option value="position" {{ $sortBy==='position' ? 'selected' : '' }}>الموضع</option>
                    <option value="verse_id" {{ $sortBy==='verse_id' ? 'selected' : '' }}>آية ID</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">ترتيب</label>
                <select name="sort_order" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 touch-manipulation">
                    <option value="asc" {{ $sortOrder==='asc' ? 'selected' : '' }}>تصاعدي</option>
                    <option value="desc" {{ $sortOrder==='desc' ? 'selected' : '' }}>تنازلي</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg touch-manipulation">تطبيق</button>
                <a href="{{ route('admin.words.index') }}" class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg touch-manipulation text-center">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <div class="card-elegant rounded-xl overflow-hidden">
        @if($words->count() > 0)
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">ID</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">آية</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الموضع</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">النص</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الصوت</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($words as $w)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $w->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $w->verse_id }} — {{ $w->verse->chapter->name_ar ?? '' }} ({{ $w->verse->verse_number ?? '' }})</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $w->position }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-200" dir="rtl">{{ $w->text }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 break-all">{{ $w->audio_url ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.words.show', $w) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 @permClass('words.view')" @permDisabled('words.view') title="عرض">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.words.edit', $w) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 @permClass('words.edit')" @permDisabled('words.edit') title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.words.destroy', $w) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الكلمة؟')" class="inline @permClass('words.delete')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" @permDisabled('words.delete') class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden">
                <div class="space-y-4 p-4">
                    @foreach($words as $w)
                    <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-1" dir="rtl">{{ $w->text }}</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">ID: {{ $w->id }} • الموضع: {{ $w->position }}</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $w->verse_id }} — {{ $w->verse->chapter->name_ar ?? '' }} (آية {{ $w->verse->verse_number ?? '' }})</div>
                            </div>
                            <div class="flex items-center gap-2 ml-3">
                                <a href="{{ route('admin.words.show', $w) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 @permClass('words.view')" @permDisabled('words.view') title="عرض">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.words.edit', $w) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 @permClass('words.edit')" @permDisabled('words.edit') title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.words.destroy', $w) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الكلمة؟')" class="inline @permClass('words.delete')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" @permDisabled('words.delete') class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($w->audio_url)
                        <div class="text-xs text-slate-500 dark:text-slate-400 break-all">{{ $w->audio_url }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-700 px-4 sm:px-6 py-4">{{ $words->links() }}</div>
        @else
            <div class="p-16 text-center"><div class="text-slate-500 dark:text-slate-400">لا توجد كلمات</div></div>
        @endif
    </div>
</div>
@endsection


