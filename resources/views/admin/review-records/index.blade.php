@extends('admin.layouts.app')

@section('title', 'تقييمات المراجعة')

@section('content')
<div class="p-6">
	<div class="flex items-center justify-between mb-6">
		<div>
			<h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تقييمات المراجعة</h1>
			<p class="text-slate-600 dark:text-slate-400">إدارة تقييم أداء المستخدم للمراجعات</p>
		</div>
		<a href="{{ route('admin.review-records.create', request()->only('spaced_repetition_id')) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 @permClass('review-records.create')" @permDisabled('review-records.create')>
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
			إضافة تقييم
		</a>
	</div>

	<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
			<p class="text-sm font-medium text-slate-600 dark:text-slate-400">إجمالي</p>
			<p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($total) }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
			<p class="text-sm font-medium text-slate-600 dark:text-slate-400">ناجحة</p>
			<p class="text-2xl font-bold text-emerald-600">{{ number_format($successCount) }}</p>
		</div>
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
			<p class="text-sm font-medium text-slate-600 dark:text-slate-400">اليوم</p>
			<p class="text-2xl font-bold text-blue-600">{{ number_format($today) }}</p>
		</div>
	</div>

	<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
		<form method="GET" action="{{ route('admin.review-records.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">المراجعة</label>
				<select name="spaced_repetition_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
					<option value="0">الكل</option>
					@foreach($revisions as $rev)
						<option value="{{ $rev->id }}" {{ $revisionId==$rev->id?'selected':'' }}>#{{ $rev->id }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">التقييم</label>
				<select name="performance_rating" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
					<option value="">الكل</option>
					@for($i=0;$i<=5;$i++)
						<option value="{{ $i }}" {{ (string)$rating===(string)$i?'selected':'' }}>{{ $i }}</option>
					@endfor
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">النجاح</label>
				<select name="successful" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
					<option value="">الكل</option>
					<option value="1" {{ $success==='1'?'selected':'' }}>ناجحة</option>
					<option value="0" {{ $success==='0'?'selected':'' }}>غير ناجحة</option>
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">تاريخ</label>
				<input type="date" name="review_date" value="{{ $date }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
			</div>
			<div class="md:col-span-4 flex items-center gap-3">
				<button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">تطبيق الفلاتر</button>
				<a href="{{ route('admin.review-records.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg">إعادة تعيين</a>
			</div>
		</form>
	</div>

	<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
		<div class="overflow-x-auto">
			<table class="w-full">
				<thead class="bg-slate-50 dark:bg-slate-700/50">
					<tr>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">المراجعة</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">التاريخ</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">التقييم</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">النجاح</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">إجراءات</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-200 dark:divide-slate-700">
					@forelse($records as $record)
					<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
						<td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">
							<a href="{{ route('admin.spaced-repetitions.show', $record->spaced_repetition_id) }}" class="text-emerald-600 hover:text-emerald-500 underline">#{{ $record->spaced_repetition_id }}</a>
						</td>
						<td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ optional($record->review_date)->format('Y-m-d H:i') }}</td>
						<td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $record->performance_rating }} ({{ $record->getPerformanceDescription() }})</td>
						<td class="px-6 py-4 text-sm">
							<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $record->successful ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' }}">
								{{ $record->successful ? 'ناجحة' : 'غير ناجحة' }}
							</span>
						</td>
						<td class="px-6 py-4">
							<div class="flex items-center gap-2">
								<a href="{{ route('admin.review-records.show', $record) }}" class="p-2 text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg" title="عرض">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
								</a>
								<a href="{{ route('admin.review-records.edit', $record) }}" class="p-2 text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg @permClass('review-records.edit')" @permDisabled('review-records.edit') title="تعديل">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
								</a>
								<form action="{{ route('admin.review-records.destroy', $record) }}" method="POST" class="inline @permClass('review-records.delete')" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
									@csrf
									@method('DELETE')
									<button type="submit" class="p-2 text-slate-600 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" @permDisabled('review-records.delete') title="حذف">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
									</button>
								</form>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="5" class="px-6 py-12 text-center">
							<div class="text-slate-500 dark:text-slate-400">
								<svg class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
								<p class="text-lg font-medium">لا توجد تقييمات</p>
								<p class="text-sm">لم يتم العثور على أي سجلات تطابق معايير البحث</p>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		@if($records->hasPages())
			<div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
				{{ $records->links() }}
			</div>
		@endif
	</div>
</div>
@endsection


