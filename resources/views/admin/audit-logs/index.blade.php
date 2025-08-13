@extends('admin.layouts.app')

@section('page-title', 'سجلات التدقيق')
@section('page-subtitle', 'استعراض وفلاتر السجلات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 p-6">
    
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif
    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
        <div>
                    <h1 class="text-3xl font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 bg-clip-text text-transparent">سجلات التدقيق</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-lg mt-1">تتبع العمليات الهامة عبر النظام بسهولة ووضوح</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-sm text-slate-500 dark:text-slate-400">إجمالي السجلات</div>
                    <div class="text-2xl font-bold text-white">{{ $logs->total() ?? 0 }}</div>
        </div>
                <a href="{{ route('admin.audit-logs.create') }}" 
                   class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 @permClass('audit-logs.create') @if(!auth('admin')->user()->can('audit-logs.create')) permission-disabled tooltip-disabled @endif" 
                   @permDisabled('audit-logs.create')
                   data-tooltip="ليس لديك صلاحية لإضافة سجلات جديدة">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
                    إضافة سجل جديد
        </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Search & Filters Section -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 mb-8">
        <!-- Search Header -->
        <div class="border-b border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">البحث والفلترة المتقدمة</h2>
            </div>
        </div>
        
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="p-6">
            <!-- Quick Search Bar -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-3 text-white">🔍 البحث السريع</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ $search }}" 
                           placeholder="ابحث في الأكشن، المستخدم، الوصف، أو أي تفاصيل أخرى..." 
                           class="w-full px-5 py-4 pl-12 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- User Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">👤 المستخدم</label>
                    <select name="user_id" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    <option value="">جميع المستخدمين</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (string)$userId === (string)$user->id ? 'selected' : '' }}>{{ $user->name }} — {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>

                <!-- Severity Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">⚠️ مستوى الخطورة</label>
                    <select name="severity" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        <option value="">جميع المستويات</option>
                    @foreach($severities as $s)
                            <option value="{{ $s }}" {{ $severity === $s ? 'selected' : '' }}>
                                @if($s === 'critical') 🔴 خطير
                                @elseif($s === 'high') 🟠 عالي
                                @elseif($s === 'medium') 🟡 متوسط
                                @elseif($s === 'low') 🟢 منخفض
                                @else {{ $s }}
                                @endif
                            </option>
                    @endforeach
                </select>
            </div>

                <!-- Status Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">✅ الحالة</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        <option value="">جميع الحالات</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

                <!-- Method Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">🔧 طريقة الطلب</label>
                    <select name="method" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        <option value="">جميع الطرق</option>
                    @foreach($methods as $m)
                        <option value="{{ $m }}" {{ $method === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

                <!-- Category Filter -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">📂 الفئة</label>
                    <input type="text" name="category" value="{{ $category }}" 
                           placeholder="مثل: auth, users..." 
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white placeholder-slate-400 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <!-- Date Range -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">📅 من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">📅 إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <!-- Sensitive Checkbox -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white">🔒 خيارات إضافية</label>
                    <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700">
                        <input id="is_sensitive" type="checkbox" name="is_sensitive" value="1" {{ $isSensitive ? 'checked' : '' }} 
                               class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500">
                        <label for="is_sensitive" class="text-sm text-white">عمليات حساسة فقط</label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-4">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.audit-logs.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white font-medium rounded-xl transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        إعادة تعيين
                    </a>
            </div>
                @if($search || $userId || $severity || $status || $category || $method || $isSensitive || $startDate || $endDate)
                <div class="text-sm text-white bg-emerald-100 dark:bg-emerald-900/30 px-4 py-2 rounded-lg">
                    <span class="font-medium">الفلاتر النشطة:</span>
                    @if($search) البحث: "{{ $search }}" @endif
                    @if($userId) المستخدم @endif
                    @if($severity) الخطورة: {{ $severity }} @endif
                    @if($status) الحالة: {{ $status }} @endif
            </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Date Range Delete Section -->
    <div class="mb-8 p-6 bg-gradient-to-r from-orange-700/20 to-red-700/20 rounded-xl border border-orange-500/30 shadow-lg">
        <div class="flex items-center gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-orange-500/20 rounded-xl">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">حذف السجلات بالتاريخ</h3>
                    <p class="text-sm text-slate-300">حدد فترة زمنية لحذف جميع السجلات ضمنها</p>
                </div>
            </div>
        </div>

        <form id="dateRangeDeleteForm" class="space-y-4">
            @csrf
            @method('DELETE')
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- From Date -->
                <div class="space-y-2">
                    <label for="delete_from_date" class="block text-sm font-medium text-white">
                        🗓️ من تاريخ
                    </label>
                    <input type="date" 
                           id="delete_from_date" 
                           name="from_date"
                           class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                           placeholder="اختر التاريخ الأول">
                </div>

                <!-- To Date -->
                <div class="space-y-2">
                    <label for="delete_to_date" class="block text-sm font-medium text-white">
                        🗓️ إلى تاريخ
                    </label>
                    <input type="date" 
                           id="delete_to_date" 
                           name="to_date"
                           class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                           placeholder="اختر التاريخ الثاني">
                </div>

                <!-- Time Selection -->
                <div class="space-y-2">
                    <label for="delete_time_option" class="block text-sm font-medium text-white">
                        ⏰ خيار الوقت
                    </label>
                    <select id="delete_time_option" 
                            name="time_option"
                            class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
                        <option value="full_day">اليوم كاملاً</option>
                        <option value="custom_time">وقت محدد</option>
                    </select>
                </div>

                <!-- Action Button -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-transparent">
                        &nbsp;
                    </label>
                    <button type="button" 
                            onclick="deleteByDateRange()" 
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 @if(!auth('admin')->user()->can('audit-logs.delete')) permission-disabled tooltip-disabled @endif"
                            data-tooltip="ليس لديك صلاحية لحذف السجلات"
                            @if(!auth('admin')->user()->can('audit-logs.delete')) disabled @endif>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        حذف بالتاريخ
                    </button>
                </div>
            </div>

            <!-- Custom Time Inputs (Hidden by default) -->
            <div id="customTimeInputs" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-orange-500/20">
                <div class="space-y-2">
                    <label for="delete_from_time" class="block text-sm font-medium text-white">
                        🕐 الوقت من
                    </label>
                    <input type="time" 
                           id="delete_from_time" 
                           name="from_time"
                           class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
                </div>

                <div class="space-y-2">
                    <label for="delete_to_time" class="block text-sm font-medium text-white">
                        🕐 الوقت إلى
                    </label>
                    <input type="time" 
                           id="delete_to_time" 
                           name="to_time"
                           class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
                </div>
            </div>

            <!-- Preview Section -->
            <div id="dateRangePreview" class="hidden mt-4 p-4 bg-slate-800/50 rounded-xl border border-slate-600">
                <div class="flex items-center gap-3 mb-3">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-blue-400">معاينة الحذف</span>
                </div>
                <div id="previewContent" class="text-sm text-slate-300"></div>
                <div id="previewCount" class="mt-2 text-sm font-medium"></div>
            </div>

            <!-- Warning -->
            <div class="flex items-start gap-3 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl">
                <svg class="w-6 h-6 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-yellow-400 mb-1">⚠️ تحذير مهم</p>
                    <p class="text-sm text-yellow-300">
                        هذا الإجراء سيحذف جميع السجلات ضمن الفترة المحددة بشكل نهائي ولا يمكن التراجع عنه. 
                        تأكد من التواريخ قبل المتابعة.
                    </p>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Section -->
    @if($logs->count() > 0)
    <div id="bulkActionsBar" class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-6 mb-8 hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">الإجراءات المتعددة</h3>
                        <p class="text-sm text-slate-400">تم تحديد <span id="selectedCount">0</span> سجل</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" 
                        onclick="clearSelection()" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white font-medium rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    إلغاء التحديد
                </button>
                <button type="button" 
                        onclick="deleteSelected()" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 @if(!auth('admin')->user()->can('audit-logs.delete')) permission-disabled tooltip-disabled @endif"
                        data-tooltip="ليس لديك صلاحية لحذف السجلات"
                        @if(!auth('admin')->user()->can('audit-logs.delete')) disabled @endif>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف المحدد
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Enhanced Data Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        @if($logs->count() > 0)
            <!-- Table Header -->
            <div class="border-b border-slate-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">جدول السجلات</h3>
                            <p class="text-sm text-slate-400">عرض {{ $logs->count() }} من {{ $logs->total() }} سجل</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <span>صفحة {{ $logs->currentPage() }} من {{ $logs->lastPage() }}</span>
                    </div>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                <div class="flex items-center justify-center">
                                    <input type="checkbox" 
                                           id="selectAll" 
                                           class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500 bg-white"
                                           onchange="toggleSelectAll()">
                                    <label for="selectAll" class="sr-only">تحديد الكل</label>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                📅 التاريخ والوقت
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                👤 المستخدم
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                ⚡ العملية
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                🎯 الهدف
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                ⚠️ الخطورة
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                ✅ الحالة
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                                🛠️ الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($logs as $log)
                        @php
                            $sevMap = [
                                'critical' => 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg',
                                'high' => 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg',
                                'medium' => 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg',
                                'low' => 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg',
                            ];
                            $sevCls = $sevMap[$log->severity ?? 'low'] ?? $sevMap['low'];
                            
                            $statusColors = [
                                'success' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                                'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                            ];
                            $statusCls = $statusColors[$log->status ?? ''] ?? 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300';
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all duration-300 group">
                            <!-- Checkbox Column -->
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" 
                                       name="selected_logs[]" 
                                       value="{{ $log->id }}" 
                                       class="log-checkbox w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500 bg-white"
                                       onchange="updateBulkActions()">
                            </td>
                            
                            <!-- Date Column -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-white">
                                        {{ $log->performed_at ? $log->performed_at->format('Y-m-d') : $log->created_at->format('Y-m-d') }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $log->performed_at ? $log->performed_at->format('H:i:s') : $log->created_at->format('H:i:s') }}
                                    </div>
                                </div>
                            </td>

                            <!-- User Column -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-lg">
                                        {{ strtoupper(substr($log->user_name ?? 'س', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white text-sm">{{ $log->user_name ?? 'نظام' }}</div>
                                        <div class="text-xs text-slate-400">{{ Str::limit($log->user_email ?: 'غير محدد', 25) }}</div>
                                        @if($log->user_id)
                                        <div class="text-xs text-slate-500">ID: {{ $log->user_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Action Column -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="font-semibold text-white">{{ $log->action_display_name }}</div>
                                    @if($log->description)
                                    <div class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 p-2 rounded-lg">
                                        {{ Str::limit($log->description, 60) }}
                                    </div>
                                    @endif
                                    @if($log->method)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                        {{ $log->method }}
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Target Column -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($log->model_type)
                                    <div class="text-sm text-white font-medium">{{ class_basename($log->model_type) }}</div>
                                    @endif
                                    @if($log->model_name)
                                    <div class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">
                                        {{ Str::limit($log->model_name, 20) }}
                                    </div>
                                    @endif
                                    @if($log->model_id)
                                    <div class="text-xs text-slate-500">ID: {{ $log->model_id }}</div>
                                    @endif
                                    @if(!$log->model_type && !$log->model_name && !$log->model_id)
                                    <span class="text-sm text-slate-400">غير محدد</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Severity Column -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold {{ $sevCls }} transform transition-all duration-300 hover:scale-105">
                                    @if($log->severity === 'critical') 🔴
                                    @elseif($log->severity === 'high') 🟠
                                    @elseif($log->severity === 'medium') 🟡
                                    @else 🟢
                                    @endif
                                    {{ ucfirst($log->severity ?: 'low') }}
                                </span>
                            </td>

                            <!-- Status Column -->
                            <td class="px-6 py-4">
                                @if($log->status)
                                <span class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold {{ $statusCls }}">
                                    @if($log->status === 'success') ✅
                                    @elseif($log->status === 'failed') ❌
                                    @elseif($log->status === 'pending') ⏳
                                    @else 📝
                                    @endif
                                    {{ ucfirst($log->status) }}
                                </span>
                                @else
                                <span class="text-sm text-slate-400">غير محدد</span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.audit-logs.show', $log) }}" 
                                       class="p-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition-all duration-300 transform hover:scale-110 shadow-lg hover:shadow-xl @permClass('audit-logs.view') @if(!auth('admin')->user()->can('audit-logs.view')) permission-disabled tooltip-disabled @endif" 
                                       @permDisabled('audit-logs.view') 
                                       data-tooltip="ليس لديك صلاحية للعرض"
                                       title="عرض التفاصيل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.audit-logs.edit', $log) }}" 
                                       class="p-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white transition-all duration-300 transform hover:scale-110 shadow-lg hover:shadow-xl @permClass('audit-logs.edit') @if(!auth('admin')->user()->can('audit-logs.edit')) permission-disabled tooltip-disabled @endif" 
                                       @permDisabled('audit-logs.edit') 
                                       data-tooltip="ليس لديك صلاحية للتعديل"
                                       title="تعديل السجل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.audit-logs.destroy', $log) }}" method="POST" class="inline @permClass('audit-logs.delete')" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟ هذا الإجراء لا يمكن التراجع عنه.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                @permDisabled('audit-logs.delete') 
                                                class="p-3 rounded-xl bg-red-500 hover:bg-red-600 text-white transition-all duration-300 transform hover:scale-110 shadow-lg hover:shadow-xl @if(!auth('admin')->user()->can('audit-logs.delete')) permission-disabled tooltip-disabled @endif" 
                                                data-tooltip="ليس لديك صلاحية للحذف"
                                                title="حذف السجل">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <div class="border-t border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-white">
                        عرض {{ $logs->firstItem() }} إلى {{ $logs->lastItem() }} من أصل {{ $logs->total() }} سجل
                    </div>
                    <div>{{ $logs->links() }}</div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="p-20 text-center">
                <div class="flex flex-col items-center gap-6">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center shadow-xl">
                        <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-center space-y-2">
                        <h3 class="text-2xl font-bold text-white mb-2">📭 لا توجد سجلات</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-lg">لم يتم العثور على أي سجلات تطابق معايير البحث الحالية</p>
                        <div class="flex items-center justify-center gap-4 mt-6">
                            <a href="{{ route('admin.audit-logs.index') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105">
                                🔄 إعادة تعيين الفلاتر
                            </a>
                            <a href="{{ route('admin.audit-logs.create') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 @permClass('audit-logs.create')" 
                               @permDisabled('audit-logs.create')>
                                ➕ إضافة سجل جديد
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Hidden form for bulk delete -->
    <form id="bulkDeleteForm" action="{{ route('admin.audit-logs.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="log_ids" id="logIdsInput">
    </form>
</div>

<script>
// تحديث شريط الإجراءات المتعددة
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.log-checkbox:checked');
    const bulkBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    // تحديث عداد السجلات المحددة
    selectedCount.textContent = checkboxes.length;
    
    // إظهار أو إخفاء شريط الإجراءات
    if (checkboxes.length > 0) {
        bulkBar.classList.remove('hidden');
        bulkBar.classList.add('animate-fade-in');
    } else {
        bulkBar.classList.add('hidden');
        bulkBar.classList.remove('animate-fade-in');
    }
    
    // تحديث حالة checkbox تحديد الكل
    const totalCheckboxes = document.querySelectorAll('.log-checkbox');
    if (checkboxes.length === totalCheckboxes.length && totalCheckboxes.length > 0) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else if (checkboxes.length > 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
}

// تحديد أو إلغاء تحديد جميع السجلات
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.log-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActions();
}

// إلغاء تحديد جميع السجلات
function clearSelection() {
    const checkboxes = document.querySelectorAll('.log-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    selectAllCheckbox.checked = false;
    selectAllCheckbox.indeterminate = false;
    
    updateBulkActions();
}

// حذف السجلات المحددة
function deleteSelected() {
    // التحقق من الصلاحيات أولاً
    const deleteButton = document.querySelector('button[onclick="deleteSelected()"]');
    if (deleteButton && deleteButton.disabled) {
        alert('⚠️ ليس لديك صلاحية لحذف السجلات');
        return;
    }
    
    const checkboxes = document.querySelectorAll('.log-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('📝 الرجاء تحديد سجل واحد على الأقل للحذف');
        return;
    }
    
    // تجميع معرفات السجلات المحددة
    const logIds = Array.from(checkboxes).map(cb => cb.value);
    
    // رسالة تأكيد مخصصة مع أيقونات
    const confirmMessage = `🗑️ هل أنت متأكد من حذف ${checkboxes.length} سجل؟\n\n⚠️ تحذير: هذا الإجراء لا يمكن التراجع عنه!\n\n✅ اضغط موافق للمتابعة\n❌ اضغط إلغاء للتراجع`;
    
    if (confirm(confirmMessage)) {
        // إظهار رسالة تحميل
        const originalText = deleteButton.innerHTML;
        deleteButton.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> جاري الحذف...';
        deleteButton.disabled = true;
        
        // تعبئة الحقل المخفي بمعرفات السجلات
        document.getElementById('logIdsInput').value = logIds.join(',');
        
        // إرسال النموذج
        try {
            document.getElementById('bulkDeleteForm').submit();
        } catch (error) {
            // استعادة الزر في حالة حدوث خطأ
            deleteButton.innerHTML = originalText;
            deleteButton.disabled = false;
            alert('❌ حدث خطأ أثناء الحذف. يرجى المحاولة مرة أخرى.');
        }
    }
}

// إضافة تأثيرات بصرية
document.addEventListener('DOMContentLoaded', function() {
    // إضافة كلاسات CSS للتأثيرات
    const style = document.createElement('style');
    style.textContent = `
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .log-checkbox:checked + label,
        .log-checkbox:checked {
            animation: checkPulse 0.3s ease-in-out;
        }
        
        @keyframes checkPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        tr:has(.log-checkbox:checked) {
            background-color: rgba(52, 211, 153, 0.1) !important;
            border-color: rgba(52, 211, 153, 0.3);
        }
        
        .dark tr:has(.log-checkbox:checked) {
            background-color: rgba(52, 211, 153, 0.05) !important;
        }
        
        /* تأثيرات الماوس للأزرار المعطلة */
        button:disabled,
        a[disabled],
        .disabled,
        [disabled] {
            cursor: not-allowed !important;
            opacity: 0.6;
            pointer-events: auto !important;
        }
        
        button:disabled:hover,
        a[disabled]:hover,
        .disabled:hover,
        [disabled]:hover {
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
        }
        
        /* تأثيرات خاصة للأزرار مع الصلاحيات */
        .permission-disabled {
            cursor: not-allowed !important;
            opacity: 0.5;
            filter: grayscale(50%);
            position: relative;
        }
        
        .permission-disabled:hover {
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
        }
        
        .permission-disabled::after {
            content: '🔒';
            position: absolute;
            top: -8px;
            right: -8px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            z-index: 10;
        }
        
        /* تأثير تلميح للأزرار المعطلة */
        .tooltip-disabled {
            position: relative;
        }
        
        .tooltip-disabled:hover::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            margin-bottom: 5px;
        }
        
        .tooltip-disabled:hover::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }
    `;
    document.head.appendChild(style);
    
    // إضافة مستمع للأحداث على جميع checkboxes
    document.querySelectorAll('.log-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            if (this.checked) {
                row.classList.add('selected-row');
            } else {
                row.classList.remove('selected-row');
            }
        });
    });
    
    // إخفاء شريط الحذف المتعدد إذا لم تكن هناك صلاحية حذف
    const deleteButton = document.querySelector('button[onclick="deleteSelected()"]');
    if (deleteButton && deleteButton.disabled) {
        // إضافة رسالة توضيحية
        const bulkBar = document.getElementById('bulkActionsBar');
        if (bulkBar) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-2 rounded-lg text-sm';
            warningDiv.innerHTML = '⚠️ ليس لديك صلاحية لحذف السجلات';
            deleteButton.parentNode.replaceChild(warningDiv, deleteButton);
        }
    }
    
    // تحسين تجربة المستخدم للأزرار المعطلة
    document.querySelectorAll('.permission-disabled').forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // إظهار رسالة تنبيه مخصصة
            const tooltip = this.getAttribute('data-tooltip') || 'ليس لديك صلاحية لتنفيذ هذا الإجراء';
            
            // إنشاء toast notification
            showToast(tooltip, 'warning');
            
            return false;
        });
    });
    
    // دالة إظهار Toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform translate-x-0 transition-all duration-300 ${
            type === 'warning' ? 'bg-yellow-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // إزالة التنبيه بعد 3 ثواني
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    // إضافة دالة showToast للنطاق العام
    window.showToast = showToast;
    
    // إضافة مستمع لتغيير خيار الوقت
    const timeOptionSelect = document.getElementById('delete_time_option');
    const customTimeInputs = document.getElementById('customTimeInputs');
    
    if (timeOptionSelect && customTimeInputs) {
        timeOptionSelect.addEventListener('change', function() {
            if (this.value === 'custom_time') {
                customTimeInputs.classList.remove('hidden');
            } else {
                customTimeInputs.classList.add('hidden');
            }
            updateDateRangePreview();
        });
    }
    
    // إضافة مستمعين للتواريخ
    const fromDateInput = document.getElementById('delete_from_date');
    const toDateInput = document.getElementById('delete_to_date');
    const fromTimeInput = document.getElementById('delete_from_time');
    const toTimeInput = document.getElementById('delete_to_time');
    
    [fromDateInput, toDateInput, fromTimeInput, toTimeInput].forEach(input => {
        if (input) {
            input.addEventListener('change', updateDateRangePreview);
        }
    });
});

