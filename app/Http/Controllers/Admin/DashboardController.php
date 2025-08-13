<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MemorizationPlan;
use App\Models\MemorizationProgress;
use App\Models\SpacedRepetition;
use App\Models\ReviewRecord;
use App\Models\Chapter;
use App\Models\Leaderboard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:admin',
            new ControllerMiddleware('permission:dashboard.view', only: ['index']),
        ];
    }

    public function index(Request $request): View
    {
        $range = $request->input('range', 'last_30_days');
        [$start, $end] = $this->resolveRange($range);
        [$prevStart, $prevEnd] = $this->previousRange($start, $end);

        $filters = [
            'range' => $range,
        ];

        $filterOptions = [
            'ranges' => [
                'last_7_days' => 'آخر 7 أيام',
                'last_30_days' => 'آخر 30 يومًا',
                'this_month' => 'هذا الشهر',
                'last_month' => 'الشهر الماضي',
                'last_12_months' => 'آخر 12 شهرًا',
            ],
        ];

        // KPIs (حقيقية)
        $activeStudents = MemorizationPlan::where('status', 'active')->distinct('user_id')->count('user_id');
        $totalVersesMemorized = (int) MemorizationProgress::sum('verses_memorized');

        $scheduledInRange = SpacedRepetition::whereBetween('scheduled_date', [$start->toDateString(), $end->toDateString()])->count();
        $completedInRange = ReviewRecord::whereBetween('review_date', [$start, $end])->count();
        $adherence = $scheduledInRange > 0 ? round(min($completedInRange, $scheduledInRange) * 100 / $scheduledInRange, 1) : null;

        $scheduledPrev = SpacedRepetition::whereBetween('scheduled_date', [$prevStart->toDateString(), $prevEnd->toDateString()])->count();
        $completedPrev = ReviewRecord::whereBetween('review_date', [$prevStart, $prevEnd])->count();
        $adherencePrev = $scheduledPrev > 0 ? (min($completedPrev, $scheduledPrev) * 100 / $scheduledPrev) : null;
        $adherenceDelta = ($adherence !== null && $adherencePrev !== null) ? round($adherence - $adherencePrev, 1) : null;

        $avgPerformance = ReviewRecord::whereBetween('review_date', [$start, $end])->avg('performance_rating');
        $avgPerformance = $avgPerformance !== null ? round($avgPerformance, 2) : null;

        $today = now()->toDateString();
        $srsToday = SpacedRepetition::whereDate('scheduled_date', $today)->whereNull('last_reviewed_at')->count();
        $srsOverdue = SpacedRepetition::whereDate('scheduled_date', '<', $today)->whereNull('last_reviewed_at')->count();

        $avgReviewsPerStudent = $activeStudents > 0 ? round($completedInRange / $activeStudents, 2) : null;

        $kpis = [
            ['label' => 'الطلاب النشطون', 'value' => number_format($activeStudents)],
            ['label' => 'الآيات المحفوظة (إجمالي)', 'value' => number_format($totalVersesMemorized)],
            ['label' => 'نسبة الالتزام بالمراجعة', 'value' => ($adherence !== null ? ($adherence . '%') : '—'), 'delta' => $adherenceDelta],
            ['label' => 'متوسط تقييم الأداء', 'value' => ($avgPerformance !== null ? ($avgPerformance . '/5') : '—')],
            ['label' => 'المراجعات المكتملة (الفترة)', 'value' => number_format($completedInRange)],
            ['label' => 'مراجعات متأخرة الآن', 'value' => number_format($srsOverdue)],
            ['label' => 'مراجعات اليوم المجدولة', 'value' => number_format($srsToday)],
            ['label' => 'متوسط مراجعات لكل طالب', 'value' => ($avgReviewsPerStudent !== null ? (string)$avgReviewsPerStudent : '—')],
        ];

        // Charts
        $weeks = $this->weekBuckets($start, $end);
        $adherenceTrendLabels = [];
        $adherenceTrendData = [];
        $velocityLabels = [];
        $velocityData = [];

        foreach ($weeks as [$ws, $we]) {
            $label = $ws->format('d M');
            $adherenceTrendLabels[] = $label;
            $velocityLabels[] = $label;

            $weeklyScheduled = SpacedRepetition::whereBetween('scheduled_date', [$ws->toDateString(), $we->toDateString()])->count();
            $weeklyCompleted = ReviewRecord::whereBetween('review_date', [$ws, $we])->count();
            $adherenceTrendData[] = $weeklyScheduled > 0 ? round(min($weeklyCompleted, $weeklyScheduled) * 100 / $weeklyScheduled, 1) : 0;

            $velocityData[] = (int) ReviewRecord::whereBetween('review_date', [$ws, $we])->where('successful', true)->count();
        }

        // Coverage (حسب السور الأكثر حفظًا)
        $coverageRows = MemorizationProgress::select('chapter_id', DB::raw('SUM(verses_memorized) as total'))
            ->groupBy('chapter_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $coverageLabels = [];
        $coverageData = [];
        foreach ($coverageRows as $row) {
            $chapter = Chapter::find($row->chapter_id);
            if ($chapter) {
                $coverageLabels[] = $chapter->name_ar;
                $coverageData[] = (int) $row->total;
            }
        }
        $coverageColors = ['#10b981','#34d399','#6ee7b7','#a7f3d0','#93c5fd','#60a5fa','#c084fc','#f59e0b'];

        // Ratings distribution and success breakdown
        $ratingCounts = ReviewRecord::select('performance_rating', DB::raw('COUNT(*) as c'))
            ->whereBetween('review_date', [$start, $end])
            ->groupBy('performance_rating')
            ->pluck('c', 'performance_rating');
        $ratingsLabels = ['0','1','2','3','4','5'];
        $ratingsData = array_map(function ($r) use ($ratingCounts) {
            return (int) ($ratingCounts[$r] ?? 0);
        }, [0,1,2,3,4,5]);

        $successCount = ReviewRecord::whereBetween('review_date', [$start, $end])->where('successful', true)->count();
        $failCount = ReviewRecord::whereBetween('review_date', [$start, $end])->where('successful', false)->count();

        // SRS Status (اليوم) + breakdown
        $charts = [
            'cohort' => [
                'labels' => $velocityLabels,
                'datasets' => [
                    ['label' => 'مراجعات ناجحة', 'data' => $velocityData, 'color' => 'rgba(16,185,129,0.9)'],
                ],
            ],
            'velocity' => [
                'labels' => $velocityLabels,
                'data' => $velocityData,
            ],
            'coverage' => [
                'labels' => $coverageLabels,
                'data' => $coverageData,
                'colors' => $coverageColors,
            ],
            'adherence' => [
                'labels' => $adherenceTrendLabels,
                'data' => $adherenceTrendData,
            ],
            'srsStatus' => [
                'labels' => ['متأخرة', 'مجدولة اليوم', 'مكتملة اليوم'],
                'data' => [$srsOverdue, $srsToday, SpacedRepetition::whereDate('last_reviewed_at', $today)->count()],
                'colors' => ['#ef4444', '#f59e0b', '#10b981'],
            ],
            'ratings' => [
                'labels' => $ratingsLabels,
                'data' => $ratingsData,
            ],
            'srsBreakdown' => [
                'labels' => ['ناجحة', 'غير ناجحة'],
                'data' => [$successCount, $failCount],
                'colors' => ['#10b981', '#ef4444'],
            ],
        ];

        // Top students (إجمالي الآيات المحفوظة)
        $topStudents = User::select('users.id', 'users.name', DB::raw('COALESCE(SUM(memorization_progress.verses_memorized), 0) as total'))
            ->leftJoin('memorization_progress', 'memorization_progress.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($u) => [
                'name' => $u->name,
                'saved' => $u->total . ' آية',
                'score' => '-',
            ]);

        // Top leaderboard (هذا الشهر)
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $leaderboardTop = Leaderboard::with('user')
            ->where('period_type', 'monthly')
            ->where('period_start', $monthStart)
            ->where('period_end', $monthEnd)
            ->orderBy('rank')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                return [
                    'name' => $r->user?->name ?? '—',
                    'points' => (int) $r->total_points,
                    'rank' => (int) $r->rank,
                ];
            });

        // At-risk students (كثرة المتأخر/آخر نشاط قديم)
        $overdueByUser = DB::table('spaced_repetitions as s')
            ->join('plan_items as pi', 'pi.id', '=', 's.plan_item_id')
            ->join('memorization_plans as mp', 'mp.id', '=', 'pi.plan_id')
            ->select('mp.user_id', DB::raw('COUNT(*) as overdue_count'), DB::raw('MAX(s.last_reviewed_at) as last_review'))
            ->whereDate('s.scheduled_date', '<', $today)
            ->whereNull('s.last_reviewed_at')
            ->groupBy('mp.user_id')
            ->orderByDesc('overdue_count')
            ->limit(10)
            ->get();

        $atRiskStudents = $overdueByUser->map(function ($row) {
            $user = User::find($row->user_id);
            $risk = $row->overdue_count >= 20 ? 'مرتفع' : ($row->overdue_count >= 10 ? 'متوسط' : 'منخفض');
            $last = $row->last_review ? Carbon::parse($row->last_review)->diffForHumans() : 'غير متاح';
            return [
                'name' => $user?->name ?? ('مستخدم #' . $row->user_id),
                'risk' => $risk,
                'last_activity' => $last,
            ];
        })->values();

        // Recent reviews
        $recentReviews = ReviewRecord::with(['spacedRepetition.planItem.quranSurah', 'spacedRepetition.planItem.verseStart', 'spacedRepetition.planItem.verseEnd', 'spacedRepetition.planItem.memorizationPlan.user'])
            ->orderByDesc('review_date')
            ->limit(12)
            ->get()
            ->map(function ($r) {
                $planItem = $r->spacedRepetition?->planItem;
                $plan = $planItem?->memorizationPlan;
                $desc = $planItem ? $planItem->getDescription() : '—';
                return [
                    'date' => $r->review_date?->format('Y-m-d'),
                    'segment' => $desc,
                    'user' => $plan?->user?->name ?? '—',
                    'performance' => $r->performance_rating,
                    'successful' => $r->successful,
                ];
            });

        return view('admin.dashboard', compact(
            'filters', 'filterOptions', 'kpis', 'charts', 'topStudents', 'atRiskStudents', 'recentReviews', 'leaderboardTop'
        ));
    }

    private function resolveRange(string $range): array
    {
        $now = now();
        return match ($range) {
            'last_7_days' => [(clone $now)->subDays(6)->startOfDay(), (clone $now)->endOfDay()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'last_12_months' => [$now->copy()->subMonths(11)->startOfMonth(), $now->copy()->endOfMonth()],
            default => [(clone $now)->subDays(29)->startOfDay(), (clone $now)->endOfDay()],
        };
    }

    private function previousRange(Carbon $start, Carbon $end): array
    {
        $diffDays = $start->diffInDays($end) + 1;
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($diffDays - 1)->startOfDay();
        return [$prevStart, $prevEnd];
    }

    private function weekBuckets(Carbon $start, Carbon $end): array
    {
        $buckets = [];
        $cursor = $start->copy()->startOfWeek();
        $final = $end->copy()->endOfWeek();
        while ($cursor <= $final) {
            $ws = $cursor->copy()->startOfWeek();
            $we = $cursor->copy()->endOfWeek();
            if ($we > $end) { $we = $end->copy(); }
            if ($ws < $start) { $ws = $start->copy(); }
            $buckets[] = [$ws, $we];
            $cursor->addWeek();
        }
        return $buckets;
    }
}


