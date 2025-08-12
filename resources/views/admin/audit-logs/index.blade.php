@extends('admin.layouts.app')

@section('page-title', 'سجلات التدقيق')
@section('page-subtitle', 'استعراض وفلاتر السجلات')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">سجلات التدقيق</h1>
            <p class="text-slate-600 dark:text-slate-400">تتبع العمليات الهامة عبر النظام</p>
        </div>
        <a href="{{ route('admin.audit-logs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 @permClass('audit-logs.create')" @permDisabled('audit-logs.create')>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            إضافة سجل
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">بحث عام</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="الأكشن، المستخدم، الوصف..." class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">المستخدم</label>
                <select name="user_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">جميع المستخدمين</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (string)$userId === (string)$user->id ? 'selected' : '' }}>{{ $user->name }} — {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">شدة</label>
                <select name="severity" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">الكل</option>
                    @foreach($severities as $s)
                        <option value="{{ $s }}" {{ $severity === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">الكل</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الفئة</label>
                <input type="text" name="category" value="{{ $category }}" placeholder="مثل: auth, users..." class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الطريقة</label>
                <select name="method" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">الكل</option>
                    @foreach($methods as $m)
                        <option value="{{ $m }}" {{ $method === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2">
                    <input id="is_sensitive" type="checkbox" name="is_sensitive" value="1" {{ $isSensitive ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-emerald-600">
                    <label for="is_sensitive" class="text-sm text-slate-700 dark:text-slate-300">عمليات حساسة فقط</label>
                </div>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">من تاريخ</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200">تطبيق</button>
                <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card-elegant rounded-xl overflow-hidden">
        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">التاريخ</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">المستخدم</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الأكشن</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">النموذج</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الشدة</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الحالة</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($logs as $log)
                        @php
                            $sevMap = [
                                'critical' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300',
                                'high' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                'medium' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                'low' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300',
                            ];
                            $sevCls = $sevMap[$log->severity ?? 'low'] ?? $sevMap['low'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $log->performed_at ? $log->performed_at->format('Y-m-d H:i') : $log->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($log->user_name ?? 'س', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-slate-100">{{ $log->user_name ?? 'نظام' }}</div>
                                        <div class="text-xs text-slate-500">{{ $log->user_email ?: '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-slate-800 dark:text-slate-100">{{ $log->action_display_name }}</div>
                                <div class="text-xs text-slate-500">{{ Str::limit($log->description, 80) }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $log->model_type ?: '-' }} @if($log->model_name) — <span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->model_name }}</span>@endif</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sevCls }}">{{ $log->severity ?: 'low' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $log->status ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.audit-logs.show', $log) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors @permClass('audit-logs.view')" @permDisabled('audit-logs.view') title="عرض">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.audit-logs.edit', $log) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors @permClass('audit-logs.edit')" @permDisabled('audit-logs.edit') title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.audit-logs.destroy', $log) }}" method="POST" class="inline @permClass('audit-logs.delete')" onsubmit="return confirm('هل تريد حذف هذا السجل؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" @permDisabled('audit-logs.delete') class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors" title="حذف">
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
            <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">{{ $logs->links() }}</div>
        @else
            <div class="p-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا توجد سجلات</h3>
                        <p class="text-slate-600 dark:text-slate-400">قم بتعديل الفلاتر أو أضف سجلاً جديداً</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection


