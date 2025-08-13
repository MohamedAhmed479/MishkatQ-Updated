@extends('admin.layouts.app')

@section('title', 'إضافة تقييم مراجعة')

@section('content')
<div class="p-6">
	<div class="flex items-center justify-between mb-6">
		<div>
			<h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إضافة تقييم مراجعة</h1>
			<p class="text-slate-600 dark:text-slate-400">تسجيل تقييم أداء المستخدم لمراجعة SRS</p>
		</div>
	</div>

	<form method="POST" action="{{ route('admin.review-records.store') }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
		@csrf
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">المراجعة</label>
				<select name="spaced_repetition_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
					@foreach($revisions as $rev)
						<option value="{{ $rev->id }}" {{ ($defaultRevisionId??0)==$rev->id?'selected':'' }}>#{{ $rev->id }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">تاريخ التقييم</label>
				<input type="datetime-local" name="review_date" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required />
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">التقييم (0-5)</label>
				<input type="number" name="performance_rating" min="0" max="5" value="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required />
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">النجاح</label>
				<select name="successful" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
					<option value="1">ناجحة</option>
					<option value="0">غير ناجحة</option>
				</select>
			</div>
			<div class="md:col-span-2">
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">ملاحظات</label>
				<textarea name="notes" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"></textarea>
			</div>
		</div>
		<div class="mt-6">
			<button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">حفظ</button>
			<a href="{{ route('admin.review-records.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors">إلغاء</a>
		</div>
	</form>
</div>
@endsection


