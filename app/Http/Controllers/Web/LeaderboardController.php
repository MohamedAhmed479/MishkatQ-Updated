<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\User;
use App\Repositories\Eloquent\PointsTransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    protected $pointsTransactionRepo;

    public function __construct(PointsTransactionRepository $pointsTransactionRepo)
    {
        $this->pointsTransactionRepo = $pointsTransactionRepo;
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'weekly');
        $user = $request->user();

        // Calculate current period bounds
        $now = Carbon::now();
        $periodStart = match ($period) {
            'daily' => $now->copy()->startOfDay(),
            'weekly' => $now->copy()->startOfWeek(),
            'monthly' => $now->copy()->startOfMonth(),
            'yearly' => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(),
        };

        // Try to get leaderboard from leaderboards table first
        $leaderboard = Leaderboard::with('user:id,name')
            ->where('period_type', $period)
            ->where('period_start', $periodStart->toDateString())
            ->orderBy('rank')
            ->take(50)
            ->get();

        // If no leaderboard data exists, calculate dynamically from points_transactions
        if ($leaderboard->isEmpty()) {
            $userPoints = $this->pointsTransactionRepo->getUsersTotalPointsBetweenDates($period);
            
            // Get all user IDs and load users in one query to avoid N+1
            $userIds = $userPoints->pluck('user_id')->toArray();
            $users = User::whereIn('id', $userIds)
                ->select('id', 'name')
                ->get()
                ->keyBy('id');
            
            $leaderboardData = [];
            $rank = 1;
            foreach ($userPoints as $userPoint) {
                $userData = $users->get($userPoint->user_id);
                if ($userData) {
                    $leaderboardData[] = [
                        'rank' => $rank++,
                        'user_name' => $userData->name,
                        'user_id' => $userPoint->user_id,
                        'total_points' => (int)$userPoint->total_points,
                        'is_current_user' => $userPoint->user_id === $user->id,
                    ];
                }
            }
            
            $leaderboard = collect($leaderboardData);
            
            // Find user's rank in dynamic leaderboard
            $userRankEntry = $leaderboard->firstWhere('user_id', $user->id);
            $userRank = $userRankEntry ? [
                'rank' => $userRankEntry['rank'],
                'total_points' => $userRankEntry['total_points'],
            ] : null;
        } else {
            // Get user's rank from leaderboard table
            $userRank = Leaderboard::where('period_type', $period)
                ->where('period_start', $periodStart->toDateString())
                ->where('user_id', $user->id)
                ->first();
            
            $leaderboard = $leaderboard->map(fn ($entry) => [
                'rank' => $entry->rank,
                'user_name' => $entry->user?->name ?? 'مستخدم',
                'user_id' => $entry->user_id,
                'total_points' => $entry->total_points,
                'is_current_user' => $entry->user_id === $user->id,
            ]);
            
            $userRank = $userRank ? [
                'rank' => $userRank->rank,
                'total_points' => $userRank->total_points,
            ] : null;
        }

        return Inertia::render('Leaderboard/Index', [
            'leaderboard' => $leaderboard->values()->all(),
            'userRank' => $userRank,
            'period' => $period,
        ]);
    }
}
