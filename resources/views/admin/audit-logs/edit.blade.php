@extends('admin.layouts.app')

@section('page-title', 'تعديل سجل تدقيق')
@section('page-subtitle', 'يمكن تعديل التفاصيل غير الحساسة للتوثيق')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تعديل سجل</h1>
        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">العودة</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <form method="POST" action="{{ route('admin.audit-logs.update', $log) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الأكشن</label>
                <input name="action" value="{{ old('action', $log->action) }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الحالة</label>
                <input name="status" value="{{ old('status', $log->status) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الشدة</label>
                <select name="severity" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="">—</option>
                    <option value="low" {{ old('severity', $log->severity) === 'low' ? 'selected' : '' }}>low</option>
                    <option value="medium" {{ old('severity', $log->severity) === 'medium' ? 'selected' : '' }}>medium</option>
                    <option value="high" {{ old('severity', $log->severity) === 'high' ? 'selected' : '' }}>high</option>
                    <option value="critical" {{ old('severity', $log->severity) === 'critical' ? 'selected' : '' }}>critical</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الفئة</label>
                <input name="category" value="{{ old('category', $log->category) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">{{ old('description', $log->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">المستخدم</label>
                <select name="user_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="">—</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (string) old('user_id', $log->user_id) === (string) $user->id ? 'selected' : '' }}>{{ $user->name }} — {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">نوع المستخدم</label>
                <input name="user_type" value="{{ old('user_type', $log->user_type) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">اسم المستخدم</label>
                <input name="user_name" value="{{ old('user_name', $log->user_name) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">بريد المستخدم</label>
                <input name="user_email" type="email" value="{{ old('user_email', $log->user_email) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>

            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">النموذج (Model)</label>
                <input name="model_type" value="{{ old('model_type', $log->model_type) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">المعرف</label>
                <input name="model_id" type="number" value="{{ old('model_id', $log->model_id) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">اسم النموذج</label>
                <input name="model_name" value="{{ old('model_name', $log->model_name) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">Old Values (JSON)</label>
                <textarea name="old_values" rows="3" class="font-mono text-sm w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">{{ old('old_values', json_encode($log->old_values, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">New Values (JSON)</label>
                <textarea name="new_values" rows="3" class="font-mono text-sm w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">{{ old('new_values', json_encode($log->new_values, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) }}</textarea>
            </div>
            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">IP</label>
                    <input name="ip_address" value="{{ old('ip_address', $log->ip_address) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">User Agent</label>
                    <input name="user_agent" value="{{ old('user_agent', $log->user_agent) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:col-span-2">
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">Method</label>
                    <select name="method" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                        <option value="">—</option>
                        @foreach(['GET','POST','PUT','PATCH','DELETE'] as $m)
                            <option value="{{ $m }}" {{ old('method', $log->method) === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">URL</label>
                    <input name="url" value="{{ old('url', $log->url) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">Request Data (JSON)</label>
                <textarea name="request_data" rows="3" class="font-mono text-sm w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">{{ old('request_data', json_encode($log->request_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:col-span-2">
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">حساس؟</label>
                    <select name="is_sensitive" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                        <option value="">—</option>
                        <option value="1" {{ old('is_sensitive', (int) $log->is_sensitive) === 1 ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ old('is_sensitive', (int) $log->is_sensitive) === 0 ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">Session ID</label>
                    <input name="session_id" value="{{ old('session_id', $log->session_id) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">التاريخ</label>
                    <input type="datetime-local" name="performed_at" value="{{ old('performed_at', optional($log->performed_at)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">Device Info</label>
                <textarea name="device_info" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">{{ old('device_info', $log->device_info) }}</textarea>
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">Metadata (JSON)</label>
                    <textarea name="metadata" rows="3" class="font-mono text-sm w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">{{ old('metadata', json_encode($log->metadata, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) }}</textarea>
                </div>
            </div>

            <div class="md:col-span-2 flex items-center justify-end gap-3">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">حفظ</button>
                <a href="{{ route('admin.audit-logs.index') }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection


