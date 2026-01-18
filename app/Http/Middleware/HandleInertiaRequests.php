<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user('web');
        
        // Add stats to user object for sidebar without replacing the model
        if ($user) {
            $user->load('profile');
            
            // Calculate total points
            $totalPoints = 0;
            if ($user->profile && $user->profile->total_points) {
                $totalPoints = (int) $user->profile->total_points;
            } else {
                // Fallback: calculate from transactions
                $totalPoints = (int) $user->pointsTransactions()->sum('points');
                if ($totalPoints > 0 && $user->profile) {
                    $user->profile->update(['total_points' => $totalPoints]);
                }
            }
            
            // Add computed properties to user object
            $user->total_points = $totalPoints;
            $user->total_verses_memorized = $user->getTotalMemorizedVerses() ?? 0;
            
            // Calculate streak (simplified for sidebar - cache to avoid performance issues)
            $currentStreak = 0;
            for ($i = 0; $i < 365; $i++) {
                $dateToCheck = now()->startOfDay()->subDays($i);
                $hasActivity = false;
                
                $hasActivity = $user->memorizationPlans()
                    ->whereHas('planItems', function ($query) use ($dateToCheck) {
                        $query->where('is_completed', true)
                            ->whereDate('updated_at', $dateToCheck->toDateString());
                    })
                    ->exists();
                
                if (!$hasActivity) {
                    $hasActivity = \App\Models\ReviewRecord::whereHas('spacedRepetition.planItem.memorizationPlan', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->whereDate('review_date', $dateToCheck->toDateString())
                    ->exists();
                }
                
                if ($hasActivity) {
                    $currentStreak++;
                } else {
                    if ($currentStreak > 0) break;
                }
            }
            $user->current_streak = $currentStreak;
        }
        
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_points' => $user->total_points ?? 0,
                    'total_verses_memorized' => $user->total_verses_memorized ?? 0,
                    'current_streak' => $user->current_streak ?? 0,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
