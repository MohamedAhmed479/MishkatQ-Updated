@extends('admin.layouts.app')

@section('page-title', 'إدارة لوحة المتصدرين')
@section('page-subtitle', 'عرض وإدارة السجلات حسب الفترة والرتب')

@section('content')
    <div class="space-y-6">
        <!-- Header Actions & Filters -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="relative">
                    <input type="text" name="q" value="{{ $search }}" placeholder="ابحث باسم/بريد المستخدم"
                           class="pl-10 pr-4 py-2.5 w-64 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <select name="period_type" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">كل الفترات</option>
                    @foreach($periodTypes as $key => $label)
                        <option value="{{ $key }}" {{ $periodType === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <input type="date" name="date" value="{{ $date }}" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">

                <button type="submit" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">بحث</button>

                @if($search || $periodType || $date)
                    <a href="{{ route('admin.leaderboards.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">مسح</a>
                @endif
            </form>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.leaderboards.recalculate') }}" class="inline-flex items-center gap-2 @permClass('leaderboards.recalculate')">
                    @csrf
                    <select name="period_type" class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                        @foreach($periodTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date" class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" value="{{ $date ?: now()->toDateString() }}">
                    <button type="submit" @permDisabled('leaderboards.recalculate') class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">إعادة احتساب الرتب</button>
                </form>

                <a href="{{ route('admin.leaderboards.create') }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 @permClass('leaderboards.create')" @permDisabled('leaderboards.create')>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                    إضافة سجل
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3 3 7-7"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $leaderboards->total() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي السجلات</div>
                    </div>
                </div>
            </div>
            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $leaderboards->where('period_type','monthly')->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">سجلات شهرية (الصفحة)</div>
                    </div>
                </div>
            </div>
            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $leaderboards->where('period_type','weekly')->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">سجلات أسبوعية (الصفحة)</div>
                    </div>
                </div>
            </div>
            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $leaderboards->where('period_type','daily')->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">سجلات يومية (الصفحة)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-elegant rounded-xl overflow-hidden">
            @if($leaderboards->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">#</th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">المستخدم</th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">النقاط</th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">الرتبة</th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">الفترة</th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">من</th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">إلى</th>
                                <th class="text-center p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($leaderboards as $entry)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="p-4">{{ $entry->id }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-xs">{{ substr($entry->user->name, 0, 1) }}</div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $entry->user->name }}</div>
                                                <div class="text-sm text-slate-600 dark:text-slate-400">{{ $entry->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">{{ $entry->total_points }}</td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">{{ $entry->rank }}</span>
                                    </td>
                                    <td class="p-4">
                                        @php $labels = ['daily'=>'يومي','weekly'=>'أسبوعي','monthly'=>'شهري','yearly'=>'سنوي']; @endphp
                                        <span class="text-sm">{{ $labels[$entry->period_type] ?? $entry->period_type }}</span>
                                    </td>
                                    <td class="p-4">{{ $entry->period_start?->format('Y-m-d') }}</td>
                                    <td class="p-4">{{ $entry->period_end?->format('Y-m-d') }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                             <a href="{{ route('admin.leaderboards.show', $entry) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors @permClass('leaderboards.view')" @permDisabled('leaderboards.view') title="عرض التفاصيل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                             <a href="{{ route('admin.leaderboards.edit', $entry) }}" class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors @permClass('leaderboards.edit')" @permDisabled('leaderboards.edit') title="تعديل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                             <form method="POST" action="{{ route('admin.leaderboards.destroy', $entry) }}" class="inline @permClass('leaderboards.delete')" onsubmit="return confirm('هل تريد حذف هذا السجل؟')">
                                                @csrf
                                                @method('DELETE')
                                                 <button type="submit" @permDisabled('leaderboards.delete') class="p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="حذف">
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

                <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                    {{ $leaderboards->links() }}
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3 3 7-7"/></svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا توجد سجلات</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">ابدأ بإضافة أول سجل للوحة المتصدرين</p>
                            <a href="{{ route('admin.leaderboards.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg transition-all">إضافة سجل</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection


