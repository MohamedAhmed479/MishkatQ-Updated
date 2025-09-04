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
            new ControllerMiddleware('permission:dashboard.view', only: ['index', 'exportReviews']),
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
            [
                'id' => 'velocityChart',
                'title' => '📈 اتجاه المراجعات',
                'description' => 'عدد المراجعات الناجحة أسبوعياً',
                'type' => 'line',
                'labels' => $velocityLabels,
                'data' => $velocityData,
                'period' => 'أسبوعي',
            ],
            [
                'id' => 'coverageChart',
                'title' => '📊 تغطية السور',
                'description' => 'توزيع الحفظ حسب السور',
                'type' => 'doughnut',
                'labels' => $coverageLabels,
                'data' => $coverageData,
                'colors' => $coverageColors,
                'period' => 'إجمالي',
            ],
            [
                'id' => 'adherenceChart',
                'title' => '⚡ نسبة الالتزام',
                'description' => 'مدى التزام الطلاب بجدول المراجعة',
                'type' => 'line',
                'labels' => $adherenceTrendLabels,
                'data' => $adherenceTrendData,
                'period' => 'أسبوعي',
            ],
            [
                'id' => 'srsStatusChart',
                'title' => '🎯 حالة النظام',
                'description' => 'توزيع المراجعات حسب الحالة',
                'type' => 'doughnut',
                'labels' => ['متأخرة', 'مجدولة اليوم', 'مكتملة اليوم'],
                'data' => [$srsOverdue, $srsToday, SpacedRepetition::whereDate('last_reviewed_at', $today)->count()],
                'colors' => ['#ef4444', '#f59e0b', '#10b981'],
                'period' => 'اليوم',
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

        // Leaderboard with period filter
        $lbPeriod = $request->input('lb_period', 'monthly');
        [$lbStart, $lbEnd] = $this->resolveLeaderboardPeriod($lbPeriod);
        $leaderboardQuery = Leaderboard::with('user')
            ->where('period_type', $lbPeriod)
            ->whereDate('period_start', $lbStart->toDateString())
            ->whereDate('period_end', $lbEnd->toDateString())
            ->orderBy('rank');
        $leaderboardTop = $leaderboardQuery->limit(5)->get();

        if ($leaderboardTop->isEmpty()) {
            $latestStart = Leaderboard::where('period_type', $lbPeriod)->max('period_start');
            if ($latestStart) {
                $leaderboardTop = Leaderboard::with('user')
                    ->where('period_type', $lbPeriod)
                    ->whereDate('period_start', Carbon::parse($latestStart)->toDateString())
                    ->orderBy('rank')
                    ->limit(5)
                    ->get();
            }
        }

        $leaderboardTop = $leaderboardTop->map(function ($r) {
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
        $recentReviews = ReviewRecord::with([
                'spacedRepetition.planItem.quranSurah', 
                'spacedRepetition.planItem.verseStart', 
                'spacedRepetition.planItem.verseEnd', 
                'spacedRepetition.planItem.memorizationPlan.user'
            ])
            ->orderByDesc('review_date')
            ->limit(12)
            ->get()
            ->map(function ($r) {
                $planItem = $r->spacedRepetition?->planItem;
                $plan = $planItem?->memorizationPlan;
                
                // إنشاء الوصف مع التحقق من وجود العلاقات
                $desc = '—';
                if ($planItem && $planItem->quranSurah && $planItem->verseStart && $planItem->verseEnd) {
                    $surahName = $planItem->quranSurah->name_ar;
                    $startVerseNumber = $planItem->verseStart->verse_number;
                    $endVerseNumber = $planItem->verseEnd->verse_number;
                    $desc = "{$surahName} ({$startVerseNumber}-{$endVerseNumber})";
                }
                
                return [
                    'date' => $r->review_date?->format('Y-m-d'),
                    'segment' => $desc,
                    'user' => $plan?->user?->name ?? '—',
                    'performance' => $r->performance_rating,
                    'successful' => $r->successful,
                ];
            });

        // Advanced Analytics Data
        $analytics = $this->getAdvancedAnalytics($start, $end, $prevStart, $prevEnd);

        return view('admin.dashboard', compact(
            'filters', 'filterOptions', 'kpis', 'charts', 'topStudents', 'atRiskStudents', 'recentReviews', 'leaderboardTop', 'lbPeriod', 'analytics'
        ));
    }

    public function exportReviews(Request $request)
    {
        $range = $request->input('range', 'last_30_days');
        [$start, $end] = $this->resolveRange($range);

        $rows = ReviewRecord::with([
                'spacedRepetition.planItem.quranSurah', 
                'spacedRepetition.planItem.verseStart', 
                'spacedRepetition.planItem.verseEnd', 
                'spacedRepetition.planItem.memorizationPlan.user'
            ])
            ->whereBetween('review_date', [$start, $end])
            ->orderByDesc('review_date')
            ->get()
            ->map(function ($r) {
                $planItem = $r->spacedRepetition?->planItem;
                $plan = $planItem?->memorizationPlan;
                
                // إنشاء الوصف مع التحقق من وجود العلاقات
                $desc = '—';
                if ($planItem && $planItem->quranSurah && $planItem->verseStart && $planItem->verseEnd) {
                    $surahName = $planItem->quranSurah->name_ar;
                    $startVerseNumber = $planItem->verseStart->verse_number;
                    $endVerseNumber = $planItem->verseEnd->verse_number;
                    $desc = "{$surahName} ({$startVerseNumber}-{$endVerseNumber})";
                }
                
                return [
                    $r->review_date?->format('Y-m-d H:i:s'),
                    $plan?->user?->name ?? '—',
                    $desc,
                    (string) $r->performance_rating,
                    $r->successful ? 'success' : 'fail',
                ];
            });

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['date', 'user', 'segment', 'rating', 'result']);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'recent-reviews.csv', $headers);
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

    private function resolveLeaderboardPeriod(string $period): array
    {
        $now = now();
        return match ($period) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yearly' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
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

    private function getAdvancedAnalytics(Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        // Most active hours analysis
        $hourlyActivity = ReviewRecord::selectRaw('EXTRACT(HOUR FROM review_date) as hour, COUNT(*) as count')
            ->whereBetween('review_date', [$start, $end])
            ->groupBy(DB::raw('EXTRACT(HOUR FROM review_date)'))
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        $mostActiveHour = collect($hourlyActivity)->sortDesc()->keys()->first();
        $mostActiveHourCount = $hourlyActivity[$mostActiveHour] ?? 0;

        // Weekly patterns
        $weeklyPattern = ReviewRecord::selectRaw('EXTRACT(DOW FROM review_date) as day, COUNT(*) as count')
            ->whereBetween('review_date', [$start, $end])
            ->groupBy(DB::raw('EXTRACT(DOW FROM review_date)'))
            ->get()
            ->pluck('count', 'day')
            ->toArray();

        $dayNames = [0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت'];
        $mostActiveDay = collect($weeklyPattern)->sortDesc()->keys()->first();
        $mostActiveDayName = $dayNames[$mostActiveDay] ?? 'غير محدد';

        // Performance trends
        $currentAvgPerformance = ReviewRecord::whereBetween('review_date', [$start, $end])->avg('performance_rating');
        $prevAvgPerformance = ReviewRecord::whereBetween('review_date', [$prevStart, $prevEnd])->avg('performance_rating');
        $performanceTrend = $prevAvgPerformance > 0 ? round((($currentAvgPerformance - $prevAvgPerformance) / $prevAvgPerformance) * 100, 1) : 0;

        // Success rate analysis
        $currentSuccessRate = ReviewRecord::whereBetween('review_date', [$start, $end])
            ->selectRaw('(SUM(CASE WHEN successful THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate')
            ->value('rate');
        $prevSuccessRate = ReviewRecord::whereBetween('review_date', [$prevStart, $prevEnd])
            ->selectRaw('(SUM(CASE WHEN successful THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate')
            ->value('rate');
        $successRateTrend = $prevSuccessRate > 0 ? round($currentSuccessRate - $prevSuccessRate, 1) : 0;

        // Chapter performance
        $chapterStats = MemorizationProgress::select('chapter_id', DB::raw('SUM(verses_memorized) as total_verses'))
            ->with('chapter')
            ->groupBy('chapter_id')
            ->orderByDesc('total_verses')
            ->limit(5)
            ->get();

        $topChapters = $chapterStats->map(function ($stat) {
            return [
                'name' => $stat->chapter?->name_ar ?? 'غير محدد',
                'verses' => (int) $stat->total_verses,
                'progress' => min(100, round(($stat->total_verses / ($stat->chapter?->verses_count ?? 1)) * 100, 1))
            ];
        });

        // Activity intensity
        $totalReviews = ReviewRecord::whereBetween('review_date', [$start, $end])->count();
        $totalDays = $start->diffInDays($end) + 1;
        $dailyAverage = round($totalReviews / $totalDays, 1);

        // Student engagement levels
        $activeStudentsCount = MemorizationPlan::where('status', 'active')->distinct('user_id')->count('user_id');
        $studentsWithRecentActivity = ReviewRecord::whereBetween('review_date', [$start, $end])
            ->join('spaced_repetitions', 'review_records.spaced_repetition_id', '=', 'spaced_repetitions.id')
            ->join('plan_items', 'spaced_repetitions.plan_item_id', '=', 'plan_items.id')
            ->join('memorization_plans', 'plan_items.plan_id', '=', 'memorization_plans.id')
            ->distinct('memorization_plans.user_id')
            ->count('memorization_plans.user_id');

        $engagementRate = $activeStudentsCount > 0 ? round(($studentsWithRecentActivity / $activeStudentsCount) * 100, 1) : 0;

        return [
            'mostActiveHour' => $mostActiveHour ?? 20,
            'mostActiveHourCount' => $mostActiveHourCount,
            'mostActiveDay' => $mostActiveDayName,
            'performanceTrend' => $performanceTrend,
            'successRateTrend' => $successRateTrend,
            'currentSuccessRate' => round($currentSuccessRate ?? 0, 1),
            'topChapters' => $topChapters,
            'dailyAverage' => $dailyAverage,
            'engagementRate' => $engagementRate,
            'totalReviews' => $totalReviews,
            'hourlyActivity' => $hourlyActivity,
            'weeklyPattern' => $weeklyPattern,
            'dayNames' => $dayNames
        ];
    }
}


