@extends('admin.layouts.app')

@section('page-title', 'أجهزة المستخدم')
@section('page-subtitle', 'قائمة جميع أجهزة المستخدم: ' . $user->name)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.devices.index') }}" 
               class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex items-center gap-4">
                <!-- User Avatar -->
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">أجهزة المستخدم</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $user->name }} • {{ $user->email }} • {{ $devices->total() }} جهاز</p>
                </div>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="card-elegant rounded-xl p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $user->name }}</div>
                        <div class="text-slate-600 dark:text-slate-400">{{ $user->email }}</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            انضم في {{ $user->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $devices->total() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي الأجهزة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $devices->whereNotNull('token_id')->count() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أجهزة نشطة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-slate-600 dark:text-slate-400">
                            {{ $devices->whereNull('token_id')->count() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أجهزة غير نشطة</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Devices List -->
        <div class="card-elegant rounded-xl overflow-hidden">
            @if($devices->count() > 0)
                <div class="space-y-0 divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($devices as $device)
                        <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <!-- Device Info -->
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white">
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
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-1">
                                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $device->device_name }}</h3>
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
                                        </div>
                                        <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ ucfirst($device->device_type) }}
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                                </svg>
                                                {{ $device->platform }}
                                            </span>
                                            @if($device->browser)
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                                    </svg>
                                                    {{ $device->browser }}
                                                </span>
                                            @endif
                                            @if($device->ip_address)
                                                <span class="inline-flex items-center gap-1 font-mono">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                                    </svg>
                                                    {{ $device->ip_address }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mt-2">
                                            <span>تسجيل: {{ $device->created_at->format('d/m/Y H:i') }}</span>
                                            @if($device->token && $device->token->last_used_at)
                                                <span>آخر استخدام: {{ $device->token->last_used_at->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.devices.show', $device) }}" 
                                       class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" 
                                       title="عرض التفاصيل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.devices.edit', $device) }}" 
                                       class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors" 
                                       title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    @if($device->token)
                                        <form method="POST" action="{{ route('admin.devices.revoke-token', $device) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="p-2 rounded-lg text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors" 
                                                    title="إلغاء الرمز المميز"
                                                    onclick="return confirm('هل تريد إلغاء الرمز المميز لهذا الجهاز؟')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" 
                                                title="حذف"
                                                onclick="return confirm('هل تريد حذف هذا الجهاز نهائياً؟\n\nسيتم حذف:\n- الجهاز وجميع بياناته\n- الرمز المميز المرتبط به\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                                لم يقم هذا المستخدم بتسجيل أي أجهزة بعد
                            </p>
                            <div class="flex gap-3 justify-center">
                                <a href="{{ route('admin.devices.create') }}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    إضافة جهاز جديد
                                </a>
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    عرض ملف المستخدم
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Device Statistics Summary -->
        @if($devices->count() > 0)
            <div class="card-elegant rounded-xl p-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">ملخص الإحصائيات</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $devices->total() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي الأجهزة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $devices->whereNotNull('token_id')->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أجهزة نشطة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-slate-600 dark:text-slate-400">{{ $devices->whereNull('token_id')->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أجهزة غير نشطة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $devices->count() > 0 ? $devices->first()->created_at->format('d/m/Y') : '-' }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">أول جهاز مسجل</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
