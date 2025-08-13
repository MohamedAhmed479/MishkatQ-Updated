@extends('admin.layouts.app')

@section('title', 'إضافة عنصر خطة')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">إضافة عنصر خطة</h1>
            <p class="text-slate-600 dark:text-slate-400">إنشاء عنصر يومي جديد ضمن خطة</p>
        </div>
    </div>
	<form method="POST" action="{{ route('admin.plan-items.store') }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
		@csrf
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الخطة</label>
                <select name="plan_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
					@foreach($plans as $p)
						<option value="{{ $p->id }}" {{ $defaultPlanId==$p->id?'selected':'' }}>{{ $p->name }} - {{ $p->user?->name }}</option>
					@endforeach
				</select>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">السورة</label>
                <select name="quran_surah_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
					@foreach($surahs as $s)
						<option value="{{ $s->id }}">{{ $s->id }} - {{ $s->name_ar }}</option>
					@endforeach
				</select>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الآية البداية</label>
                <input type="number" name="verse_start_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الآية النهاية</label>
                <input type="number" name="verse_end_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">تاريخ التنفيذ</label>
                <input type="date" name="target_date" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
			</div>
			<div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الترتيب</label>
                <input type="number" name="sequence" min="1" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required>
			</div>
			<div class="flex items-center gap-2">
				<input id="is_completed" type="checkbox" name="is_completed" value="1" class="rounded">
				<label for="is_completed" class="text-sm">مكتمل</label>
			</div>
		</div>
		<div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">حفظ</button>
            <a href="{{ route('admin.plan-items.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors">إلغاء</a>
		</div>
	</form>
</div>
@endsection


