@extends('admin.layouts.app')

@section('page-title', 'الصلاحيات')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-white">إدارة الصلاحيات</h1>
                    <p class="mt-2 text-lg text-white/80">تحكم في أذونات النظام والوصول للوظائف</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-white/70">إجمالي الصلاحيات</div>
                <div class="text-3xl font-bold text-white">{{ $permissions->total() ?? $permissions->count() }}</div>
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

    <!-- Search and Filter Section -->
    <div class="rounded-2xl bg-gradient-to-r from-slate-800/50 to-slate-700/50 border border-slate-700 p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/20">
                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">البحث والفلترة</h3>
                    <p class="text-sm text-slate-400">ابحث في الصلاحيات أو فلتر حسب النوع</p>
                </div>
            </div>
            <div class="text-sm text-slate-400">
                عدد النتائج: <span id="resultsCount" class="text-white font-bold">{{ $permissions->count() }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-2">
                <label for="searchInput" class="block text-sm font-medium text-white">
                    🔍 البحث
                </label>
                <input type="text" 
                       id="searchInput"
                       placeholder="ابحث في أسماء الصلاحيات..." 
                       class="w-full rounded-xl bg-slate-700 border border-slate-600 px-4 py-3 text-white placeholder-slate-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300" />
            </div>
            <div class="space-y-2">
                <label for="categoryFilter" class="block text-sm font-medium text-white">
                    📂 الفئة
                </label>
                <select id="categoryFilter" 
                        class="w-full rounded-xl bg-slate-700 border border-slate-600 px-4 py-3 text-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300">
                    <option value="">جميع الفئات</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-white">
                    🎛️ الإجراءات
                </label>
                <div class="flex gap-2">
                    <button onclick="clearFilters()" 
                            class="action-button flex-1 rounded-xl bg-slate-600 px-4 py-3 text-sm font-medium text-white hover:bg-slate-700 transition-all duration-300 cursor-pointer">
                        مسح الفلاتر
                    </button>
                    <button onclick="exportPermissions()" 
                            class="action-button flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700 transition-all duration-300 cursor-pointer">
                        تصدير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Permission Section -->
    @can('permissions.create')
    <div class="rounded-2xl bg-gradient-to-r from-emerald-600/10 to-teal-600/10 border border-emerald-500/20 p-6 shadow-xl">
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/20">
                <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white">إضافة صلاحية جديدة</h3>
                <p class="text-sm text-slate-300">قم بإنشاء صلاحية جديدة لتنظيم الوصول للنظام</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label for="permission_name" class="block text-sm font-medium text-white">
                        📝 اسم الصلاحية
                    </label>
                    <input type="text" 
                           id="permission_name"
                           name="name" 
                           placeholder="مثال: users.create, posts.edit"
                           class="w-full rounded-xl bg-slate-700 border border-slate-600 px-4 py-3 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-300" 
                           required />
                </div>
                <div class="space-y-2">
                    <label for="permission_category" class="block text-sm font-medium text-white">
                        📂 الفئة (اختياري)
                    </label>
                    <input type="text" 
                           id="permission_category"
                           placeholder="مثال: users, posts, settings"
                           class="w-full rounded-xl bg-slate-700 border border-slate-600 px-4 py-3 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-300" />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-transparent">
                        &nbsp;
                    </label>
                    <button type="submit" 
                            class="action-button w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-3 font-medium text-white shadow-lg hover:from-emerald-700 hover:to-emerald-800 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        إضافة الصلاحية
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endcan

    <!-- Permissions List Section -->
    <div class="rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 shadow-xl overflow-hidden">
        <div class="border-b border-slate-700 bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/20">
                        <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">قائمة الصلاحيات</h3>
                        <p class="text-sm text-slate-400">إدارة وتعديل الصلاحيات الموجودة</p>
                    </div>
                </div>
                <div class="rounded-lg bg-slate-700/50 px-3 py-1 text-sm text-slate-300">
                    المعروض: <span id="visibleCount">{{ $permissions->count() }}</span>
                </div>
            </div>
        </div>

        @if($permissions->count() > 0)
        <div id="permissionsContainer" class="divide-y divide-slate-700">
            @php
                // Ensure we always work with a collection of models, not the paginator itself
                $permissionsCollection = method_exists($permissions, 'getCollection') ? $permissions->getCollection() : collect($permissions);
                $groupedPermissions = $permissionsCollection->groupBy(function($permission) {
                    return \Illuminate\Support\Str::before($permission->name ?? '', '.');
                });
            @endphp

            @foreach($groupedPermissions as $category => $categoryPermissions)
            <div class="permission-category" data-category="{{ $category }}">
                <!-- Category Header -->
                <div class="bg-gradient-to-r from-slate-800/70 to-slate-700/70 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-purple-400"></div>
                        <h4 class="text-lg font-bold text-white">{{ $category ?: 'عام' }}</h4>
                        <span class="rounded-full bg-slate-700 px-3 py-1 text-xs text-slate-300">
                            {{ $categoryPermissions->count() }} صلاحية
                        </span>
                    </div>
                </div>

                <!-- Category Permissions -->
                <div class="category-content">
                    @foreach($categoryPermissions as $permission)
                    <div class="permission-item group p-6 hover:bg-slate-800/50 transition-all duration-300" 
                         data-name="{{ strtolower($permission->name) }}"
                         data-category="{{ $category }}">
                        <div class="flex items-center justify-between">
                            <!-- Permission Info -->
                            <div class="flex items-center gap-4 flex-1">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white text-sm font-bold">
                                    {{ strtoupper(substr(explode('.', $permission->name)[1] ?? 'P', 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h5 class="text-lg font-semibold text-white">{{ $permission->name }}</h5>
                                        @if($permission->guard_name !== 'web')
                                        <span class="rounded-full bg-blue-500/20 px-2 py-1 text-xs text-blue-400">
                                            {{ $permission->guard_name }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-slate-400">
                                        📅 {{ $permission->created_at ? $permission->created_at->format('d/m/Y') : 'غير محدد' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                @can('permissions.edit')
                                <button type="button" 
                                        onclick="editPermission({{ $permission->id }}, '{{ $permission->name }}')"
                                        class="action-button inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-600 to-amber-700 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-amber-700 hover:to-amber-800 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    تعديل
                                </button>
                                @endcan

                                @can('permissions.delete')
                                <form method="POST" 
                                      action="{{ route('admin.permissions.destroy', $permission) }}" 
                                      onsubmit="return confirm('⚠️ هل أنت متأكد من حذف هذه الصلاحية؟\n\nقد يؤثر هذا على الأدوار المرتبطة بها.');" 
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
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-700/50 mb-4">
                <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-slate-300 mb-2">لا توجد صلاحيات</h3>
            <p class="text-slate-400 mb-6">لم يتم إنشاء أي صلاحيات بعد. ابدأ بإنشاء صلاحية جديدة.</p>
            @can('permissions.create')
            <button onclick="document.querySelector('#permission_name').focus()" 
                    class="action-button inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 font-medium text-white hover:bg-emerald-700 transition-colors cursor-pointer">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                إنشاء أول صلاحية
            </button>
            @endcan
        </div>
        @endif
    </div>

    <!-- Edit Permission Modal -->
    <div id="editPermissionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="mx-4 w-full max-w-lg transform rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 shadow-2xl transition-all duration-300 scale-95 opacity-0" id="editPermissionModalContent">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-700 bg-gradient-to-r from-amber-600/10 to-amber-700/10 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-500/20">
                        <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">تعديل الصلاحية</h3>
                        <p class="text-sm text-slate-400">تعديل اسم الصلاحية</p>
                    </div>
                </div>
                <button type="button" 
                        onclick="closeEditPermissionModal()"
                        class="rounded-xl p-2 text-slate-400 hover:bg-slate-700 hover:text-white transition-all duration-200 cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editPermissionForm" method="POST" action="" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="editPermissionName" class="block text-sm font-medium text-slate-300">
                            📝 اسم الصلاحية
                        </label>
                        <input type="text" 
                               id="editPermissionName"
                               name="name" 
                               class="w-full rounded-xl bg-slate-700 border border-slate-600 px-4 py-3 text-white placeholder-slate-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all duration-300" 
                               required />
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeEditPermissionModal()"
                            class="action-button inline-flex items-center gap-2 rounded-xl bg-slate-600 px-6 py-3 font-medium text-white hover:bg-slate-700 transition-all duration-300 cursor-pointer">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="action-button inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-3 font-medium text-white shadow-lg hover:from-amber-700 hover:to-amber-800 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // بيانات الصلاحيات - استخدم مجموعة العناصر لضمان عدم تمرير أعداد صحيحة
    let permissionsData = @json((method_exists($permissions, 'getCollection') ? $permissions->getCollection() : collect($permissions))->keyBy('id'));

    // دالة فتح modal التعديل
    function editPermission(permissionId, permissionName) {
        document.getElementById('editPermissionName').value = permissionName;
        document.getElementById('editPermissionForm').action = `/admin/permissions/${permissionId}`;

        // إظهار المودال مع تأثيرات
        const modal = document.getElementById('editPermissionModal');
        const content = document.getElementById('editPermissionModalContent');
        
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

    // دالة إغلاق modal التعديل
    function closeEditPermissionModal() {
        const modal = document.getElementById('editPermissionModal');
        const content = document.getElementById('editPermissionModalContent');
        
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
        document.getElementById('editPermissionForm').reset();
    }

    // دالة البحث والفلترة
    function performSearch() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        
        const permissionItems = document.querySelectorAll('.permission-item');
        const categories = document.querySelectorAll('.permission-category');
        let visibleCount = 0;

        permissionItems.forEach(item => {
            const name = item.dataset.name;
            const category = item.dataset.category;
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = !categoryFilter || category === categoryFilter;
            
            if (matchesSearch && matchesCategory) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // إخفاء الفئات الفارغة
        categories.forEach(categoryDiv => {
            const visibleItems = categoryDiv.querySelectorAll('.permission-item:not([style*="display: none"])');
            if (visibleItems.length === 0) {
                categoryDiv.style.display = 'none';
            } else {
                categoryDiv.style.display = '';
            }
        });

        document.getElementById('visibleCount').textContent = visibleCount;
        document.getElementById('resultsCount').textContent = visibleCount;
    }

    // دالة مسح الفلاتر
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        performSearch();
    }

    // دالة تصدير الصلاحيات
    function exportPermissions() {
        const permissions = Array.from(document.querySelectorAll('.permission-item:not([style*="display: none"])'))
            .map(item => ({
                name: item.querySelector('h5').textContent,
                category: item.dataset.category
            }));

        const csvContent = "data:text/csv;charset=utf-8," 
            + "الاسم,الفئة\n"
            + permissions.map(p => `${p.name},${p.category}`).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "permissions.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة event listeners للبحث والفلترة
        document.getElementById('searchInput').addEventListener('input', performSearch);
        document.getElementById('categoryFilter').addEventListener('change', performSearch);

        // ملء خيارات الفئات
        const categories = [...new Set(Array.from(document.querySelectorAll('.permission-item')).map(item => item.dataset.category))];
        const categorySelect = document.getElementById('categoryFilter');
        
        categories.forEach(category => {
            if (category) {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                categorySelect.appendChild(option);
            }
        });

        // إضافة مستمع لإغلاق المودال بالنقر خارجه
        document.getElementById('editPermissionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditPermissionModal();
            }
        });

        // إضافة مستمع لإغلاق المودال بمفتاح Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('editPermissionModal').classList.contains('hidden')) {
                closeEditPermissionModal();
            }
        });

        // إضافة تأثيرات CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }

            /* تحسين مظهر الأزرار والكورسر */
            button, 
            .cursor-pointer,
            label[for],
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

            /* تحسين تأثيرات المودال */
            #editPermissionModal {
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
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
            select:focus {
                outline: 2px solid rgba(139, 92, 246, 0.5) !important;
                outline-offset: 2px !important;
            }

            /* تحسين تأثيرات المرور فوق البطاقات */
            .group:hover {
                transform: translateY(-2px);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush
@endsection
