@extends('admin.layouts.app')

@section('page-title', 'إدارة التفاسير')
@section('page-subtitle', 'عرض وإدارة قائمة التفاسير')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إدارة التفاسير</h1>
            <p class="text-slate-600 dark:text-slate-400">التفاسير المتاحة للتفضيلات</p>
        </div>
        <a href="{{ route('admin.tafsirs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors @permClass('tafsirs.create')" @permDisabled('tafsirs.create')>إضافة تفسير</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <form method="GET" action="{{ route('admin.tafsirs.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-48">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">بحث</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="اسم التفسير" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">تطبيق</button>
                <a href="{{ route('admin.tafsirs.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <div class="card-elegant rounded-xl overflow-hidden">
        @if($tafsirs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">ID</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الاسم</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($tafsirs as $t)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $t->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-200">{{ $t->name }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.tafsirs.edit', $t) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 @permClass('tafsirs.edit')" @permDisabled('tafsirs.edit') title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.tafsirs.destroy', $t) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا التفسير؟')" class="inline @permClass('tafsirs.delete')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" @permDisabled('tafsirs.delete') class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20" title="حذف">
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
            <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">{{ $tafsirs->links() }}</div>
        @else
            <div class="p-16 text-center"><div class="text-slate-500 dark:text-slate-400">لا توجد تفاسير</div></div>
        @endif
    </div>
</div>
@endsection


