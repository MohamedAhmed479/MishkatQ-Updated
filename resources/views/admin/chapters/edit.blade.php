@extends('admin.layouts.app')

@section('title', 'تعديل السورة')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تعديل السورة</h1>
            <p class="text-slate-600 dark:text-slate-400">تعديل بيانات السورة: {{ $chapter->name_ar }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.chapters.show', $chapter) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                عرض السورة
            </a>
            <a href="{{ route('admin.chapters.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للسور
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <form action="{{ route('admin.chapters.update', $chapter) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name Arabic -->
                    <div class="mb-6">
                        <label for="name_ar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            اسم السورة العربية <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name_ar" 
                               id="name_ar" 
                               value="{{ old('name_ar', $chapter->name_ar) }}"
                               placeholder="مثال: الفاتحة، البقرة، آل عمران..."
                               class="w-full px-4 py-2.5 rounded-lg border @error('name_ar') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               required>
                        @error('name_ar')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">اسم السورة باللغة العربية (مطلوب)</p>
                    </div>

                    <!-- Name English -->
                    <div class="mb-6">
                        <label for="name_en" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            اسم السورة الإنجليزية
                        </label>
                        <input type="text" 
                               name="name_en" 
                               id="name_en" 
                               value="{{ old('name_en', $chapter->name_en) }}"
                               placeholder="مثال: Al-Fatihah, Al-Baqarah, Ali 'Imran..."
                               class="w-full px-4 py-2.5 rounded-lg border @error('name_en') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        @error('name_en')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">اسم السورة باللغة الإنجليزية (اختياري)</p>
                    </div>

                    <!-- Revelation Place -->
                    <div class="mb-6">
                        <label for="revelation_place" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            مكان النزول <span class="text-red-500">*</span>
                        </label>
                        <select name="revelation_place" 
                                id="revelation_place"
                                class="w-full px-4 py-2.5 rounded-lg border @error('revelation_place') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                required>
                            <option value="">اختر مكان النزول</option>
                            @foreach($revelationPlaces as $value => $label)
                                <option value="{{ $value }}" {{ old('revelation_place', $chapter->revelation_place) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('revelation_place')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">مكان نزول السورة (مكة أو المدينة)</p>
                    </div>

                    <!-- Revelation Order -->
                    <div class="mb-6">
                        <label for="revelation_order" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            ترتيب النزول
                        </label>
                        <input type="number" 
                               name="revelation_order" 
                               id="revelation_order" 
                               value="{{ old('revelation_order', $chapter->revelation_order) }}"
                               min="1"
                               placeholder="مثال: 5، 87، 89..."
                               class="w-full px-4 py-2.5 rounded-lg border @error('revelation_order') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        @error('revelation_order')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">ترتيب نزول السورة (اختياري، يجب أن يكون فريداً)</p>
                    </div>

                    <!-- Verses Count -->
                    <div class="mb-6">
                        <label for="verses_count" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            عدد الآيات
                        </label>
                        <input type="number" 
                               name="verses_count" 
                               id="verses_count" 
                               value="{{ old('verses_count', $chapter->verses_count) }}"
                               min="1"
                               placeholder="مثال: 7، 286، 200..."
                               class="w-full px-4 py-2.5 rounded-lg border @error('verses_count') border-red-300 dark:border-red-600 @else border-slate-200 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        @error('verses_count')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">عدد آيات السورة (اختياري)</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center gap-4 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" 
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.chapters.show', $chapter) }}" 
                           class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Chapter Info -->
        <div class="mt-6 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl p-6">
            <h3 class="font-medium text-slate-900 dark:text-slate-100 mb-4">معلومات السورة الحالية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">رقم السورة:</p>
                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $chapter->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">مكان النزول:</p>
                    <p class="font-medium text-slate-900 dark:text-slate-100">
                        @if($chapter->revelation_place === 'makkah')
                            <span class="text-green-600 dark:text-green-400">مكية</span>
                        @else
                            <span class="text-purple-600 dark:text-purple-400">مدنية</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">عدد الآيات:</p>
                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $chapter->verses_count ?: 'غير محدد' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">ترتيب النزول:</p>
                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $chapter->revelation_order ?: 'غير محدد' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