// دالة حذف السجلات بالتاريخ
function deleteByDateRange() {
    // التحقق من الصلاحيات أولاً
    const deleteButton = document.querySelector('button[onclick="deleteByDateRange()"]');
    if (deleteButton && deleteButton.disabled) {
        showToast('⚠️ ليس لديك صلاحية لحذف السجلات', 'warning');
        return;
    }
    
    const fromDate = document.getElementById('delete_from_date').value;
    const toDate = document.getElementById('delete_to_date').value;
    const timeOption = document.getElementById('delete_time_option').value;
    const fromTime = document.getElementById('delete_from_time').value;
    const toTime = document.getElementById('delete_to_time').value;
    
    // التحقق من صحة البيانات
    if (!fromDate || !toDate) {
        showToast('📅 يرجى تحديد التاريخ الأول والثاني', 'warning');
        return;
    }
    
    if (new Date(fromDate) > new Date(toDate)) {
        showToast('⚠️ التاريخ الأول يجب أن يكون قبل التاريخ الثاني', 'warning');
        return;
    }
    
    if (timeOption === 'custom_time' && (!fromTime || !toTime)) {
        showToast('🕐 يرجى تحديد الوقت الأول والثاني عند اختيار "وقت محدد"', 'warning');
        return;
    }
    
    // إنشاء رسالة تأكيد
    let dateRangeText = `من ${fromDate} إلى ${toDate}`;
    if (timeOption === 'custom_time' && fromTime && toTime) {
        dateRangeText += `\nمن الساعة ${fromTime} إلى ${toTime}`;
    }
    
    const confirmMessage = `🗑️ هل أنت متأكد من حذف جميع السجلات في الفترة التالية؟\n\n📅 ${dateRangeText}\n\n⚠️ تحذير: هذا الإجراء لا يمكن التراجع عنه!\n\n✅ اضغط موافق للمتابعة\n❌ اضغط إلغاء للتراجع`;
    
    if (confirm(confirmMessage)) {
        // إظهار رسالة تحميل
        const originalText = deleteButton.innerHTML;
        deleteButton.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> جاري الحذف...';
        deleteButton.disabled = true;
        
        // إنشاء FormData وإرسال الطلب
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('_method', 'DELETE');
        formData.append('from_date', fromDate);
        formData.append('to_date', toDate);
        formData.append('time_option', timeOption);
        
        if (timeOption === 'custom_time') {
            formData.append('from_time', fromTime);
            formData.append('to_time', toTime);
        }
        
        // إرسال الطلب
        fetch('{{ route("admin.audit-logs.bulk-delete-by-date") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(`✅ ${data.message}`, 'success');
                // إعادة تحميل الصفحة بعد ثانيتين
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showToast(`❌ ${data.message}`, 'error');
                // استعادة الزر
                deleteButton.innerHTML = originalText;
                deleteButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('❌ حدث خطأ أثناء الحذف. يرجى المحاولة مرة أخرى.', 'error');
            // استعادة الزر
            deleteButton.innerHTML = originalText;
            deleteButton.disabled = false;
        });
    }
}

