@extends('admin.layouts.app')

@section('page-title', 'تعديل سجل تدقيق')
@section('page-subtitle', 'يمكن تعديل التفاصيل غير الحساسة للتوثيق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 p-6">
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white bg-gradient-to-r from-amber-600 to-amber-500 bg-clip-text text-transparent">تعديل سجل التدقيق</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-lg mt-1">تحديث وتعديل تفاصيل السجل الحالي</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <!-- Log ID Badge -->
                <div class="text-center">
                    <div class="text-xs text-slate-400 mb-1">معرف السجل</div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-sm font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                        #{{ $log->id }}
                    </span>
                </div>
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a href="{{ route('admin.audit-logs.show', $log) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        عرض التفاصيل
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white font-medium rounded-xl shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Edit Form Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">✏️ تحديث سجل التدقيق #{{ $log->id }}</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.audit-logs.update', $log) }}" class="p-8">
            @csrf
            @method('PUT')
            <!-- Essential Information Section -->
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">📋 المعلومات الأساسية</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">⚡ العملية المنفذة <span class="text-red-400">*</span></label>
                        <input name="action" 
                               value="{{ old('action', $log->action) }}"
                               required 
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                        <p class="text-xs text-slate-400">مثل: user.created, post.updated, login.success</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">✅ حالة العملية</label>
                        <input name="status" 
                               value="{{ old('status', $log->status) }}"
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                        <p class="text-xs text-slate-400">حالة تنفيذ العملية</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">⚠️ مستوى الخطورة</label>
                        <select name="severity" 
                                class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                            <option value="">اختر مستوى الخطورة</option>
                            <option value="low" {{ old('severity', $log->severity) === 'low' ? 'selected' : '' }}>🟢 منخفض (Low)</option>
                            <option value="medium" {{ old('severity', $log->severity) === 'medium' ? 'selected' : '' }}>🟡 متوسط (Medium)</option>
                            <option value="high" {{ old('severity', $log->severity) === 'high' ? 'selected' : '' }}>🟠 عالي (High)</option>
                            <option value="critical" {{ old('severity', $log->severity) === 'critical' ? 'selected' : '' }}>🔴 خطير (Critical)</option>
                        </select>
                        <p class="text-xs text-slate-400">تحديد أهمية هذه العملية</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-white">📂 فئة العملية</label>
                        <input name="category" 
                               value="{{ old('category', $log->category) }}"
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300" 
                               placeholder="auth, users, posts, settings...">
                        <p class="text-xs text-slate-400">تصنيف العملية حسب النوع</p>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="mb-10">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">📝 وصف العملية</label>
                    <textarea name="description" 
                              rows="4" 
                              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 resize-none"
                              placeholder="وصف تفصيلي للعملية التي تم تنفيذها...">{{ old('description', $log->description) }}</textarea>
                    <p class="text-xs text-slate-400">شرح مفصل عن طبيعة العملية والغرض منها</p>
                </div>
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

            <!-- Action Buttons -->
            <div class="border-t border-slate-200 dark:border-slate-700 pt-8 mt-10">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-400">
                        <span class="font-medium">آخر تحديث:</span> {{ $log->updated_at->format('Y-m-d H:i:s') }}
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.audit-logs.index') }}" 
                           class="inline-flex items-center gap-2 px-8 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white font-medium rounded-xl transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            إلغاء التعديل
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            حفظ التعديلات
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


