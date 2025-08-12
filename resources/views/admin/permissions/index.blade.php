@extends('admin.layouts.app')

@section('page-title', 'الصلاحيات')

@section('content')
<div class="card-elegant rounded-xl p-6">
    <h2 class="text-lg font-bold text-slate-100 mb-4">كل الصلاحيات</h2>
    @can('permissions.create')
    <form method="POST" action="{{ route('admin.permissions.store') }}" class="mb-4 flex items-center gap-3">
        @csrf
        <input type="text" name="name" placeholder="اسم الصلاحية" class="flex-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
        <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">إضافة</button>
    </form>
    @endcan

    <div class="space-y-2">
        @foreach($permissions as $permission)
            <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-slate-800 border border-slate-700">
                <span class="text-slate-200">{{ $permission->name }}</span>
                <div class="flex items-center gap-2">
                    @can('permissions.edit')
                    <form method="POST" action="{{ route('admin.permissions.update', $permission) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $permission->name }}" class="rounded-lg bg-slate-900 border border-slate-700 text-slate-100 px-2 py-1" />
                        <button class="px-3 py-1 rounded bg-emerald-600 text-white">تحديث</button>
                    </form>
                    @endcan
                    @can('permissions.delete')
                    <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" onsubmit="return confirm('تأكيد الحذف؟');">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 rounded bg-red-700 text-white">حذف</button>
                    </form>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection


