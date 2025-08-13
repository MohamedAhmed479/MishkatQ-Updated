@extends('admin.layouts.app')

@section('title', 'تفاصيل مراجعة SRS')

@section('content')
<div class="p-6">
	<div class="mb-6 flex items-center justify-between">
		<h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">مراجعة #{{ $spaced_repetition->id }}</h1>
		<div class="flex gap-2">
			<a href="{{ route('admin.spaced-repetitions.edit', $spaced_repetition) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg @permClass('spaced-repetitions.edit')" @permDisabled('spaced-repetitions.edit')>تعديل</a>
			<a href="{{ route('admin.review-records.index', ['spaced_repetition_id' => $spaced_repetition->id]) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">تقييمات هذه المراجعة</a>
		</div>
	</div>

	<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
			<p class="text-sm text-slate-500">تاريخ التنفيذ</p>
			<p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ optional($spaced_repetition->scheduled_date)->format('Y-m-d') }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
			<p class="text-sm text-slate-500">الحالة</p>
			<p class="text-2xl font-bold {{ $spaced_repetition->last_reviewed_at ? 'text-emerald-600' : 'text-amber-600' }}">{{ $spaced_repetition->last_reviewed_at ? 'مكتملة' : 'مجدولة' }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
			<p class="text-sm text-slate-500">الفترة (الترتيب)</p>
			<p class="text-2xl font-bold text-slate-900 dark:text-slate-100">#{{ $spaced_repetition->interval_index }}</p>
		</div>
	</div>

	<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div>
				<p class="text-slate-500">المستخدم</p>
				<p class="font-medium text-slate-900 dark:text-slate-100">{{ $spaced_repetition->planItem?->memorizationPlan?->user?->name }}</p>
			</div>
			<div>
				<p class="text-slate-500">الخطة / العنصر</p>
				<p class="font-medium text-slate-900 dark:text-slate-100">
					خطة 
					<a href="{{ route('admin.memorization-plans.show', $spaced_repetition->planItem?->plan_id) }}" class="text-emerald-600 hover:text-emerald-500 underline">#{{ $spaced_repetition->planItem?->plan_id }}</a>
					/
					عنصر 
					<a href="{{ route('admin.plan-items.show', $spaced_repetition->plan_item_id) }}" class="text-emerald-600 hover:text-emerald-500 underline">#{{ $spaced_repetition->plan_item_id }}</a>
				</p>
			</div>
			<div>
				<p class="text-slate-500">السورة</p>
				<p class="font-medium text-slate-900 dark:text-slate-100">{{ $spaced_repetition->planItem?->quranSurah?->name_ar }}</p>
			</div>
			<div>
				<p class="text-slate-500">معامل السهولة</p>
				<p class="font-medium text-slate-900 dark:text-slate-100">{{ number_format($spaced_repetition->ease_factor ?? 0, 2) }}</p>
			</div>
			<div>
				<p class="text-slate-500">عدد مرات المراجعة</p>
				<p class="font-medium text-slate-900 dark:text-slate-100">{{ $spaced_repetition->repetition_count }}</p>
			</div>
			<div>
				<p class="text-slate-500">آخر مراجعة</p>
				<p class="font-medium text-slate-900 dark:text-slate-100">{{ optional($spaced_repetition->last_reviewed_at)->format('Y-m-d H:i') ?: '-' }}</p>
			</div>
		</div>
	</div>

	<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
		<h2 class="text-lg font-bold mb-4">تقييم المستخدم</h2>
		@if($spaced_repetition->reviewRecord)
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<div>
					<p class="text-slate-500">التقييم</p>
					<p class="font-medium text-slate-900 dark:text-slate-100">{{ $spaced_repetition->reviewRecord->performance_rating }} ({{ $spaced_repetition->reviewRecord->getPerformanceDescription() }})</p>
				</div>
				<div>
					<p class="text-slate-500">النجاح</p>
					<p class="font-medium text-slate-900 dark:text-slate-100">{{ $spaced_repetition->reviewRecord->successful ? 'ناجحة' : 'غير ناجحة' }}</p>
				</div>
				<div>
					<p class="text-slate-500">تاريخ التقييم</p>
					<p class="font-medium text-slate-900 dark:text-slate-100">{{ optional($spaced_repetition->reviewRecord->review_date)->format('Y-m-d H:i') }}</p>
				</div>
			</div>
			@if($spaced_repetition->reviewRecord->notes)
				<p class="mt-4 text-slate-300">ملاحظات: <span class="text-slate-100">{{ $spaced_repetition->reviewRecord->notes }}</span></p>
			@endif
			<div class="mt-4">
				<a href="{{ route('admin.review-records.edit', $spaced_repetition->reviewRecord) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg @permClass('review-records.edit')" @permDisabled('review-records.edit')>تعديل التقييم</a>
			</div>
		@else
			<p class="text-slate-400">لا يوجد تقييم بعد.</p>
			<a href="{{ route('admin.review-records.create', ['spaced_repetition_id' => $spaced_repetition->id]) }}" class="mt-3 inline-block px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg @permClass('review-records.create')" @permDisabled('review-records.create')>إضافة تقييم</a>
		@endif
	</div>
</div>
@endsection


