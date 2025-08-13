@extends('admin.layouts.app')

@section('title', 'تفاصيل خطة الحفظ')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">خطة: {{ $memorization_plan->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.memorization-plans.edit', $memorization_plan) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg @permClass('memorization-plans.edit')" @permDisabled('memorization-plans.edit')>تعديل</a>
            <a href="{{ route('admin.plan-items.index', ['plan_id' => $memorization_plan->id]) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">عناصر الخطة</a>
        </div>
    </div>

	<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
            <p class="text-sm text-slate-500">إجمالي العناصر</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $itemsCount }}</p>
        </div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
			<p class="text-sm text-slate-500">المكتملة</p>
			<p class="text-2xl font-bold text-emerald-600">{{ $completedItems }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
			<p class="text-sm text-slate-500">القادمة</p>
			<p class="text-2xl font-bold text-blue-600">{{ $upcomingItems }}</p>
		</div>
	</div>

	<div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-slate-500">المستخدم</p>
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $memorization_plan->user?->name }}</p>
            </div>
            <div>
                <p class="text-slate-500">الحالة</p>
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $memorization_plan->status }}</p>
            </div>
            <div>
                <p class="text-slate-500">تاريخ البداية</p>
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ optional($memorization_plan->start_date)->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-slate-500">تاريخ النهاية</p>
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ optional($memorization_plan->end_date)->format('Y-m-d') }}</p>
            </div>
			<div class="md:col-span-2">
                <p class="text-slate-500">الوصف</p>
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $memorization_plan->description ?: '-' }}</p>
			</div>
		</div>
	</div>

	@if($memorization_plan->planItems->count())
		<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
			<h2 class="text-lg font-bold mb-4">أحدث العناصر</h2>
			<div class="overflow-x-auto">
				<table class="w-full">
					<thead>
						<tr>
							<th class="px-4 py-2 text-right">التاريخ</th>
							<th class="px-4 py-2 text-right">السورة</th>
							<th class="px-4 py-2 text-right">الآيات</th>
							<th class="px-4 py-2 text-right">الحالة</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-slate-200 dark:divide-slate-700">
						@foreach($memorization_plan->planItems->take(10) as $item)
						<tr>
							<td class="px-4 py-2">{{ $item->target_date?->format('Y-m-d') }}</td>
							<td class="px-4 py-2">{{ $item->quranSurah?->name_ar }}</td>
							<td class="px-4 py-2">{{ $item->verseStart?->verse_number }} - {{ $item->verseEnd?->verse_number }}</td>
							<td class="px-4 py-2">{{ $item->is_completed ? 'مكتمل' : 'غير مكتمل' }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	@endif
</div>
@endsection


