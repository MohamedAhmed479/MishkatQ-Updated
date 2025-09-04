<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SpacedRepetitionService;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReviewReminder;
use Carbon\Carbon;

class ProcessDailyReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:process-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process daily review schedule and send notifications';

    /**
     * The spaced repetition service.
     *
     * @var SpacedRepetitionService
     */
    protected $spacedRepetitionService;

    /**
     * The audit service.
     *
     * @var AuditService
     */
    protected $auditService;

    /**
     * Create a new command instance.
     *
     * @param SpacedRepetitionService $spacedRepetitionService
     * @param AuditService $auditService
     * @return void
     */
    public function __construct(SpacedRepetitionService $spacedRepetitionService, AuditService $auditService)
    {
        parent::__construct();
        $this->spacedRepetitionService = $spacedRepetitionService;
        $this->auditService = $auditService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting daily review process...');

        // تسجيل بداية المهمة
        $this->auditService->log(
            action: 'scheduled_task_started',
            description: 'بدء عملية معالجة المراجعات اليومية',
            severity: 'low',
            category: 'scheduled_task',
            metadata: [
                'command' => 'reviews:process-daily',
                'started_at' => Carbon::now()->toISOString()
            ]
        );

        try {
            // الحصول على جميع المستخدمين النشطين
            // $users = User::where('last_active_at', '>=', Carbon::now()->subMinutes(2))->get();
            $users = User::all();
            $today = Carbon::today()->format('Y-m-d');

            $totalNotificationsSent = 0;
            $totalUsersProcessed = 0;
            $errors = [];

            foreach ($users as $user) {
                try {
                    $totalUsersProcessed++;
                    
                    // الحصول على مراجعات اليوم للمستخدم
                    $todayReviews = $this->spacedRepetitionService->getTodayReviewsForUser($user->id);

                    $reviewCount = $todayReviews->count();

                    if ($reviewCount > 0) {
                        // إرسال إشعار للمستخدم
                        Notification::send($user, new ReviewReminder($reviewCount, $today));

                        $totalNotificationsSent++;

                        $this->info("Sent notification to user #{$user->id} for {$reviewCount} reviews");
                    }
                } catch (\Exception $e) {
                    $errorMessage = "Error processing reviews for user #{$user->id}: {$e->getMessage()}";
                    $errors[] = $errorMessage;
                    
                    Log::error($errorMessage);
                    $this->error($errorMessage);
                }
            }

            $this->info("Process completed. Sent notifications to {$totalNotificationsSent} users.");

            // تسجيل نجاح المهمة
            $this->auditService->log(
                action: 'scheduled_task_completed',
                description: 'تم إنجاز عملية معالجة المراجعات اليومية بنجاح',
                severity: 'low',
                category: 'scheduled_task',
                metadata: [
                    'command' => 'reviews:process-daily',
                    'total_users_processed' => $totalUsersProcessed,
                    'notifications_sent' => $totalNotificationsSent,
                    'errors_count' => count($errors),
                    'completed_at' => Carbon::now()->toISOString()
                ]
            );

            return 0;

        } catch (\Exception $e) {
            // تسجيل فشل المهمة
            $this->auditService->log(
                action: 'scheduled_task_failed',
                description: 'فشل في عملية معالجة المراجعات اليومية: ' . $e->getMessage(),
                severity: 'high',
                category: 'scheduled_task',
                status: 'failed',
                metadata: [
                    'command' => 'reviews:process-daily',
                    'error_message' => $e->getMessage(),
                    'failed_at' => Carbon::now()->toISOString()
                ]
            );

            $this->error('Daily review process failed: ' . $e->getMessage());
            return 1;
        }
    }
}
