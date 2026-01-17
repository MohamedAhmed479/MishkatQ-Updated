<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'weekly');
        $user = $request->user();

        // Get leaderboard entries
        $leaderboard = Leaderboard::with('user:id,name')
            ->where('period_type', $period)
            ->orderBy('rank')
            ->take(50)
            ->get();

        // Get user's rank
        $userRank = Leaderboard::where('period_type', $period)
            ->where('user_id', $user->id)
            ->first();

        return Inertia::render('Leaderboard/Index', [
            'leaderboard' => $leaderboard->map(fn ($entry) => [
                'rank' => $entry->rank,
                'user_name' => $entry->user?->name ?? 'مستخدم',
                'user_id' => $entry->user_id,
                'total_points' => $entry->total_points,
                'is_current_user' => $entry->user_id === $user->id,
            ]),
            'userRank' => $userRank ? [
                'rank' => $userRank->rank,
                'total_points' => $userRank->total_points,
            ] : null,
            'period' => $period,
        ]);
    }
}
