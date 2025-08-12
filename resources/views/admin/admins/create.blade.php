@extends('admin.layouts.app')

@section('page-title', 'إنشاء مشرف')

@section('content')
<div class="card-elegant rounded-xl p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-slate-300 mb-1">الاسم</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
        </div>
        <div>
            <label class="block text-sm text-slate-300 mb-1">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-300 mb-1">كلمة المرور</label>
                <input type="password" name="password" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
            </div>
        </div>
        <div>
            <label class="block text-sm text-slate-300 mb-2">الأدوار</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($roles as $roleId => $roleName)
                    <label class="inline-flex items-center gap-2 text-slate-300">
                        <input type="checkbox" name="roles[]" value="{{ $roleId }}" class="rounded border-slate-600 bg-slate-800"> {{ $roleName }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">حفظ</button>
            <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded-lg bg-slate-700 text-slate-200 hover:bg-slate-600">إلغاء</a>
        </div>
    </form>
</div>
@endsection