// دالة تحديث معاينة الحذف
function updateDateRangePreview() {
    const fromDate = document.getElementById('delete_from_date').value;
    const toDate = document.getElementById('delete_to_date').value;
    const timeOption = document.getElementById('delete_time_option').value;
    const fromTime = document.getElementById('delete_from_time').value;
    const toTime = document.getElementById('delete_to_time').value;
    const previewDiv = document.getElementById('dateRangePreview');
    const previewContent = document.getElementById('previewContent');
    const previewCount = document.getElementById('previewCount');
    
    if (!fromDate || !toDate) {
        previewDiv.classList.add('hidden');
        return;
    }
    
    // إنشاء نص المعاينة
    let previewText = `📅 سيتم حذف جميع السجلات من ${fromDate} إلى ${toDate}`;
    
    if (timeOption === 'custom_time' && fromTime && toTime) {
        previewText += `<br>🕐 من الساعة ${fromTime} إلى ${toTime}`;
    } else {
        previewText += `<br>🕐 اليوم كاملاً (00:00 - 23:59)`;
    }
    
    previewContent.innerHTML = previewText;
    
    // حساب عدد السجلات المتوقع حذفها (تقدير)
    const daysDiff = Math.ceil((new Date(toDate) - new Date(fromDate)) / (1000 * 60 * 60 * 24)) + 1;
    previewCount.innerHTML = `📊 تقدير: ${daysDiff} يوم(أيام) من السجلات`;
    previewCount.className = 'mt-2 text-sm font-medium text-orange-400';
    
    previewDiv.classList.remove('hidden');
}
</script>

@endsection


