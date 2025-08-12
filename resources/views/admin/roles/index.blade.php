@extends('admin.layouts.app')

@section('page-title', 'الأدوار والصلاحيات')

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class="card-elegant rounded-xl p-6">
        <h2 class="text-lg font-bold text-slate-100 mb-4">الأدوار</h2>
        @can('roles.create')
        <form method="POST" action="{{ route('admin.roles.store') }}" class="mb-4 space-y-3">
            @csrf
            <input type="text" name="name" placeholder="اسم الدور" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" required />
            <div class="grid grid-cols-2 gap-2 max-h-40 overflow-auto">
                @foreach($permissions as $permission)
                    <label class="inline-flex items-center gap-2 text-slate-300">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-slate-600 bg-slate-800"> {{ $permission->name }}
                    </label>
                @endforeach
            </div>
            <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">إضافة دور</button>
        </form>
        @endcan

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-slate-300">
                <thead>
                    <tr class="text-left text-slate-400">
                        <th class="py-2">الاسم</th>
                        <th class="py-2">الصلاحيات</th>
                        <th class="py-2">خيارات</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr class="border-t border-slate-700/50">
                        <td class="py-2">{{ $role->name }}</td>
                        <td class="py-2">
                            @foreach($role->permissions as $perm)
                                <span class="inline-block px-2 py-1 text-xs rounded bg-slate-700/50">{{ $perm->name }}</span>
                            @endforeach
                        </td>
                        <td class="py-2">
                            @can('roles.edit')
                            <details>
                                <summary class="cursor-pointer text-emerald-400">تعديل</summary>
                                <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="mt-2 space-y-3">
                                    @csrf
                                    @method('PUT')

                                    <div class="space-y-2">
                                        <label class="block text-slate-300 text-sm">اسم الدور</label>
                                        <input type="text" name="name" value="{{ $role->name }}" class="w-full rounded-lg bg-slate-800 border border-slate-700 text-slate-100 px-3 py-2" />
                                    </div>

                                    @php
                                        $groupedPermissions = collect($permissions)->groupBy(function($p){
                                            return \Illuminate\Support\Str::before($p->name, '.');
                                        });
                                    @endphp

                                    <div class="rounded-lg border border-slate-700/60 p-3 bg-slate-800/40">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="text-slate-300 text-sm">الصلاحيات</span>
                                                <span class="text-xs text-slate-400">المحدد: <span id="selected-count-{{ $role->id }}">0</span></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input type="text" placeholder="ابحث عن صلاحية..." class="rounded-lg bg-slate-900/60 border border-slate-700 text-slate-200 px-3 py-1.5 w-56" oninput="filterPerms('role-{{ $role->id }}', this.value)" />
                                                <button type="button" class="px-2 py-1 rounded bg-slate-700 text-white text-xs" onclick="toggleAll('role-{{ $role->id }}', true)">تحديد الكل</button>
                                                <button type="button" class="px-2 py-1 rounded bg-slate-700 text-white text-xs" onclick="toggleAll('role-{{ $role->id }}', false)">إلغاء الكل</button>
                                            </div>
                                        </div>

                                        <div id="perm-container-{{ $role->id }}" data-role="role-{{ $role->id }}" class="mt-3 space-y-3 max-h-64 overflow-auto pr-1">
                                            @foreach($groupedPermissions as $groupName => $groupPerms)
                                                <div class="rounded border border-slate-700/60">
                                                    <div class="flex items-center justify-between px-3 py-2 bg-slate-900/50">
                                                        <div class="text-slate-300 text-sm">
                                                            {{ $groupName ?: 'عام' }}
                                                        </div>
                                                        <label class="inline-flex items-center gap-2 text-xs text-slate-400">
                                                            <input type="checkbox" class="rounded border-slate-600 bg-slate-800" onclick="toggleGroup('role-{{ $role->id }}','{{ $groupName }}', this.checked)">
                                                            تحديد المجموعة
                                                        </label>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 px-3 py-2" data-group="{{ $groupName }}">
                                                        @foreach($groupPerms as $permission)
                                                            <label class="inline-flex items-center gap-2 text-slate-300 perm-item" data-name="{{ strtolower($permission->name) }}">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-slate-600 bg-slate-800" {{ $role->permissions->pluck('id')->contains($permission->id) ? 'checked' : '' }} onchange="updateSelectedCount('{{ $role->id }}')">
                                                                {{ $permission->name }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <button class="px-3 py-1.5 rounded bg-emerald-600 text-white">حفظ التغييرات</button>
                                </form>
                            </details>
                            @endcan
                            @can('roles.delete')
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('تأكيد الحذف؟');" class="inline-block mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 rounded bg-red-700 text-white">حذف</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $roles->links() }}</div>
    </div>

    
</div>
@endsection


@push('scripts')
<script>
    function updateSelectedCount(roleId){
        const container = document.getElementById('perm-container-' + roleId);
        if(!container) return;
        const count = container.querySelectorAll('input[type="checkbox"][name="permissions[]"]:checked').length;
        const target = document.getElementById('selected-count-' + roleId);
        if(target) target.textContent = count;
    }

    function toggleAll(roleKey, checked){
        const container = document.querySelector('[data-role="' + roleKey + '"]');
        if(!container) return;
        container.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(cb => { cb.checked = checked; });
        const roleId = roleKey.replace('role-','');
        updateSelectedCount(roleId);
    }

    function toggleGroup(roleKey, groupName, checked){
        const container = document.querySelector('[data-role="' + roleKey + '"]');
        if(!container) return;
        container.querySelectorAll('[data-group="' + groupName + '"] input[type="checkbox"][name="permissions[]"]').forEach(cb => { cb.checked = checked; });
        const roleId = roleKey.replace('role-','');
        updateSelectedCount(roleId);
    }

    function filterPerms(roleKey, query){
        const container = document.querySelector('[data-role="' + roleKey + '"]');
        if(!container) return;
        const q = (query || '').toLowerCase().trim();
        container.querySelectorAll('.perm-item').forEach(el => {
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            el.style.display = name.includes(q) ? '' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('[id^="perm-container-"]').forEach(el => {
            const roleId = el.id.replace('perm-container-','');
            updateSelectedCount(roleId);
        });
    });
</script>
@endpush
