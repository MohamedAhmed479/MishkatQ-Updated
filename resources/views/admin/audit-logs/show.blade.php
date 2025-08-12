@extends('admin.layouts.app')

@section('page-title', 'تفاصيل السجل')
@section('page-subtitle', 'عرض كامل لمحتوى سجل التدقيق')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تفاصيل السجل</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.audit-logs.edit', $log) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors">تعديل</a>
            <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">العودة</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">البيانات الأساسية</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-slate-600 dark:text-slate-400">التاريخ</span>
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->performed_at ? $log->performed_at->format('Y-m-d H:i') : $log->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-slate-600 dark:text-slate-400">الأكشن</span>
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->action_display_name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                        <span class="text-slate-600 dark:text-slate-400">الشدة</span>
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->severity ?: 'low' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-600 dark:text-slate-400">الحالة</span>
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->status ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">المستخدم</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-bold">{{ strtoupper(substr($log->user_name ?? 'س', 0, 1)) }}</div>
                        <div>
                            <div class="font-medium text-slate-800 dark:text-slate-100">{{ $log->user_name ?? 'نظام' }}</div>
                            <div class="text-xs text-slate-500">{{ $log->user_email ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">ID</span><span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->user_id ?: '-' }}</span></div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">النوع</span><span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->user_type ?: '-' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 lg:col-span-2">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">الكيان المستهدف</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">النوع</span><span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->model_type ?: '-' }}</span></div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">المعرف</span><span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->model_id ?: '-' }}</span></div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">الاسم</span><span class="font-medium text-slate-800 dark:text-slate-100">{{ $log->model_name ?: '-' }}</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 lg:col-span-2">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">التغييرات</h3>
                @php $changes = $log->formatted_changes; @endphp
                @if(!empty($changes))
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50 dark:bg-slate-700/50"><tr><th class="px-4 py-3 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الحقل</th><th class="px-4 py-3 text-right text-sm font-medium text-rose-700 dark:text-rose-300">القيمة القديمة</th><th class="px-4 py-3 text-right text-sm font-medium text-emerald-700 dark:text-emerald-300">القيمة الجديدة</th></tr></thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($changes as $field => $diff)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $field }}</td>
                                        <td class="px-4 py-3 text-sm text-rose-700 dark:text-rose-300">{{ is_array($diff['old']) ? json_encode($diff['old'], JSON_UNESCAPED_UNICODE) : (string) $diff['old'] }}</td>
                                        <td class="px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">{{ is_array($diff['new']) ? json_encode($diff['new'], JSON_UNESCAPED_UNICODE) : (string) $diff['new'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-sm text-slate-600 dark:text-slate-400">لا توجد تغييرات مسجلة.</div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">الطلب</h3>
                <div class="text-sm text-slate-600 dark:text-slate-300">
                    <div class="mb-2"><span class="font-medium">Method:</span> {{ $log->method ?: '-' }}</div>
                    <div class="mb-2 break-all"><span class="font-medium">URL:</span> {{ $log->url ?: '-' }}</div>
                    <div class="mb-2"><span class="font-medium">IP:</span> {{ $log->ip_address ?: '-' }}</div>
                    <div class="mb-2"><span class="font-medium">User Agent:</span> <span class="break-all">{{ $log->user_agent ?: '-' }}</span></div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Metadata</h3>
                <pre class="text-xs leading-relaxed whitespace-pre-wrap break-all bg-slate-50 dark:bg-slate-900/40 p-4 rounded-lg">{{ json_encode($log->metadata, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 lg:col-span-2">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Request Data</h3>
                <pre class="text-xs leading-relaxed whitespace-pre-wrap break-all bg-slate-50 dark:bg-slate-900/40 p-4 rounded-lg">{{ json_encode($log->request_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection


