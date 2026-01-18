<?php

namespace App\Services;

use App\Models\MemorizationPlan;
use App\Models\PlanItem;
use App\Models\ReviewRecord;
use App\Models\SpacedRepetition;
use App\Models\User;
use App\Notifications\MemorizationPlanAdjustedNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\IncentiveService;
use App\Services\FSRSService;

class AutoAdjustMemorizationPlanService
{
    protected IncentiveService $incentiveService;
    protected FSRSService $fsrsService;

    public function __construct(
        IncentiveService $incentiveService,
        FSRSService $fsrsService
    ) {
        $this->incentiveService = $incentiveService;
        $this->fsrsService = $fsrsService;
    }

    /**
     * Evaluation period in days (period to analyze user performance)
     */
    const EVALUATION_PERIOD = 7;

    /**
     * Critical retrievability threshold - below this, memory is at risk
     */
    const CRITICAL_RETRIEVABILITY = 0.7;

    /**
     * Warning retrievability threshold - approaching memory decay
     */
    const WARNING_RETRIEVABILITY = 0.85;

    /**
     * Maximum items to review in recovery mode per day
     */
    const RECOVERY_MODE_DAILY_LIMIT = 10;

    /**
     * Thresholds for determining user performance status
     */
    const PERFORMANCE_THRESHOLDS = [
        'excellent' => [
            'success_rate' => 90, // >= 90%
            'avg_rating' => 4.5,  // >= 4.5
        ],
        'good' => [
            'success_rate' => 70, // >= 70%
            'avg_rating' => 3.5,  // >= 3.5
        ],
    ];

    /**
     * Maximum consecutive missed sessions that trigger recovery mode
     */
    const MAX_CONSECUTIVE_MISSED_SESSIONS = 3;


    /**
     * Run the auto-adjustment process for all active users
     * This method is intended to be called by a scheduled task
     */
    public function runAutoAdjustment(): void
    {
        Log::info('Starting auto-adjustment of memorization plans');

        $activePlans = MemorizationPlan::where('status', 'active')->get();

        foreach ($activePlans as $plan) {
            try {
                $this->adjustPlanForUser($plan);
            } catch (\Exception $e) {
                Log::error('Error adjusting plan ' . $plan->id . ': ' . $e->getMessage());
            }
        }

        Log::info('Completed auto-adjustment of memorization plans');
    }


    /**
     * Adjust the memorization plan for an individual user
     *
     * @param MemorizationPlan $plan
     * @return void
     */
    public function adjustPlanForUser(MemorizationPlan $plan): void
    {
        // Gather performance data for evaluation period
        $performanceData = $this->gatherPerformanceData($plan);

        // Determine user's performance status
        $status = $this->determinePerformanceStatus($performanceData);

        // Apply adjustments based on status
        $adjustmentsMade = $this->applyAdjustments($plan, $status);

        // Log the adjustments
        $this->logAdjustments($plan, $status, $performanceData, $adjustmentsMade);

        // Send notification to user if significant changes were made
        if ($adjustmentsMade) {
            $this->notifyUser($plan, $status);
        }
    }


