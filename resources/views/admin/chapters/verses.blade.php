@extends('admin.layouts.app')

@section('page-title', 'آيات سورة ' . $chapter->name_ar)
@section('page-subtitle', 'عرض وإدارة جميع آيات السورة')

@section('content')
    <!-- Header with Breadcrumb -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.chapters.show', $chapter) }}"
           class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">آيات سورة {{ $chapter->name_ar }}</h2>
            <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
                <span>إجمالي الآيات: {{ $verses->total() }}</span>
                <span>•</span>
                <span>عدد الصفحات: {{ $verses->lastPage() }}</span>
            </div>
        </div>
    </div>

    <!-- Chapter Info Card -->
    <div class="card-elegant rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                    {{ substr($chapter->name_ar, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ $chapter->name_ar }}</h3>
                    <p class="text-slate-600 dark:text-slate-400">{{ $chapter->name_en }}</p>
                    <div class="flex items-center gap-4 mt-2 text-sm">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            {{ $verses->total() }} آية
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ $chapter->juz->name_ar ?? 'غير محدد' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.chapters.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    العودة للسور
                </a>
            </div>
        </div>
    </div>

    <!-- Verses Table -->
    <div class="card-elegant rounded-xl overflow-hidden">
        @if($verses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800">
                        <tr>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">#</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">رقم الآية</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">نص الآية</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">عدد الكلمات</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">عدد الأحرف</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($verses as $verse)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-sm font-medium">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white text-sm font-bold shadow-lg">
                                        {{ $verse->verse_number }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="verse-text" dir="rtl" lang="ar">
                                        <div class="text-slate-800 dark:text-slate-200 leading-relaxed">
                                            {{ Str::limit($verse->text_uthmani, 120) }}
                                            @if(strlen($verse->text_uthmani) > 120)
                                                <button type="button" 
                                                        class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium text-sm mr-2"
                                                        onclick="showVerseModal({{ $verse->id }}, '{{ addslashes($verse->text_uthmani) }}', {{ $verse->verse_number }})">
                                                    عرض المزيد
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-medium">
                                        {{ $verse->words_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-sm font-medium">
                                        {{ $verse->characters_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                                onclick="showVerseModal({{ $verse->id }}, '{{ addslashes($verse->text_uthmani) }}', {{ $verse->verse_number }})"
                                                title="عرض الآية كاملة">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <a href="#" 
                                           class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors"
                                           title="تعديل الآية">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
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
                {{ $verses->links() }}
            </div>
        @else
            <div class="p-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا توجد آيات</h3>
                        <p class="text-slate-600 dark:text-slate-400">لم يتم العثور على آيات لهذه السورة</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Verse Modal -->
    <div id="verseModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="hideVerseModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100" id="modalTitle">آية من القرآن الكريم</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">سورة {{ $chapter->name_ar }}</p>
                        </div>
                    </div>
                    <button onclick="hideVerseModal()" 
                            class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div class="text-center">
                        <div class="mb-6">
                            <span class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white text-2xl font-bold shadow-lg" id="modalVerseNumber">
                                1
                            </span>
                        </div>
                        <div class="verse-full-text text-center" dir="rtl" lang="ar">
                            <div class="verse-content text-xl leading-loose text-slate-800 dark:text-slate-200" id="modalVerseText">
                                نص الآية
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-between p-6 border-t border-slate-200 dark:border-slate-700">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        <span class="font-medium">عدد الكلمات:</span> 
                        <span id="modalWordsCount">0</span>
                    </div>
                    <button onclick="hideVerseModal()" 
                            class="px-6 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .verse-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Cairo', serif;
            line-height: 1.8;
        }

        .verse-full-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Cairo', serif;
        }

        .verse-content {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Cairo', serif;
            line-height: 2.5;
            text-align: justify;
        }

        /* Custom scrollbar for modal */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dark .overflow-y-auto::-webkit-scrollbar-track {
            background: #334155;
        }

        .dark .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #64748b;
        }

        .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <!-- JavaScript for Modal -->
    <script>
        function showVerseModal(verseId, verseText, verseNumber) {
            const modal = document.getElementById('verseModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalVerseNumber = document.getElementById('modalVerseNumber');
            const modalVerseText = document.getElementById('modalVerseText');
            const modalWordsCount = document.getElementById('modalWordsCount');

            // Update modal content
            modalTitle.textContent = `آية ${verseNumber} - سورة {{ $chapter->name_ar }}`;
            modalVerseNumber.textContent = verseNumber;
            modalVerseText.textContent = verseText;
            
            // Calculate words count
            const wordsCount = verseText.trim().split(/\s+/).length;
            modalWordsCount.textContent = wordsCount;

            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideVerseModal() {
            const modal = document.getElementById('verseModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideVerseModal();
            }
        });
    </script>
@endsection
