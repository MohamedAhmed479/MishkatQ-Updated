@extends('admin.layouts.app')

@section('page-title', 'خطط الحفظ — ' . $chapter->name_ar)
@section('page-subtitle', 'عرض جميع عناصر الخطط التي تستخدم السورة')

@section('content')
<div class="p-6">
    <!-- Header with Back -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.chapters.show', $chapter) }}"
               class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
               title="عودة لتفاصيل السورة">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">خطط الحفظ — سورة {{ $chapter->name_ar }}</h2>
                <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300">
                        إجمالي الخطط: {{ number_format($planItems->total()) }}
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.chapters.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            السور
        </a>
    </div>

    <!-- Table Card -->
    <div class="card-elegant rounded-xl overflow-hidden">
                    @if($planItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800">
                        <tr>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">#</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">المستخدم</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">اسم الخطة</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">نوع العنصر</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">تاريخ البداية</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">تاريخ الانتهاء</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">الحالة</th>
                            <th class="text-right p-4 font-semibold text-slate-700 dark:text-slate-300 text-sm">الإجراءات</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($planItems as $item)
                        @php
                            $type = $item->type ?? 'verse';
                            $typeMap = [
                                'verse' => ['label' => 'آية', 'cls' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300'],
                                'chapter' => ['label' => 'سورة', 'cls' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'],
                                'juz' => ['label' => 'جزء', 'cls' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300'],
                            ];
                            $typeConf = $typeMap[$type] ?? $typeMap['verse'];
                            $status = $item->status ?? 'pending';
                            $statusMap = [
                                'completed' => ['label' => 'مكتمل', 'cls' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'],
                                'in_progress' => ['label' => 'قيد التقدم', 'cls' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300'],
                                'pending' => ['label' => 'في الانتظار', 'cls' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'],
                                'overdue' => ['label' => 'متأخر', 'cls' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300'],
                                'cancelled' => ['label' => 'ملغي', 'cls' => 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'],
                            ];
                            $statusConf = $statusMap[$status] ?? $statusMap['pending'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-sm font-medium">
                                    {{ $loop->iteration }}
                                </span>
                                        </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($item->memorizationPlan->user->name ?? 'م', 0, 1)) }}
                                                </div>
                                                <div>
                                        <div class="font-medium text-slate-900 dark:text-slate-100">{{ $item->memorizationPlan->user->name ?? 'غير محدد' }}</div>
                                        <div class="text-xs text-slate-500">ID: {{ $item->memorizationPlan->user_id ?? 'غير محدد' }}</div>
                                                </div>
                                            </div>
                                        </td>
                            <td class="p-4 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $item->memorizationPlan->name ?? 'غير محدد' }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $typeConf['cls'] }}">{{ $typeConf['label'] }}</span>
                                        </td>
                            <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('Y-m-d') : 'غير محدد' }}</td>
                            <td class="p-4 text-sm text-slate-600 dark:text-slate-400">{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('Y-m-d') : 'غير محدد' }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusConf['cls'] }}">{{ $statusConf['label'] }}</span>
                                        </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                            onclick="togglePlanModal('plan-{{ $item->id }}')"
                                            title="عرض التفاصيل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                        <!-- Tailwind Modal -->
                        <div id="plan-{{ $item->id }}" class="fixed inset-0 z-50 hidden">
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="togglePlanModal('plan-{{ $item->id }}')"></div>
                            <div class="absolute inset-0 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-3xl w-full shadow-2xl">
                                    <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-700">
                                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">تفاصيل الخطة — {{ $item->memorizationPlan->name ?? 'خطة' }}</h3>
                                        <button onclick="togglePlanModal('plan-{{ $item->id }}')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <h6 class="font-semibold mb-3 text-slate-800 dark:text-slate-100">معلومات المستخدم</h6>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                                                        <span class="text-slate-600 dark:text-slate-400">الاسم</span>
                                                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->memorizationPlan->user->name ?? 'غير محدد' }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                                                        <span class="text-slate-600 dark:text-slate-400">البريد الإلكتروني</span>
                                                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->memorizationPlan->user->email ?? 'غير محدد' }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between py-2">
                                                        <span class="text-slate-600 dark:text-slate-400">تاريخ التسجيل</span>
                                                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->memorizationPlan->user->created_at ? $item->memorizationPlan->user->created_at->format('Y-m-d') : 'غير محدد' }}</span>
                                                    </div>
                                                </div>
                                                        </div>
                                            <div>
                                                <h6 class="font-semibold mb-3 text-slate-800 dark:text-slate-100">تفاصيل الخطة</h6>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                                                        <span class="text-slate-600 dark:text-slate-400">اسم الخطة</span>
                                                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->memorizationPlan->name ?? 'غير محدد' }}</span>
                                                        </div>
                                                    <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                                                        <span class="text-slate-600 dark:text-slate-400">نوع العنصر</span>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $typeConf['cls'] }}">{{ $typeConf['label'] }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between py-2">
                                                        <span class="text-slate-600 dark:text-slate-400">الحالة</span>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusConf['cls'] }}">{{ $statusConf['label'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                                                    <span class="text-slate-600 dark:text-slate-400">تاريخ البداية</span>
                                                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('Y-m-d') : 'غير محدد' }}</span>
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <span class="text-slate-600 dark:text-slate-400">تاريخ الانتهاء</span>
                                                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('Y-m-d') : 'غير محدد' }}</span>
                                                </div>
                                            </div>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-slate-700">
                                                    <span class="text-slate-600 dark:text-slate-400">تاريخ الإنشاء</span>
                                                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : 'غير محدد' }}</span>
                                                </div>
                                                <div class="flex items-center justify-between py-2">
                                                    <span class="text-slate-600 dark:text-slate-400">آخر تحديث</span>
                                                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : 'غير محدد' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end p-6 border-t border-slate-200 dark:border-slate-700">
                                        <button onclick="togglePlanModal('plan-{{ $item->id }}')" class="px-6 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
            <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                            {{ $planItems->links() }}
                        </div>
                    @else
            <div class="p-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                            </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-slate-800 dark:text-slate-100 mb-2">لا توجد خطط حفظ</h3>
                        <p class="text-slate-600 dark:text-slate-400">لم يتم العثور على خطط حفظ تستخدم هذه السورة</p>
                        </div>
                </div>
            </div>
        @endif
</div>

    <script>
        function togglePlanModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const isHidden = el.classList.contains('hidden');
            if (isHidden) {
                el.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                el.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('[id^="plan-"]').forEach(m => m.classList.add('hidden'));
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</div>
@endsection
