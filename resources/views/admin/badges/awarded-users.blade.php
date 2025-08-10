@extends('admin.layouts.app')

@section('page-title', 'المستخدمون الحاصلون على الشارة')
@section('page-subtitle', 'قائمة المستخدمين الذين حصلوا على شارة: ' . $badge->name)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.badges.show', $badge) }}" 
               class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex items-center gap-4">
                <!-- Badge Icon -->
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold">
                    {{ substr($badge->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">المستخدمون الحاصلون على الشارة</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $badge->name }} • {{ $users->total() }} مستخدم</p>
                </div>
            </div>
        </div>

        <!-- Badge Info Card -->
        <div class="card-elegant rounded-xl p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                            {{ $badge->points_value }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">نقطة لكل مستخدم</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ $users->total() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">مستخدم حصل عليها</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ $badge->points_value * $users->total() }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي النقاط الممنوحة</div>
                    </div>
                </div>
                <div class="text-sm text-slate-600 dark:text-slate-400">
                    <strong>المعيار:</strong> 
                    @php
                        $criteria = $badge->winning_criteria;
                        $typeLabels = [
                            'verses_memorized' => 'آية محفوظة',
                            'consecutive_days' => 'يوم متتالي',
                            'total_points' => 'نقطة',
                            'perfect_reviews' => 'مراجعة مثالية'
                        ];
                    @endphp
                    {{ $criteria['threshold'] ?? 0 }} {{ $typeLabels[$criteria['type']] ?? '' }}
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="card-elegant rounded-xl overflow-hidden">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    المستخدم
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    البريد الإلكتروني
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    تاريخ الحصول على الشارة
                                </th>
                                <th class="text-right p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    النقاط المكتسبة
                                </th>
                                <th class="text-center p-4 text-sm font-medium text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $user->name }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">مستخدم #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-sm font-mono text-slate-600 dark:text-slate-300">{{ $user->email }}</span>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-sm text-slate-600 dark:text-slate-300">
                                            {{ \Carbon\Carbon::parse($user->pivot->awarded_at)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ \Carbon\Carbon::parse($user->pivot->awarded_at)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <span class="font-medium">{{ $badge->points_value }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" 
                                               title="عرض تفاصيل المستخدم">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                    {{ $users->links() }}
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا يوجد مستخدمون</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">
                                لم يحصل أي مستخدم على هذه الشارة بعد
                            </p>
                            <a href="{{ route('admin.badges.show', $badge) }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                العودة لتفاصيل الشارة
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Summary Card -->
        @if($users->count() > 0)
            <div class="card-elegant rounded-xl p-6 bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">ملخص الإحصائيات</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $users->total() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">مستخدم حصل على الشارة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $badge->points_value * $users->total() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي النقاط الممنوحة</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $users->count() > 0 ? number_format(($badge->points_value * $users->total()) / $users->total(), 0) : 0 }}
                        </div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">متوسط النقاط لكل مستخدم</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
