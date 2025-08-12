@extends('admin.layouts.app')

@section('page-title', 'تفاصيل سجل لوحة المتصدرين')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.leaderboards.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">تفاصيل السجل</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400">عرض بيانات سجل لوحة المتصدرين</p>
            </div>
        </div>

        <div class="card-elegant rounded-xl p-6">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-slate-500">المستخدم</div>
                        <div class="text-slate-800 dark:text-slate-100 font-semibold">{{ $leaderboard->user->name }} ({{ $leaderboard->user->email }})</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">نوع الفترة</div>
                        @php $labels = ['daily'=>'يومي','weekly'=>'أسبوعي','monthly'=>'شهري','yearly'=>'سنوي']; @endphp
                        <div class="text-slate-800 dark:text-slate-100">{{ $labels[$leaderboard->period_type] ?? $leaderboard->period_type }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-sm text-slate-500">النقاط</div>
                        <div class="text-slate-800 dark:text-slate-100">{{ $leaderboard->total_points }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">الرتبة</div>
                        <div class="text-slate-800 dark:text-slate-100">{{ $leaderboard->rank }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">الفترة</div>
                        <div class="text-slate-800 dark:text-slate-100">{{ $leaderboard->period_start?->format('Y-m-d') }} — {{ $leaderboard->period_end?->format('Y-m-d') }}</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('admin.leaderboards.edit', $leaderboard) }}" class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعديل
                    </a>
                    <form method="POST" action="{{ route('admin.leaderboards.destroy', $leaderboard) }}" onsubmit="return confirm('هل تريد حذف هذا السجل؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-slate-200 dark:border-slate-600 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


