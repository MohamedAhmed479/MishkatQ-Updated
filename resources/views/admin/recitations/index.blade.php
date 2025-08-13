@extends('admin.layouts.app')

@section('page-title', 'إدارة التسجيلات الصوتية')
@section('page-subtitle', 'عرض، إضافة، وتعديل روابط التلاوات')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إدارة التسجيلات</h1>
            <p class="text-slate-600 dark:text-slate-400">التسجيلات حسب القارئ والآية</p>
        </div>
        <a href="{{ route('admin.recitations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors @permClass('recitations.create')" @permDisabled('recitations.create')>إضافة تسجيل</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <form method="GET" action="{{ route('admin.recitations.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">بحث في الرابط</label>
                <input type="text" name="q" value="{{ $search }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" placeholder="https://...">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">القارئ</label>
                <select name="reciter_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="">الكل</option>
                    @foreach($reciters as $r)
                        <option value="{{ $r->id }}" {{ (string)$reciterId === (string)$r->id ? 'selected' : '' }}>{{ $r->reciter_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">السورة</label>
                <select name="chapter_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="">الكل</option>
                    @foreach($chapters as $c)
                        <option value="{{ $c->id }}" {{ (string)$chapterId === (string)$c->id ? 'selected' : '' }}>{{ $c->id }} — {{ $c->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">آية ID</label>
                <input type="number" name="verse_id" value="{{ $verseId }}" min="1" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">ترتيب حسب</label>
                <select name="sort_by" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="id" {{ $sortBy==='id' ? 'selected' : '' }}>ID</option>
                    <option value="reciter_id" {{ $sortBy==='reciter_id' ? 'selected' : '' }}>القارئ</option>
                    <option value="verse_id" {{ $sortBy==='verse_id' ? 'selected' : '' }}>الآية</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 text-slate-700 dark:text-slate-300">ترتيب</label>
                <select name="sort_order" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
                    <option value="asc" {{ $sortOrder==='asc' ? 'selected' : '' }}>تصاعدي</option>
                    <option value="desc" {{ $sortOrder==='desc' ? 'selected' : '' }}>تنازلي</option>
                </select>
            </div>
            <div class="lg:col-span-6 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">تطبيق</button>
                <a href="{{ route('admin.recitations.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <div class="card-elegant rounded-xl overflow-hidden">
        @if($recitations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">ID</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">القارئ</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الآية</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الرابط</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($recitations as $rec)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $rec->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-200">{{ $rec->reciter->reciter_name ?? '' }} (ID: {{ $rec->reciter_id }})</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                <div>{{ $rec->verse_id }} — {{ $rec->verse->chapter->name_ar ?? '' }} ({{ $rec->verse->verse_number ?? '' }})</div>
                                @if($rec->verse)
                                <div class="mt-1 text-slate-800 dark:text-slate-100 text-sm max-w-[40ch] truncate" dir="rtl">{{ \Illuminate\Support\Str::limit($rec->verse->text, 90) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 break-all">
                                @php
                                    $fullAudioUrl = preg_match('/^https?:\/\//i', $rec->audio_url)
                                        ? $rec->audio_url
                                        : 'https://verses.quran.foundation/' . ltrim($rec->audio_url, '/');
                                @endphp
                                <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-3 shadow-sm transition-colors group hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-600">
                                    <div class="js-audio-player" data-src="{{ $fullAudioUrl }}">
                                        <audio src="{{ $fullAudioUrl }}" preload="none"></audio>
                                        <div class="flex items-center gap-3">
                                            <button type="button" class="p-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-transform duration-200 group-hover:ring-2 group-hover:ring-emerald-300 group-hover:scale-105" data-role="toggle" title="تشغيل/إيقاف">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" data-icon="play"><path d="M6 4.5v11l9-5.5-9-5.5z"/></svg>
                                                <svg class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" data-icon="pause"><path d="M6 4h3v12H6zM11 4h3v12h-3z"/></svg>
                                            </button>
                                            <div class="flex-1">
                                                <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden cursor-pointer transition-all group-hover:h-3 group-hover:bg-emerald-50 dark:group-hover:bg-slate-600" data-role="bar">
                                                    <div class="h-full bg-emerald-600 transition-all" style="width:0%" data-role="fill"></div>
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400"><span data-role="current">0:00</span> / <span data-role="duration">0:00</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.recitations.show', $rec) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 @permClass('recitations.view')" @permDisabled('recitations.view') title="عرض">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.recitations.edit', $rec) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 @permClass('recitations.edit')" @permDisabled('recitations.edit') title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.recitations.destroy', $rec) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذا التسجيل؟')" class="inline @permClass('recitations.delete')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" @permDisabled('recitations.delete') class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">{{ $recitations->links() }}</div>
        @else
            <div class="p-16 text-center"><div class="text-slate-500 dark:text-slate-400">لا توجد تسجيلات</div></div>
        @endif
    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formatTime = (seconds) => {
        const s = Math.floor(seconds % 60).toString().padStart(2, '0');
        const m = Math.floor(seconds / 60).toString();
        return `${m}:${s}`;
    };

    document.querySelectorAll('.js-audio-player').forEach(function (wrap) {
        const audio = wrap.querySelector('audio');
        const toggle = wrap.querySelector('[data-role="toggle"]');
        const iconPlay = toggle.querySelector('[data-icon="play"]');
        const iconPause = toggle.querySelector('[data-icon="pause"]');
        const bar = wrap.querySelector('[data-role="bar"]');
        const fill = wrap.querySelector('[data-role="fill"]');
        const currentEl = wrap.querySelector('[data-role="current"]');
        const durationEl = wrap.querySelector('[data-role="duration"]');
        const container = wrap.closest('.group');

        let isSeeking = false;

        toggle.addEventListener('click', function () {
            if (audio.paused) {
                audio.play();
            } else {
                audio.pause();
            }
        });

        audio.addEventListener('play', function () {
            iconPlay.classList.add('hidden');
            iconPause.classList.remove('hidden');
        });

        audio.addEventListener('pause', function () {
            iconPause.classList.add('hidden');
            iconPlay.classList.remove('hidden');
        });

        audio.addEventListener('loadedmetadata', function () {
            durationEl.textContent = formatTime(audio.duration || 0);
        });

        audio.addEventListener('timeupdate', function () {
            if (audio.duration && !isSeeking) {
                const pct = (audio.currentTime / audio.duration) * 100;
                fill.style.width = pct + '%';
                currentEl.textContent = formatTime(audio.currentTime);
            }
        });

        // Visual feedback on hover: intensify progress color
        if (container) {
            container.addEventListener('mouseenter', function () {
                fill.classList.add('bg-emerald-500');
            });
            container.addEventListener('mouseleave', function () {
                fill.classList.remove('bg-emerald-500');
            });
        }

        const seek = (clientX) => {
            const rect = bar.getBoundingClientRect();
            const ratio = Math.min(Math.max(0, clientX - rect.left), rect.width) / rect.width;
            if (audio.duration) {
                audio.currentTime = ratio * audio.duration;
            }
        };

        bar.addEventListener('click', function (e) {
            seek(e.clientX);
        });

        bar.addEventListener('mousedown', function () { isSeeking = true; });
        window.addEventListener('mouseup', function () { isSeeking = false; });
        window.addEventListener('mousemove', function (e) { if (isSeeking) seek(e.clientX); });
    });
});
</script>
@endpush

