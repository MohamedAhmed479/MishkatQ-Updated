<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Services\AnalyticsService;
use App\Services\IncentiveService;
use App\Services\QuranService;
use App\Models\ReadingPlan;
use App\Models\ReviewRecord;
use App\Models\PlanItem;
use App\Models\Verse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private MemorizationPlanInterface $planRepository,
        private SpacedRepetitionInterface $spacedRepetitionRepository,
        private AnalyticsService $analyticsService,
        private IncentiveService $incentiveService,
        private QuranService $quranService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Eager load profile to avoid lazy loading issues
        $user->load('profile');
        
        // Get active plan
        $activePlan = $this->planRepository->findActivePlanForUser($user->id);
        
        // Load plan items with relationships if plan exists
        $firstItem = null;
        $lastItem = null;
        $todayItem = null;
        if ($activePlan) {
            $activePlan->load(['planItems.quranSurah']);
            $firstItem = $activePlan->planItems()->orderBy('sequence')->with('quranSurah')->first();
            $lastItem = $activePlan->planItems()->orderBy('sequence', 'desc')->with('quranSurah')->first();
            
            $todayItem = $activePlan->planItems()
                ->whereDate('target_date', now()->toDateString())
                ->where('is_completed', false)
                ->with(['quranSurah', 'verseStart', 'verseEnd'])
                ->first();
        }
        
        // Get today's revisions
        $todayRevisions = $this->spacedRepetitionRepository->getTodayRevisionsForUser($user->id) ?? collect();
        // Pending revisions are those where last_reviewed_at is null
        $pendingRevisionsCount = $todayRevisions->whereNull('last_reviewed_at')->count();
        
        // Get user stats
        $userStats = $this->getUserStats($user);
        
        // Get recent badges - order by pivot created_at timestamp
        $recentBadges = $user->badges()->orderByPivot('created_at', 'desc')->take(3)->get();
        
        // Get weekly activity
        $weeklyActivity = $this->getWeeklyActivity($user);
        
        // Reading plan summary (daily wird)
        $readingSummary = null;
        $activeReadingPlan = ReadingPlan::where('user_id', $user->id)
            ->where('status', ReadingPlan::STATUS_ACTIVE)
            ->first();

        if ($activeReadingPlan) {
            $dailyWird = $activeReadingPlan->getDailyWird();
            $todayReadingProgress = $activeReadingPlan->todayProgress();
            $startPage = $dailyWird['start_page'] ?? $activeReadingPlan->current_page;
            $endPage = $dailyWird['end_page'] ?? $startPage;
            $pagesCount = $dailyWird['pages_count'] ?? max(1, $endPage - $startPage + 1);

            $readingSummary = [
                'plan_id' => $activeReadingPlan->id,
                'name' => $activeReadingPlan->name,
                'start_page' => $startPage,
                'end_page' => $endPage,
                'pages_count' => $pagesCount,
                'has_read_today' => (bool) $todayReadingProgress,
            ];
        }
        
        // Calculate plan progress percentage dynamically
        $planProgress = 0;
        if ($activePlan) {
            $totalItems = $activePlan->planItems()->count();
            $completedItems = $activePlan->planItems()->where('is_completed', true)->count();
            $planProgress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
        }
        
        // Get Aya of the Day (cached for 24 hours to be consistent for all users on the same day)
        $ayaOfTheDay = $this->getAyaOfTheDay();
        
        return Inertia::render('Dashboard/Index', [
            'activePlan' => $activePlan ? [
                'id' => $activePlan->id,
                'name' => $activePlan->name,
                'start_chapter' => $firstItem?->quranSurah?->name_ar,
                'end_chapter' => $lastItem?->quranSurah?->name_ar,
                'progress' => $planProgress,
                'total_items' => $activePlan->planItems()->count(),
                'completed_items' => $activePlan->planItems()->where('is_completed', true)->count(),
            ] : null,
            'todayItem' => $todayItem ? [
                'id' => $todayItem->id,
                'chapter_name' => $todayItem->quranSurah?->name_ar,
                'start_verse' => $todayItem->verseStart?->verse_number,
                'end_verse' => $todayItem->verseEnd?->verse_number,
                'word_count' => $todayItem->getWordCount(),
                'verses_count' => $todayItem->getVersesCount(),
            ] : null,
            'pendingRevisionsCount' => $pendingRevisionsCount,
            'stats' => $userStats,
            'recentBadges' => $recentBadges->map(fn ($badge) => [
                'id' => $badge->id,
                'name' => $badge->name,
                'icon' => $badge->icon,
            ]),
            'weeklyActivity' => $weeklyActivity,
            'readingSummary' => $readingSummary,
            'ayaOfTheDay' => $ayaOfTheDay,
        ]);
    }
    
    private function getUserStats($user): array
    {
        // Use pre-calculated streak from user profile (optimized)
        $streakInfo = $user->getStreakInfo();
        $currentStreak = $streakInfo['current_streak'];
        
        // Check if streak needs to be reset (user missed a day)
        if ($user->profile && $user->profile->last_activity_date) {
            $lastActivity = Carbon::parse($user->profile->last_activity_date)->startOfDay();
            $today = now()->startOfDay();
            $daysDiff = $today->diffInDays($lastActivity);
            
            // If more than 1 day passed without activity, reset streak
            if ($daysDiff > 1) {
                $currentStreak = 0;
                $user->profile->update(['current_streak' => 0]);
            }
        }
        
        // Get total points from profile (already synced by IncentiveService)
        $totalPoints = $user->profile ? (int) ($user->profile->total_points ?? 0) : 0;
        
        return [
            'total_points' => $totalPoints,
            'current_streak' => $currentStreak,
            'best_streak' => $streakInfo['best_streak'],
            'total_verses_memorized' => $user->getTotalMemorizedVerses() ?? 0,
            'badges_count' => $user->badges()->count(),
        ];
    }

    private function getWeeklyActivity($user): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();
        
        // Get all plan item completions in the last 7 days (single query)
        $planCompletionDates = PlanItem::whereHas('memorizationPlan', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('is_completed', true)
        ->whereBetween('updated_at', [$startDate, $endDate])
        ->selectRaw('DATE(updated_at) as activity_date')
        ->distinct()
        ->pluck('activity_date')
        ->map(fn($date) => Carbon::parse($date)->toDateString())
        ->toArray();
        
        // Get all review dates in the last 7 days (single query)
        $reviewDates = ReviewRecord::whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereBetween('review_date', [$startDate, $endDate])
        ->selectRaw('DATE(review_date) as activity_date')
        ->distinct()
        ->pluck('activity_date')
        ->map(fn($date) => Carbon::parse($date)->toDateString())
        ->toArray();
        
        // Get reading progress dates (single query)
        $readingDates = $user->readingProgress()
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->toArray();
        
        // Merge all activity dates
        $activeDates = array_unique(array_merge($planCompletionDates, $reviewDates, $readingDates));
        
        // Build the weekly activity array
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->toDateString();
            $dayName = $date->locale('ar')->dayName;
            
            $days[] = [
                'date' => $dateString,
                'day' => $dayName,
                'active' => in_array($dateString, $activeDates),
            ];
        }
        
        return $days;
    }

    /**
     * Get Aya of the Day - consistent for all users on the same day
     */
    private function getAyaOfTheDay(): ?array
    {
        $cacheKey = 'aya_of_the_day_' . now()->toDateString();
        
        return Cache::remember($cacheKey, now()->endOfDay(), function () {
            // Use the day of year as a seed to get the same verse for everyone on the same day
            $dayOfYear = now()->dayOfYear;
            $totalVerses = Verse::count();
            
            if ($totalVerses === 0) {
                return null;
            }
            
            // Deterministic "random" verse based on day
            $verseIndex = ($dayOfYear * 17) % $totalVerses; // Use prime number for better distribution
            
            $verse = Verse::with('chapter')
                ->skip($verseIndex)
                ->first();
            
            if (!$verse) {
                // Fallback to a well-known verse (Ayat Al-Kursi)
                $verse = Verse::where('chapter_id', 2)->where('verse_number', 255)->with('chapter')->first();
            }
            
            if (!$verse) {
                return null;
            }
            
            return [
                'id' => $verse->id,
                'verse_key' => $verse->verse_key ?? "{$verse->chapter_id}:{$verse->verse_number}",
                'text' => $verse->text_uthmani,
                'chapter_name_ar' => $verse->chapter?->name_ar,
                'chapter_name_en' => $verse->chapter?->name_en,
                'verse_number' => $verse->verse_number,
                'chapter_id' => $verse->chapter_id,
            ];
        });
    }
}
