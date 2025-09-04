@extends('admin.layouts.app')

@section('page-title', 'تفاصيل السجل')
@section('page-subtitle', 'عرض كامل لمحتوى سجل التدقيق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 p-6">
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">تفاصيل السجل</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-lg mt-1">عرض شامل ومفصل لسجل التدقيق</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <!-- Severity Badge -->
                @php
                    $sevColors = [
                        'critical' => 'bg-red-500 text-white',
                        'high' => 'bg-orange-500 text-white',
                        'medium' => 'bg-blue-500 text-white',
                        'low' => 'bg-green-500 text-white',
                    ];
                    $sevCls = $sevColors[$log->severity ?? 'low'] ?? $sevColors['low'];
                @endphp
                <div class="text-center">
                    <div class="text-xs text-slate-400 mb-1">مستوى الخطورة</div>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold {{ $sevCls }} shadow-lg">
                        @if($log->severity === 'critical') 🔴
                        @elseif($log->severity === 'high') 🟠
                        @elseif($log->severity === 'medium') 🟡
                        @else 🟢
                        @endif
                        {{ ucfirst($log->severity ?: 'low') }}
                    </span>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a href="{{ route('admin.audit-logs.edit', $log) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 @permClass('audit-logs.edit')" @permDisabled('audit-logs.edit')>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل السجل
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

    <!-- Enhanced Info Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Essential Information Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">📋 البيانات الأساسية</h3>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <!-- Date & Time -->
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-white">📅 التاريخ والوقت</span>
                    </div>
                    <span class="font-bold text-white bg-blue-100 dark:bg-blue-900/30 px-3 py-1 rounded-lg">
                        {{ $log->performed_at ? $log->performed_at->format('Y-m-d H:i:s') : $log->created_at->format('Y-m-d H:i:s') }}
                    </span>
                </div>

                <!-- Action -->
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-white">⚡ العملية المنفذة</span>
                    </div>
                    <span class="font-bold text-white bg-purple-100 dark:bg-purple-900/30 px-3 py-1 rounded-lg">
                        {{ $log->action_display_name }}
                    </span>
                </div>

                <!-- Status -->
                @if($log->status)
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-white">✅ حالة العملية</span>
                    </div>
                    <span class="font-bold text-white bg-green-100 dark:bg-green-900/30 px-3 py-1 rounded-lg">
                        {{ ucfirst($log->status) }}
                    </span>
                </div>
                @endif

                <!-- Description if available -->
                @if($log->description)
                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                        </div>
                        <span class="font-medium text-white">📝 وصف العملية</span>
                    </div>
                    <p class="text-white bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded-lg leading-relaxed">{{ $log->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- User Information Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">👤 معلومات المستخدم</h3>
                </div>
            </div>
            <div class="p-6">
                <!-- User Avatar & Basic Info -->
                <div class="flex items-center gap-4 p-4 bg-slate-50 dark:bg-slate-700 rounded-xl mb-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-lg">
                        {{ strtoupper(substr($log->user_name ?? 'س', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-lg text-white">{{ $log->user_name ?? 'النظام الآلي' }}</div>
                        <div class="text-slate-400 text-sm">{{ $log->user_email ?: 'غير محدد' }}</div>
                        @if($log->user_type)
                        <div class="text-xs text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2 py-1 rounded mt-1 inline-block">
                            {{ $log->user_type }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- User Details Grid -->
                <div class="grid grid-cols-1 gap-4">
                    @if($log->user_id)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                        <span class="font-medium text-white flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                            معرف المستخدم
                        </span>
                        <span class="font-bold text-white bg-slate-200 dark:bg-slate-600 px-3 py-1 rounded">#{{ $log->user_id }}</span>
                    </div>
                    @endif
                    
                    @if($log->ip_address)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                        <span class="font-medium text-white flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            عنوان IP
                        </span>
                        <span class="font-bold text-white bg-slate-200 dark:bg-slate-600 px-3 py-1 rounded font-mono">{{ $log->ip_address }}</span>
                    </div>
                    @endif

                    @if($log->session_id)
                    <div class="p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                        <span class="font-medium text-white flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                            معرف الجلسة
                        </span>
                        <code class="text-xs text-white bg-slate-200 dark:bg-slate-600 p-2 rounded block break-all">{{ $log->session_id }}</code>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Target Entity Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 lg:col-span-2 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">🎯 الكيان المستهدف</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <span class="font-medium text-white">نوع النموذج</span>
                        </div>
                        <div class="font-bold text-white bg-purple-100 dark:bg-purple-900/30 px-3 py-2 rounded-lg">
                            {{ $log->model_type ? class_basename($log->model_type) : 'غير محدد' }}
                        </div>
                        @if($log->model_type)
                        <div class="text-xs text-slate-400 mt-1 font-mono">{{ $log->model_type }}</div>
                        @endif
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <span class="font-medium text-white">المعرف</span>
                        </div>
                        <div class="font-bold text-white bg-blue-100 dark:bg-blue-900/30 px-3 py-2 rounded-lg">
                            {{ $log->model_id ? '#' . $log->model_id : 'غير محدد' }}
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="font-medium text-white">اسم الكيان</span>
                        </div>
                        <div class="font-bold text-white bg-green-100 dark:bg-green-900/30 px-3 py-2 rounded-lg break-all">
                            {{ $log->model_name ?: 'غير محدد' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Changes Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 lg:col-span-2 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">🔄 التغييرات المنفذة</h3>
                </div>
            </div>
            <div class="p-6">
                @php $changes = $log->formatted_changes; @endphp
                @if(!empty($changes))
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800">
                                <tr>
                                    <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase">🏷️ الحقل</th>
                                    <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase">🔴 القيمة القديمة</th>
                                    <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase">🟢 القيمة الجديدة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($changes as $field => $diff)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-white bg-indigo-100 dark:bg-indigo-900/30 px-3 py-1 rounded-lg">{{ $field }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                                <code class="text-sm text-white break-all">{{ is_array($diff['old']) ? json_encode($diff['old'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : (string) $diff['old'] }}</code>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                                <code class="text-sm text-white break-all">{{ is_array($diff['new']) ? json_encode($diff['new'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : (string) $diff['new'] }}</code>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-white mb-2">لا توجد تغييرات</h4>
                        <p class="text-slate-400">لم يتم تسجيل أي تغييرات في هذه العملية</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Request Information Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">🌐 معلومات الطلب</h3>
                </div>
            </div>
            <div class="p-6 space-y-4">
                @if($log->method)
                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                        <span class="font-medium text-white">HTTP Method</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                        {{ $log->method }}
                    </span>
                </div>
                @endif

                @if($log->url)
                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="font-medium text-white">URL المطلوب</span>
                    </div>
                    <code class="text-sm text-white bg-slate-200 dark:bg-slate-600 p-2 rounded block break-all">{{ $log->url }}</code>
                </div>
                @endif

                @if($log->user_agent)
                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <span class="font-medium text-white">User Agent</span>
                    </div>
                    <code class="text-xs text-white bg-slate-200 dark:bg-slate-600 p-2 rounded block break-all leading-relaxed">{{ $log->user_agent }}</code>
                </div>
                @endif
            </div>
        </div>

        <!-- Technical Data Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">⚙️ البيانات التقنية</h3>
                </div>
            </div>
            <div class="p-6">
                @if($log->metadata && !empty($log->metadata))
                <div class="mb-6">
                    <h4 class="font-semibold text-white mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                        Metadata
                    </h4>
                    <div class="bg-slate-100 dark:bg-slate-900/40 rounded-xl p-4 overflow-x-auto">
                        <pre class="text-xs text-white leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($log->metadata, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif

                @if($log->request_data && !empty($log->request_data))
                <div>
                    <h4 class="font-semibold text-white mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                        Request Data
                    </h4>
                    <div class="bg-slate-100 dark:bg-slate-900/40 rounded-xl p-4 overflow-x-auto">
                        <pre class="text-xs text-white leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($log->request_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif

                @if((!$log->metadata || empty($log->metadata)) && (!$log->request_data || empty($log->request_data)))
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-slate-400">لا توجد بيانات تقنية إضافية</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


