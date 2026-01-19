<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Services\AnalyticsService;
use App\Services\IncentiveService;
use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private MemorizationPlanInterface $planRepository,
        private SpacedRepetitionInterface $spacedRepetitionRepository,
        private AnalyticsService $analyticsService,
        private IncentiveService $incentiveService
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
                'word_count' => 0, // Will be calculated on frontend or can be calculated here if needed
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
        ]);
    }
    
    private function getUserStats($user): array
    {
        // Calculate current streak - simple: count consecutive days with activity from most recent
        $currentStreak = 0;
        $lastActiveDate = null;
        
        // Find the most recent day with activity (today or before)
        for ($i = 0; $i < 365; $i++) {
            $dateToCheck = now()->startOfDay()->subDays($i);
            $hasActivity = false;
            
            // Check plan items completion
            $hasActivity = $user->memorizationPlans()
                ->whereHas('planItems', function ($query) use ($dateToCheck) {
                    $query->where('is_completed', true)
                        ->whereDate('updated_at', $dateToCheck->toDateString());
                })
                ->exists();
            
            // Check review records via spaced repetitions
            if (!$hasActivity) {
                $hasActivity = \App\Models\ReviewRecord::whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereDate('review_date', $dateToCheck->toDateString())
                ->exists();
            }
            
            if ($hasActivity) {
                if ($lastActiveDate === null) {
                    $lastActiveDate = $dateToCheck; // First active day found (most recent)
                }
                $currentStreak++;
            } else {
                // If we've started counting (found an active day), and now found a gap, stop
                if ($lastActiveDate !== null) {
                    break;
                }
            }
        }
        
        // Get total points - always calculate from transactions first as source of truth
        // Then sync with profile if needed
        $totalPointsFromTransactions = (int) $user->pointsTransactions()->sum('points');
        
        // Use profile total_points if it exists and matches or is close
        // Otherwise, use transactions sum and update profile
        if ($user->profile) {
            $profilePoints = (int) ($user->profile->total_points ?? 0);
            
            // If transactions show points but profile doesn't, update profile
            if ($totalPointsFromTransactions > 0 && $profilePoints == 0) {
                $user->profile->update(['total_points' => $totalPointsFromTransactions]);
                $totalPoints = $totalPointsFromTransactions;
            } elseif ($profilePoints > 0) {
                // Use profile if it has points (it should be synced, but prefer transactions if they differ significantly)
                $totalPoints = max($profilePoints, $totalPointsFromTransactions);
                
                // If transactions are higher, update profile
                if ($totalPointsFromTransactions > $profilePoints) {
                    $user->profile->update(['total_points' => $totalPointsFromTransactions]);
                    $totalPoints = $totalPointsFromTransactions;
                }
            } else {
                $totalPoints = $totalPointsFromTransactions;
            }
        } else {
            // No profile exists, use transactions
            $totalPoints = $totalPointsFromTransactions;
        }
        
        return [
            'total_points' => $totalPoints,
            'current_streak' => $currentStreak,
            'total_verses_memorized' => $user->getTotalMemorizedVerses() ?? 0,
            'badges_count' => $user->badges()->count(),
        ];
    }

    private function getWeeklyActivity($user): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->locale('ar')->dayName;
            
            // Check if user had any activity on this day
            $hasActivity = false;
            
            // Check plan items completion
            $hasActivity = $user->memorizationPlans()
                ->whereHas('planItems', function ($query) use ($date) {
                    $query->where('is_completed', true)
                        ->whereDate('updated_at', $date->toDateString());
                })
                ->exists();
            
            // Check review records via spaced repetitions
            if (!$hasActivity) {
                $hasActivity = \App\Models\ReviewRecord::whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereDate('review_date', $date->toDateString())
                ->exists();
            }
            
            $days[] = [
                'date' => $date->toDateString(),
                'day' => $dayName,
                'active' => $hasActivity,
            ];
        }
        
        return $days;
    }
}
