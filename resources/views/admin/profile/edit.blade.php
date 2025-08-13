@extends('admin.layouts.app')

@section('page-title', 'تعديل الملف الشخصي')

@section('content')
<div class="card-elegant rounded-xl p-6 max-w-2xl">
	<form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
		@csrf
		@method('PUT')
		<div>
			<label class="block text-sm text-slate-300 mb-1">الاسم</label>
			<input type="text" name="name" value="{{ old('name', $admin->name) }}" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
		</div>
		<div>
			<label class="block text-sm text-slate-300 mb-1">البريد الإلكتروني</label>
			<input type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
		</div>
		<div class="grid grid-cols-2 gap-4">
			<div>
				<label class="block text-sm text-slate-300 mb-1">كلمة المرور الجديدة</label>
				<input type="password" name="password" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" />
			</div>
			<div>
				<label class="block text-sm text-slate-300 mb-1">تأكيد كلمة المرور</label>
				<input type="password" name="password_confirmation" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" />
			</div>
		</div>
		<div class="flex items-center gap-3">
			<button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">حفظ</button>
			<a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg bg-slate-700 text-slate-200 hover:bg-slate-600">رجوع</a>
		</div>
	</form>
</div>
@endsection



