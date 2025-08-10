@extends('admin.layouts.app')

@section('page-title', 'إدارة الشارات')
@section('page-subtitle', 'عرض وإدارة جميع شارات التحفيز في النظام')

@section('content')
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- Search -->
                <form method="GET" class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" name="q" value="{{ $search }}" 
                               placeholder="البحث في الشارات..." 
                               class="pl-10 pr-4 py-2.5 w-64 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Status Filter -->
                    <select name="status" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>نشطة</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>غير نشطة</option>
                    </select>
                    
                    <button type="submit" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        بحث
                    </button>
                    
                    @if($search || $status)
                        <a href="{{ route('admin.badges.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            مسح
                        </a>
                    @endif
                </form>
            </div>
            
            <a href="{{ route('admin.badges.create') }}" 
               class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إضافة شارة جديدة
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $badges->total() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">إجمالي الشارات</div>
                    </div>
                </div>
            </div>

            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $badges->where('is_active', true)->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">شارات نشطة</div>
                    </div>
                </div>
            </div>

            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $badges->where('is_active', false)->count() }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">شارات غير نشطة</div>
                    </div>
                </div>
            </div>

            <div class="card-elegant rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $badges->sum(fn($badge) => $badge->users->count()) }}</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">مجموع المنح</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Badges Grid -->
        <div class="card-elegant rounded-xl overflow-hidden">
            @if($badges->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                    @foreach($badges as $badge)
                        <div class="relative group">
                            <!-- Badge Card -->
                            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-all duration-300 hover:border-amber-300 dark:hover:border-amber-600">
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $badge->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $badge->is_active ? 'نشطة' : 'غير نشطة' }}
                                    </span>
                                </div>

                                <!-- Badge Icon -->
                                <div class="flex justify-center mb-4">
                                    @if($badge->icon)
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-2xl font-bold">
                                            {{ substr($badge->name, 0, 1) }}
                                        </div>
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Badge Info -->
                                <div class="text-center">
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">{{ $badge->name }}</h3>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-2">{{ $badge->description }}</p>
                                    
                                    <!-- Points -->
                                    <div class="flex items-center justify-center gap-2 mb-4">
                                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $badge->points_value }} نقطة</span>
                                    </div>

                                    <!-- Criteria -->
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mb-4">
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

                                    <!-- Users Count -->
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                                        منحت لـ {{ $badge->users->count() }} مستخدم
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.badges.show', $badge) }}" 
                                       class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" 
                                       title="عرض التفاصيل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.badges.edit', $badge) }}" 
                                       class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors" 
                                       title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('admin.badges.toggle-status', $badge) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="p-2 rounded-lg {{ $badge->is_active ? 'text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20' : 'text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20' }} transition-colors" 
                                                title="{{ $badge->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                            @if($badge->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.badges.destroy', $badge) }}" class="inline" 
                                          onsubmit="return confirm('هل تريد حذف هذه الشارة نهائياً؟\n\nتحذير: هذا الإجراء لا يمكن التراجع عنه!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" 
                                                title="حذف">
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
                    {{ $badges->links() }}
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا توجد شارات</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">
                                @if($search || $status)
                                    لم يتم العثور على شارات تطابق معايير البحث المحددة
                                @else
                                    لم يتم إنشاء أي شارات بعد
                                @endif
                            </p>
                            <a href="{{ route('admin.badges.create') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                إضافة شارة جديدة
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
