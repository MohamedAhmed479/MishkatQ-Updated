@extends('admin.layouts.app')

@section('page-title', 'تفاصيل الجهاز')
@section('page-subtitle', 'عرض جميع معلومات الجهاز: ' . $device->device_name)

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.devices.index') }}" 
                   class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <!-- Device Icon -->
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                        @switch($device->device_type)
                            @case('mobile')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                @break
                            @case('tablet')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                @break
                            @case('desktop')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                @break
                            @default
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                        @endswitch
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $device->device_name }}</h1>
                        <p class="text-slate-600 dark:text-slate-400">{{ ucfirst($device->device_type) }} • {{ $device->platform }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.devices.edit', $device) }}" 
                   class="flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تعديل الجهاز
                </a>
            </div>
        </div>

        <!-- Device Status Card -->
        <div class="card-elegant rounded-xl p-4 mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            #{{ $device->id }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">رقم الجهاز</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $device->created_at->diffForHumans() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">تاريخ التسجيل</div>
                    </div>
                    @if($tokenInfo)
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                @if($tokenInfo['last_used_at'])
                                    {{ $tokenInfo['last_used_at']->diffForHumans() }}
                                @else
                                    لم يُستخدم
                                @endif
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">آخر استخدام</div>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($device->token)
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">نشط</span>
                    @else
                        <div class="w-3 h-3 rounded-full bg-slate-400"></div>
                        <span class="text-slate-600 dark:text-slate-400 font-medium">غير نشط</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Device Information Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">معلومات الجهاز</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">اسم الجهاز</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $device->device_name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">نوع الجهاز</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    @switch($device->device_type)
                                        @case('mobile')
                                            هاتف محمول
                                            @break
                                        @case('tablet')
                                            جهاز لوحي
                                            @break
                                        @case('desktop')
                                            جهاز مكتبي
                                            @break
                                        @default
                                            {{ ucfirst($device->device_type) }}
                                    @endswitch
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">المنصة</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $device->platform }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">المتصفح</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $device->browser ?: 'غير محدد' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">عنوان IP</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800">
                                <span class="font-mono text-slate-900 dark:text-slate-100">
                                    {{ $device->ip_address ?: 'غير محدد' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">تاريخ التسجيل</label>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                                {{ $device->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Information Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">معلومات المستخدم</h2>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($device->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-lg text-slate-800 dark:text-slate-100">{{ $device->user->name }}</div>
                            <div class="text-slate-600 dark:text-slate-400">{{ $device->user->email }}</div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">
                                انضم في {{ $device->user->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $device->user) }}" 
                               class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" 
                               title="عرض ملف المستخدم">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.devices.user-devices', $device->user) }}" 
                               class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors" 
                               title="عرض جميع أجهزة المستخدم">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Token Information Card -->
                @if($tokenInfo)
                    <div class="card-elegant rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">معلومات الرمز المميز</h2>
                        </div>

                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">تاريخ الإنشاء</label>
                                    <div class="text-slate-900 dark:text-slate-100 font-medium">
                                        {{ $tokenInfo['created_at']->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $tokenInfo['created_at']->diffForHumans() }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">آخر استخدام</label>
                                    <div class="text-slate-900 dark:text-slate-100 font-medium">
                                        @if($tokenInfo['last_used_at'])
                                            {{ $tokenInfo['last_used_at']->format('d/m/Y H:i') }}
                                        @else
                                            لم يُستخدم بعد
                                        @endif
                                    </div>
                                    @if($tokenInfo['last_used_at'])
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $tokenInfo['last_used_at']->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>
                                @if($tokenInfo['expires_at'])
                                    <div>
                                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">تاريخ الانتهاء</label>
                                        <div class="text-slate-900 dark:text-slate-100 font-medium">
                                            {{ $tokenInfo['expires_at']->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-xs {{ $tokenInfo['is_active'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $tokenInfo['is_active'] ? 'نشط' : 'منتهي الصلاحية' }}
                                        </div>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">الحالة</label>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $tokenInfo['is_active'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $tokenInfo['is_active'] ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                                        {{ $tokenInfo['is_active'] ? 'نشط' : 'منتهي الصلاحية' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-elegant rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">معلومات الرمز المميز</h2>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-6 text-center">
                            <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا يوجد رمز مميز</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                هذا الجهاز غير مرتبط بأي رمز مميز حالياً. قد يكون المستخدم قد سجل خروج من هذا الجهاز أو انتهت صلاحية الرمز المميز.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Device Status Card -->
                <div class="card-elegant rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">إحصائيات</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                {{ $device->created_at->diffInDays() }}
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">يوم منذ التسجيل</div>
                        </div>
                        
                        @if($tokenInfo && $tokenInfo['last_used_at'])
                            <div class="text-center p-4 rounded-lg bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                                    {{ $tokenInfo['last_used_at']->diffInDays() }}
                                </div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">يوم منذ آخر استخدام</div>
                            </div>
                        @endif

                        <div class="text-center p-4 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                                {{ $device->token ? 'نشط' : 'غير نشط' }}
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">حالة الجهاز</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card-elegant rounded-xl p-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.devices.edit', $device) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل الجهاز
                        </a>
                        
                        <a href="{{ route('admin.users.show', $device->user) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            عرض ملف المستخدم
                        </a>

                        <a href="{{ route('admin.devices.user-devices', $device->user) }}" 
                           class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            أجهزة المستخدم الأخرى
                        </a>

                        @if($device->token)
                            <hr class="border-slate-200 dark:border-slate-700">
                            
                            <form method="POST" action="{{ route('admin.devices.revoke-token', $device) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors text-yellow-600 dark:text-yellow-400 w-full"
                                        onclick="return confirm('هل تريد إلغاء الرمز المميز لهذا الجهاز؟\n\nسيتم تسجيل خروج المستخدم من هذا الجهاز.')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    إلغاء الرمز المميز
                                </button>
                            </form>
                        @endif

                        <hr class="border-slate-200 dark:border-slate-700">
                        
                        <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="w-full" 
                              onsubmit="return confirm('هل تريد حذف هذا الجهاز نهائياً؟\n\nسيتم حذف:\n- الجهاز وجميع بياناته\n- الرمز المميز المرتبط به\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600 dark:text-red-400 w-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                حذف الجهاز
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
