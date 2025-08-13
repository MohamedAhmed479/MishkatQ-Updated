@extends('admin.layouts.app')

@section('title', 'تعديل خطة حفظ')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">تعديل خطة حفظ</h1>
            <p class="text-slate-600 dark:text-slate-400">تحديث بيانات خطة الحفظ</p>
        </div>
    </div>
	<form method="POST" action="{{ route('admin.memorization-plans.update', $memorization_plan) }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
		@csrf
		@method('PUT')
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">المستخدم</label>
                <select name="user_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
					@foreach($users as $user)
						<option value="{{ $user->id }}" {{ $memorization_plan->user_id==$user->id?'selected':'' }}>{{ $user->name }}</option>
					@endforeach
				</select>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">اسم الخطة</label>
                <input type="text" name="name" value="{{ $memorization_plan->name }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
			</div>
			<div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">{{ $memorization_plan->description }}</textarea>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">تاريخ البداية</label>
                <input type="date" name="start_date" value="{{ $memorization_plan->start_date?->format('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">تاريخ النهاية</label>
                <input type="date" name="end_date" value="{{ $memorization_plan->end_date?->format('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
					<option value="active" {{ $memorization_plan->status==='active'?'selected':'' }}>نشطة</option>
					<option value="paused" {{ $memorization_plan->status==='paused'?'selected':'' }}>متوقفة مؤقتًا</option>
					<option value="completed" {{ $memorization_plan->status==='completed'?'selected':'' }}>مكتملة</option>
				</select>
			</div>
		</div>
        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">حفظ</button>
            <a href="{{ route('admin.memorization-plans.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors">إلغاء</a>
        </div>
	</form>
</div>
@endsection


