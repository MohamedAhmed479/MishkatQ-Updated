@extends('admin.layouts.app')

@section('page-title', 'إدارة المشرفين')

@section('content')
<div class="card-elegant rounded-xl p-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-4 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 dark:from-slate-800 dark:via-slate-700 dark:to-slate-600 p-4 rounded-xl shadow-lg">
        <div>
            <div class="flex items-center gap-3 text-white">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold">إدارة المشرفين</h2>
                    <p class="text-sm opacity-80">إجمالي {{ $admins->total() }} مشرف</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form method="GET" class="flex items-center bg-white dark:bg-slate-700 rounded-lg overflow-hidden shadow-sm w-full md:w-64">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="ابحث بالاسم أو البريد..." class="flex-1 px-3 py-2 text-sm text-slate-700 dark:text-slate-100 bg-transparent outline-none" />
                <button type="submit" class="px-3 text-blue-600 hover:text-blue-800 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @if(($search ?? '') !== '')
                    <a href="{{ route('admin.admins.index') }}" class="px-3 text-red-500 hover:text-red-700">×</a>
                @endif
            </form>
                <a href="{{ route('admin.admins.create') }}" class="flex items-center gap-2 px-4 py-2 bg-white text-blue-600 hover:bg-slate-100 dark:bg-blue-500 dark:hover:bg-blue-400 dark:text-white rounded-lg shadow-sm font-medium transition @permClass('admins.create')" @permDisabled('admins.create')>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                مشرف جديد
            </a>
        </div>
    </div>

    @if($admins->isEmpty())
        <div class="rounded-xl border border-slate-700/60 bg-slate-800/40 p-10 text-center">
            <div class="mx-auto w-12 h-12 rounded-full bg-slate-700/60 flex items-center justify-center text-slate-300 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1"/></svg>
            </div>
            <div class="text-slate-200 font-semibold">لا توجد نتائج</div>
            <div class="text-slate-400 text-sm mt-1">غيّر كلمات البحث أو أضف مشرفًا جديدًا</div>
            @can('admins.create')
            <div class="mt-4">
                <a href="{{ route('admin.admins.create') }}" class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">إضافة مشرف</a>
            </div>
            @endcan
        </div>
    @else
    <div class="card-elegant rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="table-header">
                <tr>
                    <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">#</th>
                    <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">المشرف</th>
                    <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">البريد الإلكتروني</th>
                    <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">الأدوار</th>
                    <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-base">العمليات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($admins as $admin)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                        <td class="p-4"><span class="text-sm font-mono text-slate-500 dark:text-slate-400">#{{ $admin->id }}</span></td>
                        <td class="p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-lg">
                                    {{ mb_substr($admin->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 dark:text-slate-200 text-base">{{ $admin->name }}</div>
                                    @if($admin->assignedBy)
                                        <div class="text-xs text-slate-500 dark:text-slate-400">مضاف بواسطة: {{ $admin->assignedBy->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-4"><span class="text-sm font-mono text-slate-600 dark:text-slate-300 dir-ltr">{{ $admin->email }}</span></td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($admin->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded bg-slate-700/60 text-slate-200 border border-slate-700">{{ $role->name }}</span>
                                @empty
                                    <span class="text-xs text-slate-400">لا يوجد دور</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.admins.edit', $admin) }}"
                                   class="p-2.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors action-btn {{ auth('admin')->user()?->cannot('admins.edit') ? 'opacity-40 pointer-events-none cursor-not-allowed' : '' }}"
                                   title="تعديل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا المشرف نهائياً؟\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors action-btn {{ auth('admin')->user()?->cannot('admins.delete') ? 'opacity-40 pointer-events-none cursor-not-allowed' : '' }}"
                                            title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </table>
        </div>
    </div>
    <div class="mt-6">
        {{ $admins->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection


