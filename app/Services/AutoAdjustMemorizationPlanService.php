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

class AutoAdjustMemorizationPlanService
{
    protected $incentiveService;

    public function __construct(IncentiveService $incentiveService)
    {
        $this->incentiveService = $incentiveService;
    }

    /**
     * Evaluation period in days (period to analyze user performance)
     */
    const EVALUATION_PERIOD = 7;


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
     * Maximum consecutive missed sessions that trigger plan pausing
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
     *
     * @param MemorizationPlan $plan
     * @param string $status Performance status
     * @param array $performanceData
     * @return bool Whether any adjustments were made
     */
    private function applyAdjustments(MemorizationPlan $plan, string $status): bool
    {
        $adjustmentsMade = false;

        $adjustmentsMade = $this->pausePlanIfOverdueSessionsExist($plan);
        if ($adjustmentsMade) {
            return true;
        } else {
            $adjustmentsMade = false;
        }

        switch ($status) {
            case 'excellent':
                // No changes needed for good performance
                break;

            case 'good':
                // No changes needed for good performance
                break;

            case 'poor':
                // No changes needed for good performance
                break;

            case 'overdue':
                $adjustmentsMade = $this->pausePlan($plan);
                break;
        }

        return $adjustmentsMade;
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
                // $resumeUrl = url('/memorization-plans/' . $plan->id . '/active/');
                return 'تم إيقاف خطة الحفظ الخاصة بك مؤقتًا نظرًا لعدم اكتمال المراجعات الأخيرة. يمكنك استئنافها عندما تكون مستعدًا.';

            default:
                return 'تم تحديث خطة الحفظ الخاصة بك بناءً على أدائك الأخير.';
        }
    }
}
