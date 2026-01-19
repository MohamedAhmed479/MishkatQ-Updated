<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\ReadingPlan;
use App\Models\ReadingProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReadingPlanService
{
    /**
     * Total pages in the Quran
     */
    const TOTAL_QURAN_PAGES = 604;

    /**
     * Common Hatmah targets (days to complete)
     */
    const HATMAH_TARGETS = [
        'weekly' => 7,      // ~86 pages/day
        'biweekly' => 14,   // ~43 pages/day
        'monthly' => 30,    // ~20 pages/day
        'quarterly' => 90,  // ~7 pages/day
        'yearly' => 365,    // ~2 pages/day
    ];

    /**
     * Create a new reading plan
     */
    public function createPlan(User $user, array $data): JsonResponse
    {
        // Check for existing active plan
        $existingPlan = $user->readingPlans()->active()->first();
        if ($existingPlan) {
            return ApiResponse::error('لديك خطة قراءة نشطة. يُرجى إيقافها أو إكمالها قبل البدء بخطة جديدة.', 400);
        }

        $planData = $this->preparePlanData($user, $data);
        
        $plan = ReadingPlan::create($planData);

        return ApiResponse::created([
            'plan' => $this->formatPlanResponse($plan),
        ], 'تم إنشاء خطة القراءة بنجاح');
    }

    /**
     * Prepare plan data from input
     */
    protected function preparePlanData(User $user, array $data): array
    {
        $type = $data['type'] ?? 'sequential';
        $startPage = $type === 'sequential' ? 1 : ($data['start_page'] ?? 1);
        $endPage = $type === 'sequential' ? self::TOTAL_QURAN_PAGES : ($data['end_page'] ?? self::TOTAL_QURAN_PAGES);
        
        $pagesPerDay = $data['pages_per_day'] ?? 1;
        $startDate = Carbon::parse($data['start_date'] ?? now());

        // Calculate end date if not provided
        $totalPages = $endPage - $startPage + 1;
        $daysRequired = (int) ceil($totalPages / $pagesPerDay);
        $endDate = isset($data['end_date']) 
            ? Carbon::parse($data['end_date']) 
            : (clone $startDate)->addDays($daysRequired - 1);

        // If end date is provided, recalculate pages per day
        if (isset($data['end_date']) && !isset($data['pages_per_day'])) {
            $daysBetween = $startDate->diffInDays($endDate) + 1;
            $pagesPerDay = (int) ceil($totalPages / $daysBetween);
        }

        // Default settings
        $settings = array_merge(ReadingPlan::DEFAULT_SETTINGS, $data['settings'] ?? []);

        return [
            'user_id' => $user->id,
            'name' => $data['name'] ?? 'خطة ختم القرآن',
            'description' => $data['description'] ?? null,
            'type' => $type,
            'target_type' => $data['target_type'] ?? 'pages',
            'pages_per_day' => $pagesPerDay,
            'current_page' => $startPage,
            'start_page' => $startPage,
            'end_page' => $endPage,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reading_mode' => $data['reading_mode'] ?? 'hadr',
            'status' => ReadingPlan::STATUS_ACTIVE,
            'settings' => $settings,
            'current_streak' => 0,
            'longest_streak' => 0,
            'hatmah_count' => 0,
        ];
    }

    /**
     * Get user's reading plans
     */
    public function getUserPlans(User $user, ?string $status = null): JsonResponse
    {
        $query = $user->readingPlans()->orderBy('created_at', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }

        $plans = $query->get()->map(fn($plan) => $this->formatPlanResponse($plan));

        return ApiResponse::success(['plans' => $plans]);
    }

    /**
     * Get plan details with today's wird
     */
    public function getPlanWithDailyWird(int $planId): JsonResponse
    {
        $plan = ReadingPlan::find($planId);
        
        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::notFound('خطة القراءة غير موجودة');
        }

        $dailyWird = $plan->getDailyWird();
        $todayProgress = $plan->todayProgress();

        return ApiResponse::success([
            'plan' => $this->formatPlanResponse($plan),
            'daily_wird' => $dailyWird,
            'today_progress' => $todayProgress,
            'streak_info' => [
                'current_streak' => $plan->current_streak,
                'longest_streak' => $plan->longest_streak,
                'has_read_today' => $plan->hasReadToday(),
                'is_streak_broken' => $plan->isStreakBroken(),
            ],
        ]);
    }

    /**
     * Mark reading progress
     */
    public function markProgress(int $planId, array $data): JsonResponse
    {
        $plan = ReadingPlan::find($planId);
        
        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::notFound('خطة القراءة غير موجودة');
        }

        if (!$plan->isActive()) {
            return ApiResponse::error('هذه الخطة غير نشطة', 400);
        }

        return DB::transaction(function () use ($plan, $data) {
            $startPage = $data['start_page'] ?? $plan->current_page;
            $endPage = $data['end_page'] ?? ($startPage + $plan->pages_per_day - 1);
            $endPage = min($endPage, $plan->end_page);
            
            $pagesReadThisCall = max(0, $endPage - $startPage + 1);

            // If there is already progress for today, accumulate pages instead of overwriting
            $existingProgress = ReadingProgress::where('reading_plan_id', $plan->id)
                ->whereDate('date', today())
                ->first();

            $totalPagesReadToday = $pagesReadThisCall;
            $combinedStartPage = $startPage;
            $combinedEndPage = $endPage;

            if ($existingProgress) {
                $totalPagesReadToday += (int) $existingProgress->pages_read;
                $combinedStartPage = min($existingProgress->start_page ?? $startPage, $startPage);
                $combinedEndPage = max($existingProgress->end_page ?? $endPage, $endPage);
            }

            $dailyTargetMet = $totalPagesReadToday >= $plan->pages_per_day;

            // Create or update today's progress (cumulative)
            $progress = ReadingProgress::updateOrCreate(
                [
                    'reading_plan_id' => $plan->id,
                    'date' => today(),
                ],
                [
                    'user_id' => $plan->user_id,
                    'pages_read' => $totalPagesReadToday,
                    'start_page' => $combinedStartPage,
                    'end_page' => $combinedEndPage,
                    'reading_mode' => $data['reading_mode'] ?? $plan->reading_mode,
                    'duration_minutes' => $data['duration_minutes'] ?? null,
                    'daily_target_met' => $dailyTargetMet,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            // Update plan's current page
            $newCurrentPage = $endPage + 1;
            $planUpdates = ['current_page' => $newCurrentPage];

            // Update streak
            $streakUpdates = $this->updateStreak($plan);
            $planUpdates = array_merge($planUpdates, $streakUpdates);

            // Check if plan is completed
            if ($newCurrentPage > $plan->end_page) {
                $planUpdates['status'] = ReadingPlan::STATUS_COMPLETED;
                $planUpdates['hatmah_count'] = $plan->hatmah_count + 1;
            }

            $plan->update($planUpdates);
            $plan->refresh();

            return ApiResponse::success([
                'progress' => $progress,
                'plan' => $this->formatPlanResponse($plan),
                'completion_message' => $plan->isCompleted() ? 'مبارك! لقد أتممت ختمة القرآن الكريم 🎉' : null,
            ], 'تم حفظ تقدمك بنجاح');
        });
    }

    /**
     * Update streak logic
     */
    protected function updateStreak(ReadingPlan $plan): array
    {
        $updates = ['last_reading_date' => today()];

        // If already read today, no streak update needed
        if ($plan->hasReadToday()) {
            return $updates;
        }

        // Check if streak continues (read yesterday or first day)
        if (!$plan->last_reading_date || $plan->last_reading_date->isYesterday()) {
            $newStreak = $plan->current_streak + 1;
            $updates['current_streak'] = $newStreak;
            
            if ($newStreak > $plan->longest_streak) {
                $updates['longest_streak'] = $newStreak;
            }
        } else if ($plan->isStreakBroken()) {
            // Streak broken - reset to 1
            $updates['current_streak'] = 1;
        }

        return $updates;
    }

    /**
     * Auto-adjust plan when user falls behind
     */
    public function autoAdjustPlan(int $planId): JsonResponse
    {
        $plan = ReadingPlan::find($planId);
        
        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::notFound('خطة القراءة غير موجودة');
        }

        if (!$plan->isActive()) {
            return ApiResponse::error('هذه الخطة غير نشطة', 400);
        }

        // Calculate new pages per day to meet original end date
        $remainingPages = $plan->getRemainingPages();
        $daysToEndDate = max(1, now()->startOfDay()->diffInDays($plan->end_date));
        
        $newPagesPerDay = (int) ceil($remainingPages / $daysToEndDate);

        // Don't allow unrealistic targets (more than 50 pages per day)
        if ($newPagesPerDay > 50) {
            // Extend end date instead
            $newPagesPerDay = min(10, max($plan->pages_per_day, 5)); // Moderate increase
            $newDaysNeeded = (int) ceil($remainingPages / $newPagesPerDay);
            $newEndDate = now()->addDays($newDaysNeeded);

            $plan->update([
                'pages_per_day' => $newPagesPerDay,
                'end_date' => $newEndDate,
            ]);

            return ApiResponse::success([
                'plan' => $this->formatPlanResponse($plan->refresh()),
                'adjustment_type' => 'extended',
                'message' => 'تم تمديد الخطة مع زيادة معتدلة في الورد اليومي',
            ]);
        }

        $plan->update(['pages_per_day' => $newPagesPerDay]);

        return ApiResponse::success([
            'plan' => $this->formatPlanResponse($plan->refresh()),
            'adjustment_type' => 'increased_daily',
            'new_pages_per_day' => $newPagesPerDay,
            'message' => "تم تعديل الورد اليومي إلى {$newPagesPerDay} صفحة للوصول للهدف في الموعد المحدد",
        ]);
    }

    /**
     * Pause a plan
     */
    public function pausePlan(int $planId): JsonResponse
    {
        $plan = ReadingPlan::find($planId);
        
        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::notFound('خطة القراءة غير موجودة');
        }

        if ($plan->status === ReadingPlan::STATUS_PAUSED) {
            return ApiResponse::error('الخطة متوقفة بالفعل', 400);
        }

        $plan->update(['status' => ReadingPlan::STATUS_PAUSED]);

        return ApiResponse::success([
            'plan' => $this->formatPlanResponse($plan->refresh()),
        ], 'تم إيقاف الخطة مؤقتاً');
    }

    /**
     * Resume a paused plan
     */
    public function resumePlan(int $planId): JsonResponse
    {
        $plan = ReadingPlan::find($planId);
        
        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::notFound('خطة القراءة غير موجودة');
        }

        if ($plan->status !== ReadingPlan::STATUS_PAUSED) {
            return ApiResponse::error('الخطة ليست متوقفة', 400);
        }

        // Recalculate end date based on remaining pages
        $remainingPages = $plan->getRemainingPages();
        $daysNeeded = (int) ceil($remainingPages / $plan->pages_per_day);
        $newEndDate = now()->addDays($daysNeeded);

        $plan->update([
            'status' => ReadingPlan::STATUS_ACTIVE,
            'end_date' => $newEndDate,
        ]);

        return ApiResponse::success([
            'plan' => $this->formatPlanResponse($plan->refresh()),
        ], 'تم استئناف الخطة بنجاح');
    }

    /**
     * Update plan settings
     */
    public function updateSettings(int $planId, array $settings): JsonResponse
    {
        $plan = ReadingPlan::find($planId);
        
        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::notFound('خطة القراءة غير موجودة');
        }

        // Separate reading_mode from settings (it's a direct column)
        $readingMode = $settings['reading_mode'] ?? null;
        unset($settings['reading_mode']);

        // Update settings JSON field
        $currentSettings = $plan->settings ?? [];
        $newSettings = array_merge($currentSettings, $settings);
        
        $updateData = ['settings' => $newSettings];
        
        // Update reading_mode if provided
        if ($readingMode !== null) {
            $updateData['reading_mode'] = $readingMode;
        }
        
        $plan->update($updateData);

        return ApiResponse::success([
            'settings' => $plan->refresh()->settings,
            'reading_mode' => $plan->reading_mode,
        ], 'تم حفظ الإعدادات');
    }

    /**
     * Get reading statistics
     */
    public function getStatistics(User $user): JsonResponse
    {
        $totalPlans = $user->readingPlans()->count();
        $completedPlans = $user->readingPlans()->where('status', ReadingPlan::STATUS_COMPLETED)->count();
        $totalHatmah = $user->readingPlans()->sum('hatmah_count');
        
        // Get streak from active plan
        $activePlan = $user->readingPlans()->active()->first();
        $currentStreak = $activePlan ? $activePlan->current_streak : 0;
        $longestStreak = $user->readingPlans()->max('longest_streak') ?? 0;

        // Reading history (last 30 days)
        $thirtyDaysAgo = now()->subDays(30);
        $readingHistory = $user->readingProgress()
            ->where('date', '>=', $thirtyDaysAgo)
            ->selectRaw('date, SUM(pages_read) as total_pages')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Pages read this week
        $weekStart = now()->startOfWeek();
        $pagesThisWeek = $user->readingProgress()
            ->where('date', '>=', $weekStart)
            ->sum('pages_read');

        // Pages read this month
        $monthStart = now()->startOfMonth();
        $pagesThisMonth = $user->readingProgress()
            ->where('date', '>=', $monthStart)
            ->sum('pages_read');

        return ApiResponse::success([
            'total_plans' => $totalPlans,
            'completed_plans' => $completedPlans,
            'total_hatmah' => $totalHatmah,
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
            'pages_this_week' => $pagesThisWeek,
            'pages_this_month' => $pagesThisMonth,
            'reading_history' => $readingHistory,
            'active_plan' => $activePlan ? $this->formatPlanResponse($activePlan) : null,
        ]);
    }

    /**
     * Format plan response
     */
    protected function formatPlanResponse(ReadingPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'description' => $plan->description,
            'type' => $plan->type,
            'target_type' => $plan->target_type,
            'pages_per_day' => $plan->pages_per_day,
            'current_page' => $plan->current_page,
            'start_page' => $plan->start_page,
            'end_page' => $plan->end_page,
            'start_date' => $plan->start_date->format('Y-m-d'),
            'end_date' => $plan->end_date?->format('Y-m-d'),
            'reading_mode' => $plan->reading_mode,
            'status' => $plan->status,
            'settings' => $plan->settings,
            'current_streak' => $plan->current_streak,
            'longest_streak' => $plan->longest_streak,
            'hatmah_count' => $plan->hatmah_count,
            'progress_percentage' => $plan->getProgressPercentage(),
            'total_pages' => $plan->getTotalPages(),
            'pages_read' => $plan->getPagesRead(),
            'remaining_pages' => $plan->getRemainingPages(),
            'days_remaining' => $plan->getDaysRemaining(),
            'estimated_completion' => $plan->getEstimatedCompletionDate()?->format('Y-m-d'),
            'has_read_today' => $plan->hasReadToday(),
            'created_at' => $plan->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get suggested plans (quick create options)
     */
    public function getSuggestedPlans(): JsonResponse
    {
        $suggestions = [
            [
                'name' => 'ختمة أسبوعية',
                'days' => 7,
                'pages_per_day' => (int) ceil(self::TOTAL_QURAN_PAGES / 7),
                'description' => 'ختم القرآن في أسبوع واحد',
                'difficulty' => 'صعب جداً',
            ],
            [
                'name' => 'ختمة شهرية',
                'days' => 30,
                'pages_per_day' => (int) ceil(self::TOTAL_QURAN_PAGES / 30),
                'description' => 'جزء يومياً تقريباً',
                'difficulty' => 'متوسط',
            ],
            [
                'name' => 'ختمة في شهرين',
                'days' => 60,
                'pages_per_day' => (int) ceil(self::TOTAL_QURAN_PAGES / 60),
                'description' => 'نصف جزء يومياً',
                'difficulty' => 'سهل',
            ],
            [
                'name' => 'صفحة يومياً',
                'days' => self::TOTAL_QURAN_PAGES,
                'pages_per_day' => 1,
                'description' => 'ختمة سنوية ونصف تقريباً',
                'difficulty' => 'يسير جداً',
            ],
        ];

        return ApiResponse::success(['suggestions' => $suggestions]);
    }

    /**
     * Start a new Hatmah (restart from beginning)
     */
    public function startNewHatmah(int $planId): JsonResponse
    {
        $plan = ReadingPlan::find($planId);
        
        if (!$plan || $plan->user_id !== Auth::id()) {
            return ApiResponse::notFound('خطة القراءة غير موجودة');
        }

        if (!$plan->isCompleted()) {
            return ApiResponse::error('لم تُتم الختمة الحالية بعد', 400);
        }

        // Reset for new Hatmah
        $plan->update([
            'current_page' => 1,
            'start_date' => today(),
            'end_date' => today()->addDays($plan->getDaysRemaining()),
            'status' => ReadingPlan::STATUS_ACTIVE,
            'current_streak' => 0,
        ]);

        return ApiResponse::success([
            'plan' => $this->formatPlanResponse($plan->refresh()),
        ], 'بارك الله فيك! تم بدء ختمة جديدة 🌙');
    }
}
