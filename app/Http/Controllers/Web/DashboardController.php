<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\MemorizationPlanInterface;
use App\Repositories\Interfaces\SpacedRepetitionInterface;
use App\Services\AnalyticsService;
use App\Services\IncentiveService;
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
        $pendingRevisionsCount = $todayRevisions->where('status', 'pending')->count();
        
        // Get user stats
        $userStats = $this->getUserStats($user);
        
        // Get recent badges
        $recentBadges = $user->badges()->latest('pivot_created_at')->take(3)->get();
        
        // Get weekly activity
        $weeklyActivity = $this->getWeeklyActivity($user);
        
        return Inertia::render('Dashboard/Index', [
            'activePlan' => $activePlan ? [
                'id' => $activePlan->id,
                'name' => $activePlan->name,
                'start_chapter' => $firstItem?->quranSurah?->name_ar,
                'end_chapter' => $lastItem?->quranSurah?->name_ar,
                'progress' => $activePlan->progress_percentage ?? 0,
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
        ]);
    }
    
    private function getUserStats($user): array
    {
        // Calculate current streak
        $currentStreak = 0;
        $lastActive = $user->last_active_at;
        
        if ($lastActive) {
            $currentDate = now()->startOfDay();
            $checkDate = $lastActive->copy()->startOfDay();
            
            // Check if user was active today or yesterday
            $daysDiff = $currentDate->diffInDays($checkDate);
            
            if ($daysDiff <= 1) {
                $currentStreak = 1;
                $checkDate = $checkDate->subDay();
                
                // Count consecutive days
                while ($checkDate->lte(now()->startOfDay())) {
                    $hasActivity = false;
                    
                    // Check plan items completion
                    $hasActivity = $user->memorizationPlans()
                        ->whereHas('planItems', function ($query) use ($checkDate) {
                            $query->where('is_completed', true)
                                ->whereDate('updated_at', $checkDate->toDateString());
                        })
                        ->exists();
                    
                    // Check review records via spaced repetitions
                    if (!$hasActivity) {
                        $hasActivity = \App\Models\ReviewRecord::whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->whereDate('review_date', $checkDate->toDateString())
                        ->exists();
                    }
                    
                    if ($hasActivity) {
                        $currentStreak++;
                        $checkDate = $checkDate->subDay();
                    } else {
                        break;
                    }
                }
            }
        }
        
        return [
            'total_points' => $user->getTotalPoints() ?? 0,
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
