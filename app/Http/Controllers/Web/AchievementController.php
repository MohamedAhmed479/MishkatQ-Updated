<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Services\IncentiveService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AchievementController extends Controller
{
    public function __construct(
        private IncentiveService $incentiveService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get all badges
        $allBadges = Badge::where('is_active', true)->get();
        $userBadges = $user->badges()->pluck('badges.id')->toArray();
        
        // Get user stats
        $stats = $this->getUserStats($user);
        
        // Get points history
        $pointsHistory = $user->pointsTransactions()
            ->latest()
            ->take(10)
            ->get(['id', 'points', 'description', 'activity_type', 'created_at']);

        return Inertia::render('Achievements/Index', [
            'badges' => $allBadges->map(fn ($badge) => [
                'id' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'points' => $badge->points_awarded,
                'earned' => in_array($badge->id, $userBadges),
            ]),
            'stats' => [
                'total_points' => $stats['total_points'] ?? 0,
                'current_streak' => $stats['current_streak'] ?? 0,
                'total_verses_memorized' => $stats['total_verses_memorized'] ?? 0,
                'badges_count' => count($userBadges),
                'total_badges' => $allBadges->count(),
            ],
            'pointsHistory' => $pointsHistory->map(fn ($tx) => [
                'id' => $tx->id,
                'points' => $tx->points,
                'reason' => $tx->description,
                'activity_type' => $tx->activity_type,
                'date' => $tx->created_at->diffForHumans(),
            ]),
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
            
            $daysDiff = $currentDate->diffInDays($checkDate);
            
            if ($daysDiff <= 1) {
                $currentStreak = 1;
                $checkDate = $checkDate->subDay();
                
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
        ];
    }
}
