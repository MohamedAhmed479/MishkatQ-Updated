@extends('admin.layouts.app')

@section('page-title', 'اللوحة الرئيسية')
@section('page-subtitle', 'نظرة عامة على النظام والإحصائيات')

@section('content')
<div class="space-y-6">
<form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
<div class="col-span-1 md:col-span-2">
<label class="block text-xs text-slate-400 mb-1">الفترة الزمنية</label>
<select name="range" class="w-full px-3 py-2 rounded-xl bg-slate-800/60 border border-slate-700 text-slate-100 focus:outline-none">
@foreach(($filterOptions['ranges'] ?? []) as $key => $label)
<option value="{{ $key }}" {{ ($filters['range'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
@endforeach
</select>
</div>
<div class="flex items-end">
<button type="submit" class="w-full px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold">تطبيق</button>
</div>
</form>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
@foreach(($kpis ?? []) as $kpi)
@php
$label = $kpi['label'] ?? '';
$from = 'from-emerald-500';
$to = 'to-emerald-600';
$icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1"/></svg>';
if (str_contains($label, 'الطلاب')) { $from='from-sky-500'; $to='to-sky-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0V10m0 10H7"/></svg>'; }
elseif (str_contains($label, 'الآيات')) { $from='from-indigo-500'; $to='to-indigo-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 016.5 17H20M4 12.5A2.5 2.5 0 016.5 10H20M4 5.5A2.5 2.5 0 016.5 3H20"/></svg>'; }
elseif (str_contains($label, 'الالتزام')) { $from='from-amber-500'; $to='to-amber-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3 3 7-7"/></svg>'; }
elseif (str_contains($label, 'تقييم')) { $from='from-violet-500'; $to='to-violet-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.035a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.802-2.035a1 1 0 00-1.175 0l-2.802 2.035c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81h3.463a1 1 0 00.95-.69l1.068-3.292z"/></svg>'; }
elseif (str_contains($label, 'المراجعات المكتملة')) { $from='from-emerald-500'; $to='to-emerald-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>'; }
elseif (str_contains($label, 'متأخرة')) { $from='from-rose-500'; $to='to-rose-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/></svg>'; }
elseif (str_contains($label, 'مجدولة')) { $from='from-amber-500'; $to='to-amber-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V8H3v11a2 2 0 002 2z"/></svg>'; }
elseif (str_contains($label, 'متوسط مراجعات')) { $from='from-sky-500'; $to='to-sky-600'; $icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10m-7 6h7"/></svg>'; }
@endphp
<div class="card-elegant rounded-2xl p-4 relative overflow-hidden">
<div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-l {{ $from }} {{ $to }}"></div>
<div class="flex items-center justify-between mb-3">
<div class="flex items-center gap-3">
<div class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-white bg-gradient-to-br {{ $from }} {{ $to }}">
{!! $icon !!}
</div>
<div class="text-sm text-slate-500 dark:text-slate-400">{{ $kpi['label'] }}</div>
</div>
@if(isset($kpi['delta']))
<div class="text-xs {{ $kpi['delta'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ $kpi['delta'] >= 0 ? '+' : '' }}{{ number_format($kpi['delta'], 1) }}%</div>
@endif
</div>
<div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $kpi['value'] }}</div>
</div>
@endforeach
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">مراجعات ناجحة أسبوعيًا</div>
</div>
<canvas id="cohortChart" height="220"></canvas>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">سرعة التقدم (مراجعات ناجحة)</div>
</div>
<canvas id="velocityChart" height="220"></canvas>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">تغطية الحفظ حسب السور</div>
</div>
<canvas id="coverageChart" height="220"></canvas>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">اتجاه الالتزام بالمراجعة</div>
</div>
<canvas id="adherenceChart" height="220"></canvas>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">حالة SRS (اليوم)</div>
</div>
<canvas id="srsStatusChart" height="220"></canvas>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">نسبة نجاح/فشل المراجعات</div>
</div>
<canvas id="srsBreakdownChart" height="220"></canvas>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">المتفوقون</div>
</div>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="text-slate-500">
<tr>
<th class="text-right py-2">الطالب</th>
<th class="text-right py-2">الآيات المحفوظة</th>
<th class="text-right py-2">التقييم</th>
</tr>
</thead>
<tbody class="text-slate-800 dark:text-slate-200">
@foreach(($topStudents ?? []) as $s)
<tr class="border-t border-slate-100 dark:border-slate-700/50">
<td class="py-2">{{ $s['name'] }}</td>
<td class="py-2">{{ $s['saved'] }}</td>
<td class="py-2">{{ $s['score'] }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">طلاب معرّضون للانقطاع</div>
</div>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="text-slate-500">
<tr>
<th class="text-right py-2">الطالب</th>
<th class="text-right py-2">الخطر</th>
<th class="text-right py-2">آخر نشاط</th>
<th class="text-right py-2">إجراءات</th>
</tr>
</thead>
<tbody class="text-slate-800 dark:text-slate-200">
@foreach(($atRiskStudents ?? []) as $s)
<tr class="border-t border-slate-100 dark:border-slate-700/50">
<td class="py-2">{{ $s['name'] }}</td>
<td class="py-2"><span class="px-2 py-1 rounded-lg text-xs {{ $s['risk'] === 'مرتفع' ? 'bg-red-500/20 text-red-400' : ($s['risk'] === 'متوسط' ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400') }}">{{ $s['risk'] }}</span></td>
<td class="py-2">{{ $s['last_activity'] }}</td>
<td class="py-2 flex gap-2"><button type="button" class="px-2 py-1 rounded-lg bg-emerald-600 text-white">تذكير</button><button type="button" class="px-2 py-1 rounded-lg bg-slate-700 text-white">اتصال</button></td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">لوحة المتصدرين (هذا الشهر)</div>
</div>
<div class="divide-y divide-slate-100 dark:divide-slate-700/50">
@php $medals=['🥇','🥈','🥉']; @endphp
@foreach(($leaderboardTop ?? []) as $i => $row)
<div class="flex items-center justify-between py-3">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-lg">{{ $medals[$i] ?? ($row['rank'] ?? $i+1) }}</div>
<div>
<div class="font-semibold text-slate-800 dark:text-slate-100">{{ $row['name'] }}</div>
<div class="text-xs text-slate-500">الرتبة: {{ $row['rank'] }}</div>
</div>
</div>
<div class="inline-flex items-center gap-2">
<span class="text-slate-500 text-xs">النقاط</span>
<span class="px-2 py-1 rounded-lg bg-emerald-500/15 text-emerald-400 font-semibold">{{ number_format($row['points'] ?? 0) }}</span>
</div>
</div>
@endforeach
</div>
</div>
<div class="card-elegant rounded-2xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="font-semibold text-slate-700 dark:text-slate-200">المراجعات الأخيرة</div>
<div class="flex items-center gap-2"><a href="#" class="px-3 py-1 rounded-lg bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200">تصدير CSV</a></div>
</div>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="text-slate-500">
<tr>
<th class="text-right py-2">التاريخ</th>
<th class="text-right py-2">المقطع</th>
<th class="text-right py-2">المستخدم</th>
<th class="text-right py-2">التقييم</th>
<th class="text-right py-2">النتيجة</th>
</tr>
</thead>
<tbody class="text-slate-800 dark:text-slate-200">
@foreach(($recentReviews ?? []) as $r)
<tr class="border-t border-slate-100 dark:border-slate-700/50">
<td class="py-2">{{ $r['date'] }}</td>
<td class="py-2">{{ $r['segment'] }}</td>
<td class="py-2">{{ $r['user'] }}</td>
<td class="py-2">{{ $r['performance'] }}</td>
<td class="py-2">{{ $r['successful'] ? 'ناجحة' : 'غير ناجحة' }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const chartsData = @json($charts ?? []);
function buildCohortChart() {
const ctx = document.getElementById('cohortChart');
if (!ctx || !chartsData.cohort) return;
const labels = chartsData.cohort.labels;
const datasets = chartsData.cohort.datasets.map((d) => ({ label: d.label, data: d.data, borderWidth: 2, borderColor: d.color, backgroundColor: d.color, fill: false, tension: 0.35 }));
new Chart(ctx, { type: 'line', data: { labels, datasets }, options: { responsive: true, plugins: { legend: { display: true, rtl: true, labels: { usePointStyle: true } } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } } });
}
function buildVelocityChart() {
const ctx = document.getElementById('velocityChart');
if (!ctx || !chartsData.velocity) return;
new Chart(ctx, { type: 'bar', data: { labels: chartsData.velocity.labels, datasets: [{ label: 'مراجعات ناجحة', data: chartsData.velocity.data, borderRadius: 10, backgroundColor: 'rgba(16, 185, 129, 0.6)' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } } });
}
function buildCoverageChart() {
const ctx = document.getElementById('coverageChart');
if (!ctx || !chartsData.coverage) return;
new Chart(ctx, { type: 'polarArea', data: { labels: chartsData.coverage.labels, datasets: [{ data: chartsData.coverage.data, backgroundColor: chartsData.coverage.colors, borderWidth: 0 }] }, options: { plugins: { legend: { rtl: true, position: 'right' } } } });
}
function buildAdherenceChart() {
const ctx = document.getElementById('adherenceChart');
if (!ctx || !chartsData.adherence) return;
new Chart(ctx, { type: 'line', data: { labels: chartsData.adherence.labels, datasets: [{ label: 'الالتزام %', data: chartsData.adherence.data, borderColor: 'rgba(59, 130, 246, 0.9)', backgroundColor: 'rgba(59, 130, 246, 0.25)', fill: true, tension: 0.35 }] }, options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } } });
}
function buildSrsStatusChart() {
const ctx = document.getElementById('srsStatusChart');
if (!ctx || !chartsData.srsStatus) return;
new Chart(ctx, { type: 'doughnut', data: { labels: chartsData.srsStatus.labels, datasets: [{ data: chartsData.srsStatus.data, backgroundColor: chartsData.srsStatus.colors, borderWidth: 0 }] }, options: { plugins: { legend: { rtl: true, position: 'bottom' } } } });
}
function buildSrsBreakdownChart() {
const ctx = document.getElementById('srsBreakdownChart');
if (!ctx || !chartsData.srsBreakdown) return;
new Chart(ctx, { type: 'doughnut', data: { labels: chartsData.srsBreakdown.labels, datasets: [{ data: chartsData.srsBreakdown.data, backgroundColor: chartsData.srsBreakdown.colors, borderWidth: 0 }] }, options: { plugins: { legend: { rtl: true, position: 'bottom' } } } });
}
document.addEventListener('DOMContentLoaded', function() { buildCohortChart(); buildVelocityChart(); buildCoverageChart(); buildAdherenceChart(); buildSrsStatusChart(); buildSrsBreakdownChart(); });
</script>
@endpush
@endsection


