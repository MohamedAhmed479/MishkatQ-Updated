@extends('admin.layouts.app')

@section('page-title', 'إدارة الآيات')
@section('page-subtitle', 'عرض وبحث وتعديل آيات القرآن')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إدارة الآيات</h1>
            <p class="text-slate-600 dark:text-slate-400">بحث بالآية والنص</p>
        </div>
        <a href="{{ route('admin.verses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors @permClass('verses.create')" @permDisabled('verses.create')>إضافة آية</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <form method="GET" action="{{ route('admin.verses.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">بحث نصي</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="النص العثماني/الإملائي أو المفتاح"
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">السورة</label>
                <select name="chapter_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="">جميع السور</option>
                    @foreach($chapters as $c)
                        <option value="{{ $c->id }}" {{ (string)$chapterId === (string)$c->id ? 'selected' : '' }}>{{ $c->id }} — {{ $c->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الجزء</label>
                <input type="number" name="juz_number" value="{{ $juz }}" min="1" max="30" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">رقم الصفحة</label>
                <input type="number" name="page_number" value="{{ $page }}" min="1" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">ترتيب حسب</label>
                <select name="sort_by" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="id" {{ $sortBy==='id' ? 'selected' : '' }}>ID</option>
                    <option value="chapter_id" {{ $sortBy==='chapter_id' ? 'selected' : '' }}>السورة</option>
                    <option value="verse_number" {{ $sortBy==='verse_number' ? 'selected' : '' }}>رقم الآية</option>
                    <option value="page_number" {{ $sortBy==='page_number' ? 'selected' : '' }}>رقم الصفحة</option>
                    <option value="juz_number" {{ $sortBy==='juz_number' ? 'selected' : '' }}>الجزء</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">ترتيب</label>
                <select name="sort_order" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="asc" {{ $sortOrder==='asc' ? 'selected' : '' }}>تصاعدي</option>
                    <option value="desc" {{ $sortOrder==='desc' ? 'selected' : '' }}>تنازلي</option>
                </select>
            </div>
            <div class="lg:col-span-6 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">تطبيق</button>
                <a href="{{ route('admin.verses.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <div class="card-elegant rounded-xl overflow-hidden">
        @if($verses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">ID</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">السورة</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">رقم الآية</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">النص (عثماني)</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الجزء</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الصفحة</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($verses as $v)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $v->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $v->chapter_id }} — {{ $v->chapter->name_ar ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $v->verse_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-200" dir="rtl">{{ Str::limit($v->text_uthmani, 80) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $v->juz_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $v->page_number }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.verses.show', $v) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 @permClass('verses.view')" @permDisabled('verses.view') title="عرض">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.verses.edit', $v) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 @permClass('verses.edit')" @permDisabled('verses.edit') title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.verses.destroy', $v) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الآية؟')" class="inline @permClass('verses.delete')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" @permDisabled('verses.delete') class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20" title="حذف">
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
            <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">{{ $verses->links() }}</div>
        @else
            <div class="p-16 text-center"><div class="text-slate-500 dark:text-slate-400">لا توجد آيات</div></div>
        @endif
    </div>
</div>
@endsection