    /**
     * Gather performance data for a user over the evaluation period
     *
     * @param MemorizationPlan $plan
     * @return array Performance metrics
     */
    private function gatherPerformanceData(MemorizationPlan $plan): array
    {
        $evaluationStartDate = Carbon::now()->subDays(self::EVALUATION_PERIOD);

        // Initialize data structure with all required keys
        $data = [
            'total_scheduled' => 0,
            'completed' => 0,
            'successful' => 0,
            'total_rating' => 0,
            'consecutive_missed' => $this->calculateConsecutiveMissedSessions($plan),
            'reviews' => [],
            'success_rate' => 0, // Ensure this key always exists
            'avg_rating' => 0,   // Ensure this key always exists
            'completion_rate' => 0, // Ensure this key always exists
        ];

        // Get all spaced repetitions for this plan that were scheduled within the evaluation period
        $spacedRepetitions = SpacedRepetition::whereHas('planItem', function ($query) use ($plan) {
            $query->where('plan_id', $plan->id);
        })
            ->where('scheduled_date', '>=', $evaluationStartDate)
            ->where('scheduled_date', '<=', Carbon::now())
            ->get();

        $data['total_scheduled'] = $spacedRepetitions->count();

        if ($data['total_scheduled'] === 0) {
            return $data; // Return initialized data structure
        }

        // Analyze review records
        foreach ($spacedRepetitions as $repetition) {
            $reviewRecord = ReviewRecord::where('spaced_repetition_id', $repetition->id)->latest()->first();

            if ($reviewRecord) {
                $data['completed']++;

                if ($reviewRecord->successful) {
                    $data['successful']++;
                }

                $data['total_rating'] += $reviewRecord->performance_rating;
                $data['reviews'][] = $reviewRecord;
            }
        }

        // Calculate derived metrics
        $data['success_rate'] = $data['completed'] > 0
            ? ($data['successful'] / $data['completed']) * 100
            : 0;

        $data['avg_rating'] = $data['completed'] > 0
            ? $data['total_rating'] / $data['completed']
            : 0;

        $data['completion_rate'] = $data['total_scheduled'] > 0
            ? ($data['completed'] / $data['total_scheduled']) * 100
            : 0;

        return $data;
    }


    /**
     * Calculate the number of consecutive missed sessions
     *
     * @param MemorizationPlan $plan
     * @return int
     */
    private function calculateConsecutiveMissedSessions(MemorizationPlan $plan): int
    {
        $consecutiveMissed = 0;
        $maxConsecutive = 0;
        $currentStreak = 0;

        // Get recent scheduled repetitions ordered by date
        $recentRepetitions = SpacedRepetition::whereHas('planItem', function ($query) use ($plan) {
            $query->where('plan_id', $plan->id);
        })
            ->where('scheduled_date', '<=', Carbon::now())
            ->orderBy('scheduled_date', 'desc')
            ->take(10) // Look at the 10 most recent sessions
            ->get()
            ->sortBy('scheduled_date'); // Sort by date ascending

        foreach ($recentRepetitions as $repetition) {
            $reviewRecord = ReviewRecord::where('spaced_repetition_id', $repetition->id)->exists();

            if (!$reviewRecord && $repetition->scheduled_date->isPast()) {
                $consecutiveMissed++;
                $maxConsecutive = max($maxConsecutive, $consecutiveMissed);
                $currentStreak = 0;
            } else {
                $consecutiveMissed = 0; // Reset counter on completed review
                $currentStreak++;
            }
        }

        // Award streak points if applicable
        if ($currentStreak > 0) {
            $this->incentiveService->awardStreakPoints($plan->user, $currentStreak);
        }

        return $maxConsecutive;
    }


    /**
     * Determine user's performance status based on metrics
     *
     * @param array $performanceData
     * @return string 'excellent', 'good', 'poor', or 'overdue'
     */
    private function determinePerformanceStatus(array $performanceData): string
    {
        // Check for consecutive missed sessions first
        if ($performanceData['consecutive_missed'] >= self::MAX_CONSECUTIVE_MISSED_SESSIONS) {
            return 'overdue';
        }

        // Check for excellent performance
        if (
            $performanceData['success_rate'] >= self::PERFORMANCE_THRESHOLDS['excellent']['success_rate'] &&
            $performanceData['avg_rating'] >= self::PERFORMANCE_THRESHOLDS['excellent']['avg_rating']
        ) {
            return 'excellent';
        }

        // Check for good performance
        if (
            $performanceData['success_rate'] >= self::PERFORMANCE_THRESHOLDS['good']['success_rate'] &&
            $performanceData['avg_rating'] >= self::PERFORMANCE_THRESHOLDS['good']['avg_rating']
        ) {
            return 'good';
        }

        // Default to poor if none of the above
        return 'poor';
    }


