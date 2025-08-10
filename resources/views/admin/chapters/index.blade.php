@extends('admin.layouts.app')

@section('title', 'إدارة السور')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إدارة السور</h1>
            <p class="text-slate-600 dark:text-slate-400">إدارة سور القرآن الكريم</p>
        </div>
        <a href="{{ route('admin.chapters.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            إضافة سورة جديدة
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.523 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">إجمالي السور</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($totalChapters) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">السور المكية</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($makkahChapters) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">السور المدنية</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($madinahChapters) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">إجمالي الآيات</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($totalVerses) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <form method="GET" action="{{ route('admin.chapters.index') }}" class="flex flex-wrap items-end gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-48">
                <label for="q" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">البحث</label>
                <input type="text" 
                       name="q" 
                       id="q" 
                       value="{{ $search }}"
                       placeholder="اسم السورة العربية أو الإنجليزية..."
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>

            <!-- Revelation Place Filter -->
            <div class="min-w-32">
                <label for="revelation_place" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">مكان النزول</label>
                <select name="revelation_place" 
                        id="revelation_place"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">جميع الأماكن</option>
                    <option value="makkah" {{ $revelationPlace === 'makkah' ? 'selected' : '' }}>مكية</option>
                    <option value="madinah" {{ $revelationPlace === 'madinah' ? 'selected' : '' }}>مدنية</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="min-w-32">
                <label for="sort_by" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">ترتيب حسب</label>
                <select name="sort_by" 
                        id="sort_by"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="id" {{ $sortBy === 'id' ? 'selected' : '' }}>رقم السورة</option>
                    <option value="name_ar" {{ $sortBy === 'name_ar' ? 'selected' : '' }}>الاسم العربي</option>
                    <option value="verses_count" {{ $sortBy === 'verses_count' ? 'selected' : '' }}>عدد الآيات</option>
                    <option value="revelation_order" {{ $sortBy === 'revelation_order' ? 'selected' : '' }}>ترتيب النزول</option>
                </select>
            </div>

            <!-- Sort Order -->
            <div class="min-w-32">
                <label for="sort_order" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">ترتيب</label>
                <select name="sort_order" 
                        id="sort_order"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>تصاعدي</option>
                    <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>تنازلي</option>
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center gap-3">
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200">
                    تطبيق الفلاتر
                </button>
                <a href="{{ route('admin.chapters.index') }}" 
                   class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Chapters Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">#</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">اسم السورة</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الاسم الإنجليزي</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">مكان النزول</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">عدد الآيات</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">ترتيب النزول</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($chapters as $chapter)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                        <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                            {{ $chapter->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $chapter->id }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-slate-100">{{ $chapter->name_ar }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                            {{ $chapter->name_en ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($chapter->revelation_place === 'makkah')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    مكية
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    مدنية
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                            {{ number_format($chapter->verses_count ?: 0) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                            {{ $chapter->revelation_order ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.chapters.show', $chapter) }}" 
                                   class="p-2 text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors duration-200"
                                   title="عرض">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.chapters.edit', $chapter) }}" 
                                   class="p-2 text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200"
                                   title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه السورة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-slate-600 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200"
                                            title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-slate-500 dark:text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">لا توجد سور</p>
                                <p class="text-sm">لم يتم العثور على أي سور تطابق معايير البحث</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($chapters->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $chapters->links() }}
        </div>
        @endif
    </div>
</div>

<script>
// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    if (!form) {
        console.error('Form not found!');
        return;
    }
    
    // For now, let's just log to see if the form is found
    console.log('Form found:', form);
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    
    // Temporarily disable auto-submit to test manual submission
    /*
    const inputs = form.querySelectorAll('select, input[type="text"]');
    
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            // Small delay to ensure the value is updated
            setTimeout(() => {
                form.submit();
            }, 100);
        });
    });
    */
});
</script>
@endsection
