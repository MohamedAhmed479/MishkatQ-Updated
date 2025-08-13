@extends('admin.layouts.app')

@section('title', 'إضافة مراجعة SRS')

@section('content')
<div class="p-6">
	<div class="flex items-center justify-between mb-6">
		<div>
			<h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إضافة مراجعة SRS</h1>
			<p class="text-slate-600 dark:text-slate-400">إنشاء مراجعة متباعدة لعنصر خطة</p>
		</div>
	</div>

	<form method="POST" action="{{ route('admin.spaced-repetitions.store') }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
		@csrf
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">عنصر الخطة</label>
				<select name="plan_item_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
					@foreach($planItems as $pi)
						<option value="{{ $pi->id }}" {{ ($defaultPlanItemId??0)==$pi->id?'selected':'' }}>#{{ $pi->id }} - {{ $pi->memorizationPlan?->user?->name }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الفترة (الترتيب)</label>
				<input type="number" name="interval_index" min="1" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required />
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">تاريخ التنفيذ</label>
				<input type="date" name="scheduled_date" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required />
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">معامل السهولة</label>
				<input type="number" step="0.01" min="1" name="ease_factor" value="2.5" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">عدد مرات المراجعة</label>
				<input type="number" min="0" name="repetition_count" value="0" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
			</div>
		</div>
		<div class="mt-6">
			<button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">حفظ</button>
			<a href="{{ route('admin.spaced-repetitions.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors">إلغاء</a>
		</div>
	</form>
</div>
@endsection


