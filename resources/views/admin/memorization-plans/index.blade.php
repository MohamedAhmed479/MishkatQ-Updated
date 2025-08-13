@extends('admin.layouts.app')

@section('title', 'إدارة خطط الحفظ')

@section('content')
<div class="p-6">
	<div class="flex items-center justify-between mb-6">
		<div>
			<h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إدارة خطط الحفظ</h1>
			<p class="text-slate-600 dark:text-slate-400">عرض وإدارة خطط الحفظ للمستخدمين</p>
		</div>
		<a href="{{ route('admin.memorization-plans.create') }}" 
		   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 @permClass('memorization-plans.create')" @permDisabled('memorization-plans.create')>
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
			</svg>
			إضافة خطة جديدة
		</a>
	</div>

	<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
			<div class="flex items-center gap-4">
				<div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
					<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h10" />
					</svg>
				</div>
				<div>
					<p class="text-sm font-medium text-slate-600 dark:text-slate-400">إجمالي الخطط</p>
					<p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($totalPlans) }}</p>
				</div>
			</div>
		</div>

		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
			<div class="flex items-center gap-4">
				<div class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
					<svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
				</div>
				<div>
					<p class="text-sm font-medium text-slate-600 dark:text-slate-400">نشطة</p>
					<p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($activePlans) }}</p>
				</div>
			</div>
		</div>

		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
			<div class="flex items-center gap-4">
				<div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
					<svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
					</svg>
				</div>
				<div>
					<p class="text-sm font-medium text-slate-600 dark:text-slate-400">متوقفة مؤقتًا</p>
					<p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($pausedPlans) }}</p>
				</div>
			</div>
		</div>

		<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
			<div class="flex items-center gap-4">
				<div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
					<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
					</svg>
				</div>
				<div>
					<p class="text-sm font-medium text-slate-600 dark:text-slate-400">مكتملة</p>
					<p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($completedPlans) }}</p>
				</div>
			</div>
		</div>
	</div>

	<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
		<form method="GET" action="{{ route('admin.memorization-plans.index') }}" class="flex flex-wrap items-end gap-4">
			<div class="flex-1 min-w-48">
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">البحث</label>
				<input type="text" name="q" value="{{ $search }}" placeholder="الاسم أو المستخدم..." class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
			</div>
			<div class="min-w-32">
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الحالة</label>
				<select name="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
					<option value="">الكل</option>
					<option value="active" {{ $status==='active'?'selected':'' }}>نشطة</option>
					<option value="paused" {{ $status==='paused'?'selected':'' }}>متوقفة</option>
					<option value="completed" {{ $status==='completed'?'selected':'' }}>مكتملة</option>
				</select>
			</div>
			<div class="min-w-32">
				<label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">المستخدم</label>
				<select name="user_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
					<option value="0">الجميع</option>
					@foreach($users as $user)
						<option value="{{ $user->id }}" {{ $userId==$user->id?'selected':'' }}>{{ $user->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="flex items-center gap-3">
				<button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">تطبيق الفلاتر</button>
				<a href="{{ route('admin.memorization-plans.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200">إعادة تعيين</a>
			</div>
		</form>
	</div>

	<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
		<div class="overflow-x-auto">
			<table class="w-full">
				<thead class="bg-slate-50 dark:bg-slate-700/50">
					<tr>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">#</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الاسم</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">المستخدم</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">من</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">إلى</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الحالة</th>
						<th class="px-6 py-4 text-right text-sm font-medium text-slate-700 dark:text-slate-300">الإجراءات</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-200 dark:divide-slate-700">
					@forelse($plans as $plan)
					<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
						<td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">
							<a href="{{ route('admin.memorization-plans.show', $plan) }}" class="text-emerald-600 hover:text-emerald-500 underline">{{ $plan->id }}</a>
						</td>
						<td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">{{ $plan->name }}</td>
						<td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $plan->user?->name }}</td>
						<td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ optional($plan->start_date)->format('Y-m-d') }}</td>
						<td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ optional($plan->end_date)->format('Y-m-d') }}</td>
						<td class="px-6 py-4">
							@php($color = $plan->status==='active' ? 'green' : ($plan->status==='paused' ? 'amber' : 'blue'))
							<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $color }}-100 dark:bg-{{ $color }}-900/20 text-{{ $color }}-800 dark:text-{{ $color }}-400">{{ $plan->status }}</span>
						</td>
						<td class="px-6 py-4">
						<div class="flex items-center gap-2">
							<a href="{{ route('admin.memorization-plans.show', $plan) }}" 
							   class="p-2 text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors duration-200"
							   title="عرض">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
							</a>
							<a href="{{ route('admin.memorization-plans.edit', $plan) }}" 
							   class="p-2 text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200 @permClass('memorization-plans.edit')" @permDisabled('memorization-plans.edit')
							   title="تعديل">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
							</a>
							<form action="{{ route('admin.memorization-plans.destroy', $plan) }}" method="POST" class="inline @permClass('memorization-plans.delete')" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟')">
									@csrf
									@method('DELETE')
								<button type="submit" class="p-2 text-slate-600 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200" @permDisabled('memorization-plans.delete') title="حذف">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
								</button>
								</form>
							<a href="{{ route('admin.plan-items.index', ['plan_id' => $plan->id]) }}" 
							   class="p-2 text-slate-600 dark:text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors duration-200"
							   title="عناصر الخطة">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10m-7 6h7"/></svg>
							</a>
							</div>
						</td>
					</tr>
				@empty
				<tr>
					<td colspan="7" class="px-6 py-12 text-center">
						<div class="text-slate-500 dark:text-slate-400">
							<svg class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
							</svg>
							<p class="text-lg font-medium">لا توجد خطط</p>
							<p class="text-sm">لم يتم العثور على أي خطط تطابق معايير البحث</p>
						</div>
					</td>
				</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		@if($plans->hasPages())
			<div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
				{{ $plans->links() }}
			</div>
		@endif
	</div>
</div>
@endsection


