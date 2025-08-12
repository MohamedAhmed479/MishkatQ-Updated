@extends('admin.layouts.app')

@section('page-title', 'إدارة الأجهزة')
@section('page-subtitle', 'عرض وإدارة جميع أجهزة المستخدمين المسجلة في النظام')

@section('content')
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full lg:w-auto">
                <!-- Search -->
                <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
                    <div class="relative">
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="البحث في الأجهزة..."
                               class="pl-10 pr-4 py-2.5 w-64 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- User Filter -->
                    <select name="user_id"
                            class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700
           bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100
           focus:ring-2 focus:ring-emerald-500 focus:border-transparent">

                        <option value="">جميع المستخدمين</option>

                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userFilter === $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>


                    <!-- Device Type Filter -->
                    <select name="device_type" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">جميع أنواع الأجهزة</option>
                        @foreach($deviceTypes as $type)
                            <option value="{{ $type }}" {{ $deviceType === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Platform Filter -->
                    <select name="platform" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">جميع المنصات</option>
                        @foreach($platforms as $platformOption)
                            <option value="{{ $platformOption }}" {{ $platform === $platformOption ? 'selected' : '' }}>
                                {{ $platformOption }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        بحث
                    </button>

                    @if($search || $userFilter || $deviceType || $platform)
                        <a href="{{ route('admin.devices.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            مسح
                        </a>
                    @endif
                </form>
            </div>

            <a href="{{ route('admin.devices.create') }}"
               class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 @permClass('devices.create')" @permDisabled('devices.create')>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إضافة جهاز جديد
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $devices->total() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي الأجهزة</div>
                    </div>
                </div>
            </div>

            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                            {{ $devices->where('device_type', 'mobile')->count() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أجهزة محمولة</div>
                    </div>
                </div>
            </div>

            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                            {{ $devices->where('device_type', 'desktop')->count() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أجهزة مكتبية</div>
                    </div>
                </div>
            </div>

            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                            {{ $devices->whereNotNull('token_id')->count() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أجهزة نشطة</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Devices Table -->
        <div class="card-elegant rounded-xl overflow-hidden">
            @if($devices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    <input type="checkbox" id="select-all" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    الجهاز
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    المستخدم
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    المنصة
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    عنوان IP
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    الحالة
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    تاريخ التسجيل
                                </th>
                                <th class="text-center p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($devices as $device)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                                    <td class="p-4">
                                        <input type="checkbox" name="device_ids[]" value="{{ $device->id }}" class="device-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white">
                                                @switch($device->device_type)
                                                    @case('mobile')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                        @break
                                                    @case('tablet')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                        @break
                                                    @case('desktop')
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        @break
                                                    @default
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                        </svg>
                                                @endswitch
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $device->device_name }}</div>
                                                <div class="text-sm text-slate-600 dark:text-slate-400">{{ ucfirst($device->device_type) }}</div>
                                                @if($device->browser)
                                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $device->browser }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-xs">
                                                {{ substr($device->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $device->user->name }}</div>
                                                <div class="text-sm text-slate-600 dark:text-slate-400">{{ $device->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $device->platform }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-sm font-mono text-slate-600 dark:text-slate-300">
                                            {{ $device->ip_address ?: 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if($device->token)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                                نشط
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                                <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                                غير نشط
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="text-sm text-slate-600 dark:text-slate-300">
                                            {{ $device->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $device->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                             <a href="{{ route('admin.devices.show', $device) }}"
                                               class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors @permClass('devices.view')" @permDisabled('devices.view')
                                               title="عرض التفاصيل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                             <a href="{{ route('admin.devices.edit', $device) }}"
                                               class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors @permClass('devices.edit')" @permDisabled('devices.edit')
                                               title="تعديل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            @if($device->token)
                                                 <form method="POST" action="{{ route('admin.devices.revoke-token', $device) }}" class="inline @permClass('devices.revoke-token')">
                                                    @csrf
                                                    @method('PATCH')
                                                     <button type="submit" @permDisabled('devices.revoke-token')
                                                            class="p-2 rounded-lg text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors"
                                                            title="إلغاء الرمز المميز"
                                                            onclick="return confirm('هل تريد إلغاء الرمز المميز لهذا الجهاز؟')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                             <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="inline @permClass('devices.delete')">
                                                @csrf
                                                @method('DELETE')
                                                 <button type="submit" @permDisabled('devices.delete')
                                                        class="p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                        title="حذف"
                                                        onclick="return confirm('هل تريد حذف هذا الجهاز نهائياً؟\n\nسيتم حذف:\n- الجهاز وجميع بياناته\n- الرمز المميز المرتبط به\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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

                <!-- Bulk Actions -->
                <div class="border-t border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-slate-600 dark:text-slate-400">الإجراءات المجمعة:</span>
                            <form method="POST" action="{{ route('admin.devices.bulk-delete') }}" class="inline" id="bulk-delete-form">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="device_ids" id="selected-devices">
                                 <button type="submit" @permDisabled('devices.bulk-delete')
                                        class="px-4 py-2 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="bulk-delete-btn"
                                        disabled
                                        onclick="return confirm('هل تريد حذف الأجهزة المحددة؟\n\nسيتم حذف جميع الأجهزة المحددة والرموز المميزة المرتبطة بها.')">
                                    حذف المحدد
                                </button>
                            </form>
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            <span id="selected-count">0</span> عنصر محدد
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                    {{ $devices->links() }}
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا توجد أجهزة</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">
                                @if($search || $userFilter || $deviceType || $platform)
                                    لم يتم العثور على أجهزة تطابق معايير البحث المحددة
                                @else
                                    لم يتم تسجيل أي أجهزة بعد
                                @endif
                            </p>
                            <a href="{{ route('admin.devices.create') }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                إضافة جهاز جديد
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for bulk operations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const deviceCheckboxes = document.querySelectorAll('.device-checkbox');
            const selectedCountSpan = document.getElementById('selected-count');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedDevicesInput = document.getElementById('selected-devices');

            function updateBulkActions() {
                const selectedDevices = Array.from(deviceCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                selectedCountSpan.textContent = selectedDevices.length;
                bulkDeleteBtn.disabled = selectedDevices.length === 0;
                selectedDevicesInput.value = JSON.stringify(selectedDevices);
            }

            selectAllCheckbox.addEventListener('change', function() {
                deviceCheckboxes.forEach(cb => cb.checked = this.checked);
                updateBulkActions();
            });

            deviceCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(deviceCheckboxes).every(cb => cb.checked);
                    const anyChecked = Array.from(deviceCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = anyChecked && !allChecked;

                    updateBulkActions();
                });
            });

            document.getElementById('bulk-delete-form').addEventListener('submit', function(e) {
                const selectedDevices = JSON.parse(selectedDevicesInput.value);
                if (selectedDevices.length === 0) {
                    e.preventDefault();
                    return false;
                }

                // Update the form to send as array
                selectedDevicesInput.name = 'device_ids';
                selectedDevicesInput.value = selectedDevices.join(',');

                // Create individual inputs for each selected device
                selectedDevices.forEach(deviceId => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'device_ids[]';
                    input.value = deviceId;
                    this.appendChild(input);
                });
            });
        });
    </script>
@endsection
