@extends('admin.layouts.app')

@section('title', 'إدارة التنبيهات')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">إدارة التنبيهات</h1>
            <p class="text-slate-400">عرض والتحكم في جميع التنبيهات داخل النظام</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">إجمالي التنبيهات</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">المقروءة</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($stats['read']) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">غير المقروءة</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($stats['unread']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-48">
                <label for="q" class="block text-sm font-medium text-slate-300 mb-2">البحث</label>
                <input type="text" name="q" id="q" value="{{ $search }}" placeholder="النوع أو البيانات..." class="w-full px-4 py-2.5 rounded-lg border border-slate-700 bg-slate-800 text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <div class="min-w-32">
                <label for="status" class="block text-sm font-medium text-slate-300 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-700 bg-slate-800 text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">الكل</option>
                    <option value="unread" {{ $status==='unread' ? 'selected' : '' }}>غير مقروء</option>
                    <option value="read" {{ $status==='read' ? 'selected' : '' }}>مقروء</option>
                </select>
            </div>
            <div class="min-w-48">
                <label for="notifiable_type" class="block text-sm font-medium text-slate-300 mb-2">الجهة</label>
                <select name="notifiable_type" id="notifiable_type" class="w-full px-4 py-2.5 rounded-lg border border-slate-700 bg-slate-800 text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">الكل</option>
                    @foreach($notifiableTypes as $type)
                        <option value="{{ $type }}" {{ $notifiableType===$type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-32">
                <label for="sort_order" class="block text-sm font-medium text-slate-300 mb-2">ترتيب</label>
                <select name="sort_order" id="sort_order" class="w-full px-4 py-2.5 rounded-lg border border-slate-700 bg-slate-800 text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="desc" {{ $sortOrder==='desc' ? 'selected' : '' }}>الأحدث أولاً</option>
                    <option value="asc" {{ $sortOrder==='asc' ? 'selected' : '' }}>الأقدم أولاً</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">تطبيق</button>
                <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 font-medium rounded-lg transition-colors">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <form method="POST" action="{{ route('admin.notifications.bulk-delete') }}" id="bulkDeleteForm">
            @csrf
            @method('DELETE')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-4"><input type="checkbox" id="selectAll"></th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-300">المعرف</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-300">النوع</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-300">الجهة</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-300">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-300">تاريخ الإنشاء</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-slate-300">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($notifications as $notification)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4"><input type="checkbox" name="ids[]" value="{{ $notification->id }}" class="rowCheckbox"></td>
                            <td class="px-6 py-4 text-sm text-slate-100">{{ $notification->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-100">{{ class_basename($notification->type) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-100">{{ class_basename($notification->notifiable_type) }} #{{ $notification->notifiable_id }}</td>
                            <td class="px-6 py-4">
                                @if($notification->read_at)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">مقروء</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-400">غير مقروء</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-300">{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.notifications.show', $notification) }}" class="p-2 text-slate-300 hover:text-emerald-400 hover:bg-emerald-900/20 rounded-lg" title="عرض">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($notification->read_at)
                                    <form action="{{ route('admin.notifications.mark-as-unread', $notification) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-orange-400 hover:bg-orange-900/20 rounded-lg" title="تعليم كغير مقروء">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 12H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z"/></svg>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.notifications.mark-as-read', $notification) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-green-400 hover:bg-green-900/20 rounded-lg" title="تعليم كمقروء">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التنبيه؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-red-400 hover:bg-red-900/20 rounded-lg" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-slate-400">
                                    لا توجد تنبيهات حالياً.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-700">
                {{ $notifications->links() }}
            </div>
            @endif

            <!-- Bulk actions -->
             <div class="px-6 py-4 border-t border-slate-700 flex items-center gap-3">
                 <button formaction="{{ route('admin.notifications.bulk-mark-as-read') }}" formmethod="POST" class="px-3 py-2 rounded-lg bg-emerald-600 text-white @permClass('notifications.mark')" @permDisabled('notifications.mark') onclick="injectMethod(this, 'PATCH')">تعليم كمقروء</button>
                 <button formaction="{{ route('admin.notifications.bulk-mark-as-unread') }}" formmethod="POST" class="px-3 py-2 rounded-lg bg-orange-600 text-white @permClass('notifications.mark')" @permDisabled('notifications.mark') onclick="injectMethod(this, 'PATCH')">تعليم كغير مقروء</button>
                 <button type="submit" class="px-3 py-2 rounded-lg bg-red-600 text-white @permClass('notifications.bulk-delete')" @permDisabled('notifications.bulk-delete')>حذف المحدد</button>
             </div>
        </form>
    </div>
</div>

<script>
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }
    function injectMethod(button, method) {
        const form = document.getElementById('bulkDeleteForm');
        let methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = method;
        form.appendChild(methodInput);
    }
</script>
@endsection


