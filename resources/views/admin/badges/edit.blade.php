@extends('admin.layouts.app')

@section('page-title', 'تعديل الشارة')
@section('page-subtitle', 'تحديث بيانات الشارة: ' . $badge->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.badges.index') }}" 
               class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-lg">
                    {{ substr($badge->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">تعديل الشارة</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $badge->name }}</p>
                </div>
            </div>
        </div>

        <!-- Badge Status Card -->
        <div class="card-elegant rounded-xl p-4 mb-6 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-4">
                    <div class="text-slate-600 dark:text-slate-400">
                        <span class="font-medium">رقم الشارة:</span> #{{ $badge->id }}
                    </div>
                    <div class="text-slate-600 dark:text-slate-400">
                        <span class="font-medium">تاريخ الإنشاء:</span> {{ $badge->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $badge->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                    <span class="{{ $badge->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                        {{ $badge->is_active ? 'نشطة' : 'غير نشطة' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card-elegant rounded-xl p-6">
            <form method="POST" action="{{ route('admin.badges.update', $badge) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Badge Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            اسم الشارة
                        </span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $badge->name) }}" required 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" 
                           placeholder="أدخل اسم الشارة" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            وصف الشارة
                        </span>
                    </label>
                    <textarea name="description" rows="3" required 
                              class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" 
                              placeholder="أدخل وصف الشارة وشروط الحصول عليها">{{ old('description', $badge->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            رمز الشارة (اختياري)
                        </span>
                    </label>
                    <input type="text" name="icon" value="{{ old('icon', $badge->icon) }}" 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" 
                           placeholder="مثال: first-steps.svg" />
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">اسم ملف الأيقونة أو رابطها</p>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Points Value -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            قيمة النقاط
                        </span>
                    </label>
                    <input type="number" name="points_value" value="{{ old('points_value', $badge->points_value) }}" min="0" required 
                           class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" 
                           placeholder="100" />
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">عدد النقاط التي يحصل عليها المستخدم عند الحصول على هذه الشارة</p>
                    @error('points_value')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Winning Criteria -->
                <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        شروط الحصول على الشارة
                    </h3>

                    @php
                        $currentCriteria = $badge->winning_criteria;
                    @endphp

                    <!-- Criteria Type -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">نوع المعيار</label>
                        <select name="criteria_type" required 
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            <option value="">اختر نوع المعيار</option>
                            @foreach($criteriaTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('criteria_type', $currentCriteria['type'] ?? '') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('criteria_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Criteria Threshold -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الحد الأدنى المطلوب</label>
                        <input type="number" name="criteria_threshold" value="{{ old('criteria_threshold', $currentCriteria['threshold'] ?? 1) }}" min="1" required 
                               class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" 
                               placeholder="1" />
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">الرقم المطلوب تحقيقه للحصول على الشارة</p>
                        @error('criteria_threshold')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $badge->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-amber-600 bg-white border-slate-300 rounded focus:ring-amber-500 dark:bg-slate-800 dark:border-slate-600" />
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        تفعيل الشارة (يمكن للمستخدمين الحصول عليها)
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" 
                            class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.badges.index') }}" 
                       class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        <!-- Usage Statistics -->
        <div class="mt-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <div class="text-sm">
                    <div class="text-green-800 dark:text-green-300 font-medium mb-1">إحصائيات الاستخدام:</div>
                    <p class="text-green-700 dark:text-green-400">
                        تم منح هذه الشارة لـ <strong>{{ $badge->users()->count() }}</strong> مستخدم حتى الآن.
                        @if($badge->users()->count() > 0)
                            يرجى الحذر عند تعديل المعايير حيث قد يؤثر ذلك على المستخدمين الذين حصلوا عليها بالفعل.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
