@extends('admin.layouts.app')

@section('title', 'تفاصيل التنبيه')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">تفاصيل التنبيه</h1>
            <p class="text-slate-400">{{ $notification->id }}</p>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-medium rounded-lg">
            العودة للتنبيهات
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-slate-400">النوع</div>
                    <div class="font-medium text-slate-100">{{ $notification->type }}</div>
                </div>
                <div>
                    <div class="text-sm text-slate-400">الحالة</div>
                    <div>
                        @if($notification->read_at)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">مقروء</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-400">غير مقروء</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-sm text-slate-400">الجهة</div>
                    <div class="font-medium text-slate-100">{{ $notification->notifiable_type }} #{{ $notification->notifiable_id }}</div>
                </div>
                <div>
                    <div class="text-sm text-slate-400">أنشئ في</div>
                    <div class="font-medium text-slate-100">{{ $notification->created_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            <div>
                <div class="text-sm text-slate-400 mb-2">البيانات</div>
                <pre class="p-4 rounded-xl bg-slate-900/40 text-slate-100 overflow-auto">{{ json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-700 flex items-center gap-3">
            @if($notification->read_at)
            <form action="{{ route('admin.notifications.mark-as-unread', $notification) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 rounded-lg bg-orange-600 text-white">تعليم كغير مقروء</button>
            </form>
            @else
            <form action="{{ route('admin.notifications.mark-as-read', $notification) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white">تعليم كمقروء</button>
            </form>
            @endif
            <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التنبيه؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white">حذف</button>
            </form>
        </div>
    </div>
</div>
@endsection


