<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get analytics data
        $analytics = $this->analyticsService->getUserProgressAnalytics($user);

        return Inertia::render('Analytics/Index', [
            'analytics' => $analytics,
        ]);
    }
}
