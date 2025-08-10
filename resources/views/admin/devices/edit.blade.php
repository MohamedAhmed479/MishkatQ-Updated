@extends('admin.layouts.app')

@section('page-title', 'تعديل الجهاز')
@section('page-subtitle', 'تحديث بيانات الجهاز: ' . $device->device_name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.devices.index') }}" 
               class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white">
                    @switch($device->device_type)
                        @case('mobile')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            @break
                        @case('tablet')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            @break
                        @case('desktop')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            @break
                        @default
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                    @endswitch
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">تعديل الجهاز</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $device->device_name }} • {{ $device->user->name }}</p>
                </div>
            </div>
        </div>

        <!-- Device Status Card -->
        <div class="card-elegant rounded-xl p-4 mb-6 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-4">
                    <div class="text-slate-600 dark:text-slate-400">
                        <span class="font-medium">رقم الجهاز:</span> #{{ $device->id }}
                    </div>
                    <div class="text-slate-600 dark:text-slate-400">
                        <span class="font-medium">تاريخ التسجيل:</span> {{ $device->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($device->token)
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">نشط</span>
                    @else
                        <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                        <span class="text-slate-600 dark:text-slate-400 font-medium">غير نشط</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card-elegant rounded-xl p-6">
            <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- User Selection -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            المستخدم *
                        </span>
                    </label>
                    <select name="user_id" required 
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        <option value="">اختر المستخدم</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $device->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Device Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Device Name -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                اسم الجهاز *
                            </span>
                        </label>
                        <input type="text" name="device_name" value="{{ old('device_name', $device->device_name) }}" required 
                               class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" 
                               placeholder="مثال: iPhone 15 Pro" />
                        @error('device_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Device Type -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                نوع الجهاز *
                            </span>
                        </label>
                        <select name="device_type" required 
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <option value="">اختر نوع الجهاز</option>
                            @foreach($deviceTypes as $type)
                                <option value="{{ $type }}" {{ old('device_type', $device->device_type) == $type ? 'selected' : '' }}>
                                    @switch($type)
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
                                            {{ ucfirst($type) }}
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                        @error('device_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Platform and Browser -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Platform -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                المنصة *
                            </span>
                        </label>
                        <select name="platform" required 
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <option value="">اختر المنصة</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform }}" {{ old('platform', $device->platform) == $platform ? 'selected' : '' }}>
                                    {{ $platform }}
                                </option>
                            @endforeach
                        </select>
                        @error('platform')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Browser -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                                المتصفح
                            </span>
                        </label>
                        <select name="browser" 
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <option value="">اختر المتصفح (اختياري)</option>
                            @foreach($browsers as $browser)
                                <option value="{{ $browser }}" {{ old('browser', $device->browser) == $browser ? 'selected' : '' }}>
                                    {{ $browser }}
                                </option>
                            @endforeach
                        </select>
                        @error('browser')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- IP Address -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            عنوان IP
                        </span>
                    </label>
                    <input type="text" name="ip_address" value="{{ old('ip_address', $device->ip_address) }}" 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" 
                           placeholder="مثال: 192.168.1.100" />
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">عنوان IP الخاص بالجهاز (اختياري)</p>
                    @error('ip_address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" 
                            class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.devices.index') }}" 
                       class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        <!-- Token Information -->
        @if($device->token)
            <div class="mt-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <div class="text-sm">
                        <div class="text-emerald-800 dark:text-emerald-300 font-medium mb-1">معلومات الرمز المميز:</div>
                        <p class="text-emerald-700 dark:text-emerald-400 mb-2">
                            هذا الجهاز مرتبط برمز مميز نشط يمكن للمستخدم من خلاله الوصول للنظام.
                        </p>
                        <div class="text-emerald-600 dark:text-emerald-400 text-xs space-y-1">
                            <div><strong>تاريخ الإنشاء:</strong> {{ $device->token->created_at->format('d/m/Y H:i') }}</div>
                            @if($device->token->last_used_at)
                                <div><strong>آخر استخدام:</strong> {{ $device->token->last_used_at->format('d/m/Y H:i') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-6 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm">
                        <div class="text-slate-800 dark:text-slate-300 font-medium mb-1">لا يوجد رمز مميز:</div>
                        <p class="text-slate-600 dark:text-slate-400">
                            هذا الجهاز غير مرتبط بأي رمز مميز حالياً. قد يكون المستخدم قد سجل خروج من هذا الجهاز أو انتهت صلاحية الرمز المميز.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
