@extends('admin.layouts.app')

@section('title', 'تفاصيل عنصر الخطة')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">عنصر بتاريخ {{ $plan_item->target_date?->format('Y-m-d') }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.plan-items.edit', $plan_item) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg @permClass('plan-items.edit')" @permDisabled('plan-items.edit')>تعديل</a>
        </div>
    </div>

	<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <p class="text-slate-500">الخطة</p>
            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $plan_item->memorizationPlan?->name }} - {{ $plan_item->memorizationPlan?->user?->name }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <p class="text-slate-500">السورة</p>
            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $plan_item->quranSurah?->name_ar }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <p class="text-slate-500">الآيات</p>
            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $plan_item->verseStart?->verse_number }} - {{ $plan_item->verseEnd?->verse_number }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <p class="text-slate-500">الترتيب</p>
            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $plan_item->sequence }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <p class="text-slate-500">الحالة</p>
            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $plan_item->is_completed ? 'مكتمل' : 'غير مكتمل' }}</p>
		</div>
	</div>

	<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
		<h2 class="text-lg font-bold mb-4">وصف</h2>
		<p>{{ $plan_item->getDescription() }}</p>
	</div>
</div>
@endsection