    /**
     * Apply adjustments to the plan based on performance status
     * Now includes FSRS-based recovery mode instead of just pausing
     *
     * @param MemorizationPlan $plan
     * @param string $status Performance status
     * @return bool Whether any adjustments were made
     */
    private function applyAdjustments(MemorizationPlan $plan, string $status): bool
    {
        $adjustmentsMade = false;

        // Check for critical items that need immediate attention
        $criticalItems = $this->getCriticalItems($plan);
        
        if ($criticalItems->count() > 0) {
            $this->enableRecoveryMode($plan, $criticalItems);
            $adjustmentsMade = true;
        }

        switch ($status) {
            case 'excellent':
                // For excellent performance, we can slightly accelerate the plan
                // by adjusting future memorization items closer
                break;

            case 'good':
                // Maintain current pace
                break;

            case 'poor':
                // Enable gentle recovery - prioritize weak items
                $this->prioritizeWeakItems($plan);
                $adjustmentsMade = true;
                break;

            case 'overdue':
                // Instead of pausing, enable recovery mode
                $this->enableRecoveryMode($plan, $criticalItems);
                $adjustmentsMade = true;
                break;
        }

        return $adjustmentsMade;
    }

    /**
     * Get items with critically low retrievability using FSRS
     */
    private function getCriticalItems(MemorizationPlan $plan): \Illuminate\Support\Collection
    {
        $revisions = SpacedRepetition::whereHas('planItem', function ($query) use ($plan) {
            $query->where('plan_id', $plan->id);
        })
            ->with(['planItem.quranSurah', 'planItem.verseStart', 'planItem.verseEnd'])
            ->get();

        return $revisions->map(function ($revision) {
            $stability = $revision->stability ?? 1.0;
            $daysElapsed = $revision->last_reviewed_at 
                ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                : 0;

            $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);

            $revision->current_retrievability = $retrievability;
            $revision->priority_score = $this->fsrsService->calculatePriorityScore(
                $retrievability,
                $stability,
                max(0, Carbon::today()->diffInDays($revision->scheduled_date, false))
            );

            return $revision;
        })
            ->filter(function ($revision) {
                return $revision->current_retrievability < self::CRITICAL_RETRIEVABILITY;
            })
            ->sortByDesc('priority_score');
    }

    /**
     * Enable recovery mode for a plan
     * Creates a focused review schedule for the most critical items
     */
    private function enableRecoveryMode(MemorizationPlan $plan, \Illuminate\Support\Collection $criticalItems): void
    {
        Log::info("Enabling recovery mode for plan {$plan->id} with {$criticalItems->count()} critical items");

        // Mark plan as in recovery mode
        $plan->update([
            'status' => 'recovery',
            'recovery_started_at' => Carbon::now(),
        ]);

        // Reschedule critical items for immediate review, spread across days
        $itemsPerDay = min(self::RECOVERY_MODE_DAILY_LIMIT, ceil($criticalItems->count() / 7));
        $currentDate = Carbon::today();
        $dayOffset = 0;

        foreach ($criticalItems->take(self::RECOVERY_MODE_DAILY_LIMIT * 7) as $index => $revision) {
            if ($index > 0 && $index % $itemsPerDay === 0) {
                $dayOffset++;
            }

            $newScheduledDate = (clone $currentDate)->addDays($dayOffset);

            $revision->update([
                'scheduled_date' => $newScheduledDate,
                'is_recovery_item' => true,
            ]);
        }

        // Temporarily pause new memorization items
        $this->pauseNewMemorization($plan);
    }

    /**
     * Pause new memorization items during recovery mode
     */
    private function pauseNewMemorization(MemorizationPlan $plan): void
    {
        // Postpone any upcoming memorization items by the recovery period
        PlanItem::where('plan_id', $plan->id)
            ->where('is_completed', false)
            ->where('target_date', '>=', Carbon::today())
            ->update([
                'target_date' => DB::raw('DATE_ADD(target_date, INTERVAL 7 DAY)'),
            ]);
    }

    /**
     * Prioritize weak items by adjusting their schedule
     */
    private function prioritizeWeakItems(MemorizationPlan $plan): void
    {
        $revisions = SpacedRepetition::whereHas('planItem', function ($query) use ($plan) {
            $query->where('plan_id', $plan->id);
        })
            ->whereNull('last_reviewed_at')
            ->get();

        foreach ($revisions as $revision) {
            $stability = $revision->stability ?? 1.0;
            
            // If stability is low, schedule sooner
            if ($stability < 21) {
                $daysElapsed = $revision->last_reviewed_at 
                    ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                    : 0;
                
                $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);
                
                if ($retrievability < self::WARNING_RETRIEVABILITY) {
                    // Reschedule to today or tomorrow
                    $revision->update([
                        'scheduled_date' => Carbon::today()->addDay(),
                    ]);
                }
            }
        }
    }

    /**
     * Get items that are approaching memory decay (for predictive notifications)
     */
    public function getItemsApproachingDecay(int $userId): \Illuminate\Support\Collection
    {
        $revisions = SpacedRepetition::whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereNotNull('last_reviewed_at')
            ->get();

        return $revisions->map(function ($revision) {
            $stability = $revision->stability ?? 1.0;
            $daysElapsed = $revision->last_reviewed_at 
                ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                : 0;

            $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);

            // Calculate days until retrievability drops below warning threshold
            $daysUntilDecay = $this->calculateDaysUntilDecay($stability, $daysElapsed);

            $revision->current_retrievability = $retrievability;
            $revision->days_until_decay = $daysUntilDecay;

            return $revision;
        })
            ->filter(function ($revision) {
                // Return items that will decay within the next 3 days
                return $revision->days_until_decay <= 3 && $revision->days_until_decay >= 0;
            })
            ->sortBy('days_until_decay');
    }

    /**
     * Calculate days until retrievability drops below warning threshold
     */
    private function calculateDaysUntilDecay(float $stability, int $daysElapsed): int
    {
        // Binary search for the day when R drops below threshold
        $targetR = self::WARNING_RETRIEVABILITY;
        $low = $daysElapsed;
        $high = $daysElapsed + 365;

        while ($low < $high) {
            $mid = (int) (($low + $high) / 2);
            $r = $this->fsrsService->calculateRetrievability($stability, $mid);
            
            if ($r > $targetR) {
                $low = $mid + 1;
            } else {
                $high = $mid;
            }
        }

        return max(0, $low - $daysElapsed);
    }

    /**
     * Send predictive notifications for items about to decay
     */
    public function sendPredictiveNotifications(): void
    {
        $users = User::whereHas('memorizationPlans', function ($query) {
            $query->where('status', 'active');
        })->get();

        foreach ($users as $user) {
            $decayingItems = $this->getItemsApproachingDecay($user->id);
            
            if ($decayingItems->count() > 0) {
                $this->sendDecayWarningNotification($user, $decayingItems);
            }
        }
    }

    /**
     * Send decay warning notification to user
     */
    private function sendDecayWarningNotification(User $user, \Illuminate\Support\Collection $decayingItems): void
    {
        $itemCount = $decayingItems->count();
        $firstItem = $decayingItems->first();
        
        $surahName = $firstItem->planItem?->quranSurah?->name_ar ?? 'غير معروف';

        $notificationData = [
            'type' => 'memory_decay_warning',
            'message' => $itemCount === 1
                ? "ذاكرتك لـ {$surahName} بدأت تضعف. راجع اليوم للحفاظ عليها!"
                : "لديك {$itemCount} أجزاء تحتاج مراجعة قبل أن تُنسى. راجع اليوم!",
            'item_count' => $itemCount,
            'priority' => 'high',
        ];

        $user->notify(new MemorizationPlanAdjustedNotification($notificationData));

        Log::info("Sent decay warning to user {$user->id} for {$itemCount} items");
    }

    /**
     * Check if a plan should exit recovery mode
     */
    public function checkRecoveryModeCompletion(MemorizationPlan $plan): bool
    {
        if ($plan->status !== 'recovery') {
            return false;
        }

        // Check if critical items have been reviewed
        $criticalItems = $this->getCriticalItems($plan);
        
        if ($criticalItems->count() === 0) {
            // All critical items addressed, exit recovery mode
            $plan->update([
                'status' => 'active',
                'recovery_started_at' => null,
            ]);

            $this->notifyUser($plan, 'recovery_complete');
            
            Log::info("Plan {$plan->id} exited recovery mode successfully");
            
            return true;
        }

        // Check if recovery has been going on too long (more than 14 days)
        if ($plan->recovery_started_at && Carbon::parse($plan->recovery_started_at)->diffInDays(Carbon::now()) > 14) {
            // Offer to reset or continue
            $this->notifyUser($plan, 'recovery_extended');
        }

        return false;
    }

    /**
     * Get recovery mode progress for a plan
     */
    public function getRecoveryProgress(MemorizationPlan $plan): array
    {
        if ($plan->status !== 'recovery') {
            return [
                'in_recovery' => false,
            ];
        }

        $criticalItems = $this->getCriticalItems($plan);
        $totalItems = SpacedRepetition::whereHas('planItem', function ($query) use ($plan) {
            $query->where('plan_id', $plan->id);
        })->count();

        $recoveredItems = $totalItems - $criticalItems->count();
        $progress = $totalItems > 0 ? ($recoveredItems / $totalItems) * 100 : 0;

        return [
            'in_recovery' => true,
            'started_at' => $plan->recovery_started_at,
            'days_in_recovery' => $plan->recovery_started_at 
                ? Carbon::parse($plan->recovery_started_at)->diffInDays(Carbon::now()) 
                : 0,
            'critical_items_remaining' => $criticalItems->count(),
            'total_items' => $totalItems,
            'progress_percentage' => round($progress, 1),
            'estimated_days_remaining' => ceil($criticalItems->count() / self::RECOVERY_MODE_DAILY_LIMIT),
        ];
    }


    /**
     * Pause the memorization plan if the number of uncompleted sessions
     * within the evaluation period exceeds the allowed limit.
     *
     * @param MemorizationPlan $plan The memorization plan to check.
     * @return bool|null Returns true if the plan was paused, false if pausing failed, or null if no action was needed.
     */
    private function pausePlanIfOverdueSessionsExist(MemorizationPlan $plan)
    {
        // Define the start of the evaluation period (e.g., last 7 days)
        $evaluationStartDate = Carbon::now()->subDays(self::EVALUATION_PERIOD);

        // Count uncompleted sessions within the evaluation period
        $countSessionsNotCompleted = $plan->planItems()
            ->where('target_date', '>=', $evaluationStartDate)
            ->where('target_date', '<=', Carbon::now())
            ->where("is_completed", 0)
            ->count();

        // If missed sessions exceed the threshold, pause the plan
        if ($countSessionsNotCompleted > self::MAX_CONSECUTIVE_MISSED_SESSIONS) {
            return $this->pausePlan($plan) ? true : false;
        }

        // No action needed
        return null;
    }


    /**
     * Pause the plan for users with many consecutive missed sessions
     *
     * @param MemorizationPlan $plan
     * @return bool Whether changes were made
     */
    private function pausePlan(MemorizationPlan $plan): bool
    {
        // Update plan status to paused
        $plan->status = 'paused';
        $plan->save();

        return true;
    }


    /**
     * Log adjustments made to the plan
     *
     * @param MemorizationPlan $plan
     * @param string $status
     * @param array $performanceData
     * @param bool $adjustmentsMade
     * @return void
     */
    private function logAdjustments(MemorizationPlan $plan, string $status, array $performanceData, bool $adjustmentsMade): void
    {
        $logData = [
            'plan_id' => $plan->id,
            'user_id' => $plan->user_id,
            'status' => $status,
            'performance_data' => [
                'success_rate' => $performanceData['success_rate'],
                'avg_rating' => $performanceData['avg_rating'],
                'completion_rate' => $performanceData['completion_rate'],
                'consecutive_missed' => $performanceData['consecutive_missed'],
            ],
            'adjustments_made' => $adjustmentsMade,
        ];

        Log::info('Plan auto-adjustment', $logData);
    }


    /**
     * Notify the user about plan adjustments
     *
     * @param MemorizationPlan $plan
     * @param string $status
     * @return void
     */
    private function notifyUser(MemorizationPlan $plan, string $status): void
    {
        $user = User::find($plan->user_id);

        if (!$user) {
            Log::error('User not found for plan ' . $plan->id);
            return;
        }

        $notificationData = [
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'status' => $status,
            'message' => $this->getNotificationMessage($status, $plan),
        ];

        $user->notify(new MemorizationPlanAdjustedNotification($notificationData));
    }


    /**
     * Get notification message based on status
     *
     * @param string $status
     * @param MemorizationPlan $plan
     * @return string
     */
    private function getNotificationMessage(string $status, MemorizationPlan $plan): string
    {
        switch ($status) {
            case 'excellent':
                return 'نظرًا لأدائك الممتاز، تم تسريع خطة الحفظ الخاصة بك لتتيح لك التقدم بوتيرة أسرع.';

            case 'poor':
                return 'تم تعديل خطة الحفظ الخاصة بك لتوفير المزيد من الوقت للمراجعة وتثبيت المحفوظ.';

            case 'overdue':
                return 'تم تفعيل وضع الاسترداد لخطتك. سنركز على تثبيت ما حفظته قبل المتابعة. لا تقلق، سنساعدك على العودة للمسار!';

            case 'recovery':
                return 'تم تفعيل وضع الاسترداد الذكي. سنركز على المراجعات الأهم أولاً لحماية حفظك.';

            case 'recovery_complete':
                return 'أحسنت! أكملت وضع الاسترداد بنجاح. يمكنك الآن متابعة خطة الحفظ الجديدة.';

            case 'recovery_extended':
                return 'لا يزال لديك بعض المراجعات المتأخرة. خذ وقتك، والأهم هو الاستمرار ولو بمراجعة واحدة يومياً.';

            default:
                return 'تم تحديث خطة الحفظ الخاصة بك بناءً على أدائك الأخير.';
        }
    }

    /**
     * Run recovery check for all plans in recovery mode
     */
    public function runRecoveryChecks(): void
    {
        Log::info('Running recovery mode checks');

        $recoveryPlans = MemorizationPlan::where('status', 'recovery')->get();

        foreach ($recoveryPlans as $plan) {
            try {
                $this->checkRecoveryModeCompletion($plan);
            } catch (\Exception $e) {
                Log::error('Error checking recovery for plan ' . $plan->id . ': ' . $e->getMessage());
            }
        }

        Log::info('Completed recovery mode checks');
    }

    /**
     * Get a summary of memory health for a user
     */
    public function getMemoryHealthSummary(int $userId): array
    {
        $revisions = SpacedRepetition::whereHas('planItem.memorizationPlan', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        if ($revisions->isEmpty()) {
            return [
                'total_items' => 0,
                'health_score' => 0,
                'status' => 'no_data',
                'status_ar' => 'لا توجد بيانات',
            ];
        }

        $totalRetrievability = 0;
        $criticalCount = 0;
        $warningCount = 0;
        $healthyCount = 0;

        foreach ($revisions as $revision) {
            $stability = $revision->stability ?? 1.0;
            $daysElapsed = $revision->last_reviewed_at 
                ? Carbon::now()->diffInDays($revision->last_reviewed_at) 
                : 0;

            $retrievability = $this->fsrsService->calculateRetrievability($stability, $daysElapsed);
            $totalRetrievability += $retrievability;

            if ($retrievability < self::CRITICAL_RETRIEVABILITY) {
                $criticalCount++;
            } elseif ($retrievability < self::WARNING_RETRIEVABILITY) {
                $warningCount++;
            } else {
                $healthyCount++;
            }
        }

        $count = $revisions->count();
        $avgRetrievability = $totalRetrievability / $count;
        $healthScore = round($avgRetrievability * 100, 1);

        // Determine overall status
        $status = 'healthy';
        $statusAr = 'ممتاز';

        if ($criticalCount > $count * 0.3) {
            $status = 'critical';
            $statusAr = 'يحتاج اهتمام';
        } elseif ($warningCount > $count * 0.3) {
            $status = 'warning';
            $statusAr = 'جيد';
        }

        return [
            'total_items' => $count,
            'health_score' => $healthScore,
            'status' => $status,
            'status_ar' => $statusAr,
            'critical_count' => $criticalCount,
            'warning_count' => $warningCount,
            'healthy_count' => $healthyCount,
            'average_retrievability' => round($avgRetrievability, 4),
        ];
    }
}
