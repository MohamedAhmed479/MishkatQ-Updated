@extends('admin.layouts.app')

@section('title', 'إضافة سورة جديدة')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">إضافة سورة جديدة</h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">إضافة سورة جديدة إلى القرآن الكريم</p>
        </div>
        <a href="{{ route('admin.chapters.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200 touch-manipulation">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            العودة للسور
        </a>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 sm:p-6">
                <form action="{{ route('admin.chapters.store') }}" method="POST">
                    @csrf

                    <!-- Name Arabic -->
                    <div class="mb-4 sm:mb-6">
                        <label for="name_ar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            اسم السورة العربية <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name_ar" 
                               id="name_ar" 
                               value="{{ old('name_ar') }}"
                               placeholder="مثال: الفاتحة، البقرة، آل عمران..."
                               class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border @error('name_ar') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
                               required>
                        @error('name_ar')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">اسم السورة باللغة العربية (مطلوب)</p>
                    </div>

                    <!-- Name English -->
                    <div class="mb-4 sm:mb-6">
                        <label for="name_en" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            اسم السورة الإنجليزية
                        </label>
                        <input type="text" 
                               name="name_en" 
                               id="name_en" 
                               value="{{ old('name_en') }}"
                               placeholder="مثال: Al-Fatihah, Al-Baqarah, Ali 'Imran..."
                               class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border @error('name_en') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base">
                        @error('name_en')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">اسم السورة باللغة الإنجليزية (اختياري)</p>
                    </div>

                    <!-- Revelation Place -->
                    <div class="mb-4 sm:mb-6">
                        <label for="revelation_place" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            مكان النزول <span class="text-red-500">*</span>
                        </label>
                        <select name="revelation_place" 
                                id="revelation_place"
                                class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border @error('revelation_place') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base"
                                required>
                            <option value="">اختر مكان النزول</option>
                            @foreach($revelationPlaces as $value => $label)
                                <option value="{{ $value }}" {{ old('revelation_place') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('revelation_place')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">مكان نزول السورة (مكة أو المدينة)</p>
                    </div>

                    <!-- Revelation Order -->
                    <div class="mb-4 sm:mb-6">
                        <label for="revelation_order" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            ترتيب النزول
                        </label>
                        <input type="number" 
                               name="revelation_order" 
                               id="revelation_order" 
                               value="{{ old('revelation_order') }}"
                               min="1"
                               placeholder="مثال: 5، 87، 89..."
                               class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border @error('revelation_order') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base">
                        @error('revelation_order')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">ترتيب نزول السورة (اختياري، يجب أن يكون فريداً)</p>
                    </div>

                    <!-- Verses Count -->
                    <div class="mb-4 sm:mb-6">
                        <label for="verses_count" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            عدد الآيات
                        </label>
                        <input type="number" 
                               name="verses_count" 
                               id="verses_count" 
                               value="{{ old('verses_count') }}"
                               min="1"
                               placeholder="مثال: 7، 286، 200..."
                               class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border @error('verses_count') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-base">
                        @error('verses_count')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">عدد آيات السورة (اختياري)</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 pt-4 sm:pt-6 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" @permDisabled('chapters.create')
                                class="flex-1 sm:flex-none px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 touch-manipulation @permClass('chapters.create')">
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            إنشاء السورة
                        </button>
                        <a href="{{ route('admin.chapters.index') }}" 
                           class="flex-1 sm:flex-none px-6 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200 touch-manipulation text-center">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Information -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">معلومات مهمة</h3>
                    <ul class="text-sm sm:text-base text-blue-800 dark:text-blue-200 space-y-1">
                        <li>• اسم السورة العربية مطلوب ولا يمكن تكراره</li>
                        <li>• ترتيب النزول يجب أن يكون فريداً لكل سورة</li>
                        <li>• عدد الآيات يساعد في تتبع تقدم الحفظ</li>
                        <li>• يمكن تعديل هذه المعلومات لاحقاً</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
