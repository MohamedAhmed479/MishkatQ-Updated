@extends('admin.layouts.app')

@section('page-title', 'تفاصيل الآية')
@section('page-subtitle', 'عرض نص الآية ومعلوماتها')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">تفاصيل الآية {{ $verse->verse_key }}</h1>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <a href="{{ route('admin.verses.edit', $verse) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors touch-manipulation">تعديل</a>
            <a href="{{ route('admin.verses.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors touch-manipulation">العودة</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">السورة</div>
            <div class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $verse->chapter->name_ar ?? '' }} ({{ $verse->chapter_id }})</div>
            <div class="mt-3 text-sm text-slate-600 dark:text-slate-400">رقم الآية: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->verse_number }}</span></div>
            <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">الصفحة: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->page_number }}</span> — الجزء: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->juz_number }}</span></div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-600 dark:text-slate-400">المفتاح</div>
            <div class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $verse->verse_key }}</div>
            <div class="mt-3 text-sm text-slate-600 dark:text-slate-400">سجدة: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $verse->sajda ? 'نعم' : 'لا' }}</span></div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700 sm:col-span-2">
            <div class="text-sm text-slate-600 dark:text-slate-400 mb-2">النص العثماني</div>
            <div class="text-lg sm:text-2xl leading-loose text-slate-900 dark:text-slate-100" dir="rtl">{{ $verse->text_uthmani }}</div>
        </div>

        @if($verse->text_imlaei)
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700 sm:col-span-2">
            <div class="text-sm text-slate-600 dark:text-slate-400 mb-2">النص الإملائي</div>
            <div class="text-base sm:text-xl leading-loose text-slate-900 dark:text-slate-100" dir="rtl">{{ $verse->text_imlaei }}</div>
        </div>
        @endif

		<div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200 dark:border-slate-700 sm:col-span-2">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
				<div class="text-sm text-slate-600 dark:text-slate-400">التلاوات المرتبطة بهذه الآية</div>
				<a href="{{ route('admin.recitations.index', ['verse_id' => $verse->id]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 touch-manipulation">عرض الكل</a>
			</div>
			@if($verse->recitations->count())
				<div class="space-y-4">
					@foreach($verse->recitations as $r)
					@php
						$fullAudioUrl = preg_match('/^https?:\/\//i', $r->audio_url)
							? $r->audio_url
							: 'https://verses.quran.foundation/' . ltrim($r->audio_url, '/');
					@endphp
					<div class="rounded-lg border border-slate-200 dark:border-slate-700 p-3">
						<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
							<div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $r->reciter->reciter_name ?? '—' }}</div>
							<div class="text-xs text-slate-500" dir="ltr">ID: {{ $r->id }}</div>
						</div>
						<div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-3 shadow-sm transition-colors group hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-600">
							<div class="js-audio-player" data-src="{{ $fullAudioUrl }}">
								<audio src="{{ $fullAudioUrl }}" preload="none"></audio>
								<div class="flex items-center gap-3">
									<button type="button" class="p-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-transform duration-200 group-hover:ring-2 group-hover:ring-emerald-300 group-hover:scale-105 touch-manipulation" data-role="toggle" title="تشغيل/إيقاف">
										<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" data-icon="play"><path d="M6 4.5v11l9-5.5-9-5.5z"/></svg>
										<svg class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" data-icon="pause"><path d="M6 4h3v12H6zM11 4h3v12h-3z"/></svg>
									</button>
									<div class="flex-1">
										<div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden cursor-pointer transition-all group-hover:h-3 group-hover:bg-emerald-50 dark:group-hover:bg-slate-600 touch-manipulation" data-role="bar">
											<div class="h-full bg-emerald-600 transition-all" style="width:0%" data-role="fill"></div>
										</div>
										<div class="mt-1 text-xs text-slate-500 dark:text-slate-400"><span data-role="current">0:00</span> / <span data-role="duration">0:00</span></div>
									</div>
								</div>
							</div>
							<div class="mt-2 text-xs text-slate-500 break-all" dir="ltr">
								<a href="{{ $fullAudioUrl }}" target="_blank" rel="noopener" class="text-emerald-600 hover:text-emerald-700 touch-manipulation">{{ $fullAudioUrl }}</a>
							</div>
						</div>
					@endforeach
				</div>
			@else
				<div class="text-slate-500 dark:text-slate-400">لا توجد تلاوات لهذه الآية.</div>
			@endif
		</div>
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

		if (container) {
			container.addEventListener('mouseenter', function () {
				fill.classList.add('bg-emerald-500');
			});
			container.addEventListener('mouseleave', function () {
				fill.classList.remove('bg-emerald-500');
			});
		}
	});
});
</script>
@endpush

