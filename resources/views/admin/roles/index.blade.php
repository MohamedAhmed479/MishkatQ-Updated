@extends('admin.layouts.app')

@section('page-title', 'الأدوار والصلاحيات')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-white">إدارة الأدوار والصلاحيات</h1>
                    <p class="mt-2 text-lg text-white/80">تحكم في أذونات المستخدمين والوصول للنظام</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-white/70">إجمالي الأدوار</div>
                <div class="text-3xl font-bold text-white">{{ $roles->total() ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="animate-fade-in rounded-xl border border-green-500/30 bg-green-500/10 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-500/20">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-green-400">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="animate-fade-in rounded-xl border border-red-500/30 bg-red-500/10 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500/20">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-red-400">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Add New Role Section -->
        @can('roles.create')
    <div class="rounded-2xl bg-gradient-to-r from-emerald-600/10 to-teal-600/10 border border-emerald-500/20 p-6 shadow-xl">
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/20">
                <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white">إضافة دور جديد</h3>
                <p class="text-sm text-slate-300">قم بإنشاء دور جديد وتحديد الصلاحيات المناسبة</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
            @csrf
            
            <!-- Role Name -->
            <div class="space-y-2">
                <label for="role_name" class="block text-sm font-medium text-white">
                    📝 اسم الدور
                </label>
                <input type="text" 
                       id="role_name"
                       name="name" 
                       placeholder="أدخل اسم الدور الجديد" 
                       class="w-full rounded-xl bg-slate-700 border border-slate-600 px-4 py-3 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-300" 
                       required />
            </div>

            <!-- Permissions Selection -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-white">
                        🔐 الصلاحيات
                    </label>
                    <div class="flex gap-2">
                        <button type="button" onclick="toggleAllNewRole(true)" class="rounded-lg bg-emerald-600 px-3 py-1 text-xs text-white hover:bg-emerald-700 transition-colors">
                            تحديد الكل
                        </button>
                        <button type="button" onclick="toggleAllNewRole(false)" class="rounded-lg bg-slate-600 px-3 py-1 text-xs text-white hover:bg-slate-700 transition-colors">
                            إلغاء الكل
                        </button>
            </div>
                                    </div>

                <div class="rounded-xl border border-slate-600 bg-slate-800/50 p-4">
                                    @php
                                        $groupedPermissions = collect($permissions)->groupBy(function($p){
                                            return \Illuminate\Support\Str::before($p->name, '.');
                                        });
                                    @endphp

                    <div class="max-h-64 space-y-4 overflow-auto pr-2">
                        @foreach($groupedPermissions as $groupName => $groupPerms)
                        <div class="rounded-lg border border-slate-700/60 bg-slate-900/30">
                            <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-slate-800/50 to-slate-700/50">
                                            <div class="flex items-center gap-3">
                                    <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
                                    <span class="font-medium text-slate-200">{{ $groupName ?: 'عام' }}</span>
                                    <span class="rounded-full bg-slate-700 px-2 py-1 text-xs text-slate-300">{{ count($groupPerms) }}</span>
                                                        </div>
                                                        <label class="inline-flex items-center gap-2 text-xs text-slate-400">
                                    <input type="checkbox" 
                                           class="rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500" 
                                           onclick="toggleGroupNewRole('{{ $groupName }}', this.checked)">
                                                            تحديد المجموعة
                                                        </label>
                                                    </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4" data-group="{{ $groupName }}">
                                                        @foreach($groupPerms as $permission)
                                <label class="inline-flex items-center gap-3 rounded-lg bg-slate-800/40 p-3 text-slate-300 hover:bg-slate-700/50 transition-colors cursor-pointer">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}" 
                                           class="rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500">
                                    <span class="text-sm">{{ $permission->name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                    </div>
                                        </div>
                                    </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-3 font-medium text-white shadow-lg hover:from-emerald-700 hover:to-emerald-800 transition-all duration-300 transform hover:scale-105">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    إضافة الدور
                </button>
            </div>
                                </form>
    </div>
    @endcan

    <!-- Roles List Section -->
    <div class="rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 shadow-xl overflow-hidden">
        <div class="border-b border-slate-700 bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/20">
                        <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">قائمة الأدوار</h3>
                        <p class="text-sm text-slate-400">إدارة وتعديل الأدوار الموجودة</p>
                    </div>
                </div>
                <div class="rounded-lg bg-slate-700/50 px-3 py-1 text-sm text-slate-300">
                    {{ $roles->count() }} من أصل {{ $roles->total() }}
                </div>
            </div>
        </div>

        @if($roles->count() > 0)
        <div class="divide-y divide-slate-700">
            @foreach($roles as $role)
            <div class="group p-6 hover:bg-slate-800/50 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <!-- Role Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-lg">
                                {{ strtoupper(substr($role->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-white">{{ $role->name }}</h4>
                                <p class="text-sm text-slate-400">{{ $role->permissions->count() }} صلاحية</p>
                            </div>
                        </div>

                        <!-- Permissions Display -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($role->permissions->take(6) as $perm)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-700/60 px-3 py-1 text-xs text-slate-300">
                                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-400"></div>
                                    {{ $perm->name }}
                                </span>
                                @endforeach
                                @if($role->permissions->count() > 6)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-600/60 px-3 py-1 text-xs text-slate-400">
                                    +{{ $role->permissions->count() - 6 }} المزيد
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        @can('roles.edit')
                        <button type="button" 
                                onclick="openEditModal('{{ $role->id }}')"
                                class="action-button inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-600 to-amber-700 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-amber-700 hover:to-amber-800 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            تعديل
                                                </button>
                            @endcan

                            @can('roles.delete')
                        <form method="POST" 
                              action="{{ route('admin.roles.destroy', $role) }}" 
                              onsubmit="return confirm('⚠️ هل أنت متأكد من حذف هذا الدور؟\n\nسيؤثر هذا على جميع المستخدمين المرتبطين بهذا الدور.');" 
                              class="inline-block">
                                @csrf
                                @method('DELETE')
                            <button type="submit" 
                                    class="action-button inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                حذف
                            </button>
                            </form>
                            @endcan
                    </div>
                </div>
            </div>
                @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-700/50 mb-4">
                <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-slate-300 mb-2">لا توجد أدوار</h3>
            <p class="text-slate-400 mb-6">لم يتم إنشاء أي أدوار بعد. ابدأ بإنشاء دور جديد.</p>
            @can('roles.create')
            <a href="#" 
               onclick="document.querySelector('#role_name').focus(); return false;"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 font-medium text-white hover:bg-emerald-700 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                إنشاء أول دور
            </a>
            @endcan
        </div>
        @endif

        @if($roles->hasPages())
        <div class="border-t border-slate-700 bg-slate-800/30 px-6 py-4">
            {{ $roles->links() }}
        </div>
        @endif
    </div>

    <!-- Edit Role Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="mx-4 w-full max-w-4xl transform rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 shadow-2xl transition-all duration-300 scale-95 opacity-0" id="editModalContent">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-700 bg-gradient-to-r from-amber-600/10 to-amber-700/10 px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/20">
                        <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">تعديل الدور</h3>
                        <p class="text-sm text-slate-400">تعديل اسم الدور والصلاحيات المرتبطة</p>
                    </div>
                </div>
                <button type="button" 
                        onclick="closeEditModal()"
                        class="rounded-xl p-2 text-slate-400 hover:bg-slate-700 hover:text-white transition-all duration-200 cursor-pointer">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editRoleForm" method="POST" action="" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Role Name Section -->
                    <div class="rounded-xl bg-slate-800/50 border border-slate-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-2 w-2 rounded-full bg-amber-400"></div>
                            <h4 class="text-lg font-semibold text-white">معلومات الدور</h4>
                        </div>
                        <div class="space-y-2">
                            <label for="editRoleName" class="block text-sm font-medium text-slate-300">
                                📝 اسم الدور
                            </label>
                            <input type="text" 
                                   id="editRoleName"
                                   name="name" 
                                   class="w-full rounded-xl bg-slate-700 border border-slate-600 px-4 py-3 text-white placeholder-slate-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all duration-300" 
                                   placeholder="أدخل اسم الدور"
                                   required />
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div class="rounded-xl bg-slate-800/50 border border-slate-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full bg-amber-400"></div>
                                <h4 class="text-lg font-semibold text-white">الصلاحيات</h4>
                                <span class="rounded-full bg-slate-700 px-3 py-1 text-xs text-slate-300">
                                    المحدد: <span id="editSelectedCount">0</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="text" 
                                       id="editPermissionSearch"
                                       placeholder="🔍 ابحث في الصلاحيات..." 
                                       class="w-64 rounded-lg bg-slate-700 border border-slate-600 px-3 py-2 text-sm text-slate-200 placeholder-slate-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20" />
                                <button type="button" 
                                        onclick="toggleAllEditPermissions(true)"
                                        class="action-button rounded-lg bg-emerald-600 px-3 py-2 text-xs font-medium text-white hover:bg-emerald-700 transition-colors cursor-pointer">
                                    تحديد الكل
                                </button>
                                <button type="button" 
                                        onclick="toggleAllEditPermissions(false)"
                                        class="action-button rounded-lg bg-slate-600 px-3 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors cursor-pointer">
                                    إلغاء الكل
                                </button>
                            </div>
                        </div>

                        <div id="editPermissionsContainer" class="max-h-96 space-y-4 overflow-auto rounded-lg bg-slate-900/50 p-4">
                            @foreach($groupedPermissions as $groupName => $groupPerms)
                            <div class="permission-group rounded-lg border border-slate-700/60 bg-slate-800/50">
                                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-slate-800/50 to-slate-700/50">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-amber-400"></div>
                                        <span class="font-medium text-slate-200">{{ $groupName ?: 'عام' }}</span>
                                        <span class="rounded-full bg-slate-700 px-2 py-1 text-xs text-slate-300">{{ count($groupPerms) }}</span>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-xs text-slate-400 cursor-pointer">
                                        <input type="checkbox" 
                                               class="group-checkbox rounded border-slate-600 bg-slate-800 text-amber-500 focus:ring-amber-500" 
                                               data-group="{{ $groupName }}"
                                               onchange="toggleEditGroup('{{ $groupName }}', this.checked)">
                                        تحديد المجموعة
                                    </label>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4" data-group="{{ $groupName }}">
                                    @foreach($groupPerms as $permission)
                                    <label class="permission-item inline-flex items-center gap-3 rounded-lg bg-slate-800/60 border border-transparent p-3 text-slate-300 hover:bg-slate-700/60 hover:border-amber-500/30 transition-all duration-200 cursor-pointer" 
                                           data-name="{{ strtolower($permission->name) }}">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->id }}" 
                                               class="edit-permission-checkbox rounded border-slate-600 bg-slate-800 text-amber-500 focus:ring-amber-500"
                                               onchange="updateEditSelectedCount()">
                                        <span class="text-sm">{{ $permission->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeEditModal()"
                            class="action-button inline-flex items-center gap-2 rounded-xl bg-slate-600 px-6 py-3 font-medium text-white hover:bg-slate-700 transition-all duration-300 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="action-button inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-3 font-medium text-white shadow-lg hover:from-amber-700 hover:to-amber-800 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    // دالة تحديث عدد الصلاحيات المحددة
    function updateSelectedCount(roleId){
        const container = document.getElementById('perm-container-' + roleId);
        if(!container) return;
        const count = container.querySelectorAll('input[type="checkbox"][name="permissions[]"]:checked').length;
        const target = document.getElementById('selected-count-' + roleId);
        if(target) target.textContent = count;
    }

    // دالة تحديد/إلغاء تحديد جميع الصلاحيات لدور معين
    function toggleAll(roleKey, checked){
        const container = document.querySelector('[data-role="' + roleKey + '"]');
        if(!container) return;
        container.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(cb => { 
            cb.checked = checked; 
            // إضافة تأثير بصري
            if(checked) {
                cb.closest('label').classList.add('animate-pulse');
                setTimeout(() => {
                    cb.closest('label').classList.remove('animate-pulse');
                }, 300);
            }
        });
        const roleId = roleKey.replace('role-','');
        updateSelectedCount(roleId);
    }

    // دالة تحديد/إلغاء تحديد مجموعة صلاحيات
    function toggleGroup(roleKey, groupName, checked){
        const container = document.querySelector('[data-role="' + roleKey + '"]');
        if(!container) return;
        container.querySelectorAll('[data-group="' + groupName + '"] input[type="checkbox"][name="permissions[]"]').forEach(cb => { 
            cb.checked = checked; 
            // إضافة تأثير بصري
            if(checked) {
                cb.closest('label').classList.add('animate-pulse');
                setTimeout(() => {
                    cb.closest('label').classList.remove('animate-pulse');
                }, 200);
            }
        });
        const roleId = roleKey.replace('role-','');
        updateSelectedCount(roleId);
    }

    // دالة البحث في الصلاحيات
    function filterPerms(roleKey, query){
        const container = document.querySelector('[data-role="' + roleKey + '"]');
        if(!container) return;
        const q = (query || '').toLowerCase().trim();
        
        container.querySelectorAll('.perm-item').forEach(el => {
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const isVisible = name.includes(q);
            el.style.display = isVisible ? '' : 'none';
            
            // إخفاء/إظهار المجموعات الفارغة
            const group = el.closest('[data-group]');
            if(group) {
                const visibleItems = Array.from(group.querySelectorAll('.perm-item')).filter(item => 
                    item.style.display !== 'none'
                );
                const groupContainer = group.closest('.rounded.border');
                if(groupContainer) {
                    groupContainer.style.display = visibleItems.length > 0 ? '' : 'none';
                }
            }
        });
    }

    // دوال خاصة بنموذج إضافة دور جديد
    function toggleAllNewRole(checked) {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
            if(!cb.closest('[data-role]')) { // فقط للنموذج الجديد
                cb.checked = checked;
                if(checked) {
                    cb.closest('label').classList.add('animate-pulse');
                    setTimeout(() => {
                        cb.closest('label').classList.remove('animate-pulse');
                    }, 300);
                }
            }
        });
    }

    function toggleGroupNewRole(groupName, checked) {
        document.querySelectorAll(`[data-group="${groupName}"] input[name="permissions[]"]`).forEach(cb => {
            if(!cb.closest('[data-role]')) { // فقط للنموذج الجديد
                cb.checked = checked;
                if(checked) {
                    cb.closest('label').classList.add('animate-pulse');
                    setTimeout(() => {
                        cb.closest('label').classList.remove('animate-pulse');
                    }, 200);
                }
            }
        });
    }

    // دوال خاصة بـ Modal التعديل
    let currentEditingRole = null;
    let rolesData = @json($roles->keyBy('id'));

    // فتح modal التعديل
    function openEditModal(roleId) {
        currentEditingRole = roleId;
        const role = rolesData[roleId];
        if (!role) return;

        // تعبئة بيانات الدور
        document.getElementById('editRoleName').value = role.name;
        document.getElementById('editRoleForm').action = `/admin/roles/${roleId}`;

        // إعادة تعيين جميع الصلاحيات
        document.querySelectorAll('.edit-permission-checkbox').forEach(cb => {
            cb.checked = role.permissions.some(p => p.id == cb.value);
        });

        // تحديث العداد
        updateEditSelectedCount();

        // إظهار المودال مع تأثيرات
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // تأثير الظهور
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);

        // منع التمرير
        document.body.style.overflow = 'hidden';
    }

    // إغلاق modal التعديل
    function closeEditModal() {
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        
        // تأثير الاختفاء
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);

        // إعادة تمكين التمرير
        document.body.style.overflow = '';
        
        // إعادة تعيين النموذج
        document.getElementById('editRoleForm').reset();
        document.getElementById('editPermissionSearch').value = '';
        filterEditPermissions('');
        currentEditingRole = null;
    }

    // تحديث عدد الصلاحيات المحددة في المودال
    function updateEditSelectedCount() {
        const count = document.querySelectorAll('.edit-permission-checkbox:checked').length;
        document.getElementById('editSelectedCount').textContent = count;
        
        // تحديث حالة checkboxes المجموعات
        document.querySelectorAll('.group-checkbox').forEach(groupCb => {
            const groupName = groupCb.dataset.group;
            const groupPermissions = document.querySelectorAll(`[data-group="${groupName}"] .edit-permission-checkbox`);
            const checkedInGroup = document.querySelectorAll(`[data-group="${groupName}"] .edit-permission-checkbox:checked`);
            
            if (checkedInGroup.length === 0) {
                groupCb.checked = false;
                groupCb.indeterminate = false;
            } else if (checkedInGroup.length === groupPermissions.length) {
                groupCb.checked = true;
                groupCb.indeterminate = false;
            } else {
                groupCb.checked = false;
                groupCb.indeterminate = true;
            }
        });
    }

    // تحديد/إلغاء تحديد جميع الصلاحيات في المودال
    function toggleAllEditPermissions(checked) {
        document.querySelectorAll('.edit-permission-checkbox').forEach(cb => {
            cb.checked = checked;
            
            // تأثير بصري
            if (checked) {
                cb.closest('.permission-item').classList.add('animate-pulse');
                setTimeout(() => {
                    cb.closest('.permission-item').classList.remove('animate-pulse');
                }, 300);
            }
        });
        updateEditSelectedCount();
    }

    // تحديد/إلغاء تحديد مجموعة في المودال
    function toggleEditGroup(groupName, checked) {
        document.querySelectorAll(`[data-group="${groupName}"] .edit-permission-checkbox`).forEach(cb => {
            cb.checked = checked;
            
            // تأثير بصري
            if (checked) {
                cb.closest('.permission-item').classList.add('animate-pulse');
                setTimeout(() => {
                    cb.closest('.permission-item').classList.remove('animate-pulse');
                }, 200);
            }
        });
        updateEditSelectedCount();
    }

    // البحث في الصلاحيات في المودال
    function filterEditPermissions(query) {
        const q = query.toLowerCase().trim();
        
        document.querySelectorAll('.permission-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const isVisible = name.includes(q);
            item.style.display = isVisible ? '' : 'none';
        });

        // إخفاء/إظهار المجموعات الفارغة
        document.querySelectorAll('.permission-group').forEach(group => {
            const visibleItems = Array.from(group.querySelectorAll('.permission-item')).filter(item => 
                item.style.display !== 'none'
            );
            group.style.display = visibleItems.length > 0 ? '' : 'none';
        });
    }

    // إضافة تأثيرات عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function(){
        // تحديث عدادات الصلاحيات المحددة
        document.querySelectorAll('[id^="perm-container-"]').forEach(el => {
            const roleId = el.id.replace('perm-container-','');
            updateSelectedCount(roleId);
        });

        // إضافة تأثيرات CSS للانيميشن
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }
            
            .group:hover .group-hover\\:scale-105 {
                transform: scale(1.05);
            }
            
            details[open] summary {
                border-radius: 0.75rem 0.75rem 0 0;
            }
            
            details summary::-webkit-details-marker {
                display: none;
            }
            
            details summary {
                list-style: none;
                position: relative;
            }
            
            details summary::after {
                content: '';
                position: absolute;
                right: 8px;
                top: 50%;
                transform: translateY(-50%);
                width: 0;
                height: 0;
                border-left: 4px solid transparent;
                border-right: 4px solid transparent;
                border-top: 6px solid currentColor;
                transition: transform 0.2s ease;
            }
            
            details[open] summary::after {
                transform: translateY(-50%) rotate(180deg);
            }
            
            /* تحسين مظهر الـ checkboxes */
            input[type="checkbox"]:checked {
                background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e");
            }
            
            /* تأثير hover للبطاقات */
            .group:hover {
                transform: translateY(-2px);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
            
            /* تحسين مظهر الـ scrollbar */
            .overflow-auto::-webkit-scrollbar {
                width: 6px;
            }
            
            .overflow-auto::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 3px;
            }
            
            .overflow-auto::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 3px;
            }
            
            .overflow-auto::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            /* تحسين مظهر الأزرار والكورسر */
            button, 
            .cursor-pointer,
            label[for],
            summary,
            input[type="checkbox"],
            input[type="submit"],
            a[href] {
                cursor: pointer !important;
            }

            button:hover,
            .cursor-pointer:hover {
                cursor: pointer !important;
                transform: translateY(-1px);
            }

            button:active,
            .cursor-pointer:active {
                transform: translateY(0);
            }

            button:disabled,
            .disabled {
                cursor: not-allowed !important;
                opacity: 0.5;
            }

            button:disabled:hover,
            .disabled:hover {
                cursor: not-allowed !important;
                transform: none !important;
            }

            /* تحسين تأثيرات المودال */
            #editModal {
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }

            /* تحسين تأثيرات الصلاحيات المحددة */
            .permission-item:has(input:checked) {
                background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.1)) !important;
                border-color: rgba(245, 158, 11, 0.3) !important;
                box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.2) !important;
            }

            .permission-item:has(input:checked) span {
                color: rgb(251, 191, 36) !important;
                font-weight: 500 !important;
            }

            /* تحسين تأثيرات المرور فوق البطاقات */
            .group:hover .transform {
                transform: translateY(-4px) !important;
            }

            /* تحسين أزرار الأكشن */
            .action-button {
                position: relative;
                overflow: hidden;
            }

            .action-button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }

            .action-button:hover::before {
                left: 100%;
            }

            /* تحسين الأيقونات */
            svg {
                transition: all 0.2s ease;
            }

            button:hover svg,
            .cursor-pointer:hover svg {
                transform: scale(1.1);
            }

            /* تحسين الفوكس للعناصر التفاعلية */
            input:focus,
            button:focus,
            select:focus,
            textarea:focus {
                outline: 2px solid rgba(245, 158, 11, 0.5) !important;
                outline-offset: 2px !important;
            }
        `;
        document.head.appendChild(style);

        // إضافة event listener للبحث في المودال
        const editSearchInput = document.getElementById('editPermissionSearch');
        if (editSearchInput) {
            editSearchInput.addEventListener('input', function() {
                filterEditPermissions(this.value);
            });
        }

        // إضافة مستمع لإغلاق المودال بالنقر خارجه
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // إضافة مستمع لإغلاق المودال بمفتاح Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('editModal').classList.contains('hidden')) {
                closeEditModal();
            }
        });

        // إضافة مستمع لإغلاق النوافذ المنبثقة عند النقر خارجها
        document.addEventListener('click', function(e) {
            if (!e.target.closest('details')) {
                document.querySelectorAll('details[open]').forEach(details => {
                    if (!details.contains(e.target)) {
                        details.removeAttribute('open');
                    }
                });
            }
        });

        // إضافة تأثيرات عند تحديد/إلغاء تحديد الصلاحيات
        document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const label = this.closest('label');
                if (this.checked) {
                    label.classList.add('bg-emerald-500/10', 'border-emerald-500/30');
                    label.classList.remove('bg-slate-800/40');
                } else {
                    label.classList.remove('bg-emerald-500/10', 'border-emerald-500/30');
                    label.classList.add('bg-slate-800/40');
                }
            });
            
            // تطبيق الستايل الأولي
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.add('bg-emerald-500/10', 'border-emerald-500/30');
                label.classList.remove('bg-slate-800/40');
            }
        });
    });
</script>
@endpush
