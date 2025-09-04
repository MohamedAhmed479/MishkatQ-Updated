<?php

namespace App\Console\Commands;

use App\Services\IncentiveService;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateLeaderboards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:update {--type=monthly : The type of leaderboard to update (daily, weekly, monthly, yearly)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update leaderboards for the specified period type';

    /**
     * Execute the console command.
     */
    public function handle(IncentiveService $incentiveService, AuditService $auditService): int
    {
        $type = $this->option('type');

        if (!in_array($type, ['daily', 'weekly', 'monthly', 'yearly'])) {
            $this->error('Invalid leaderboard type. Must be one of: daily, weekly, monthly, yearly');
            
            // تسجيل خطأ في التحقق من النوع
            $auditService->log(
                action: 'scheduled_task_failed',
                description: "نوع لوحة المتصدرين غير صحيح: {$type}",
                severity: 'medium',
                category: 'scheduled_task',
                status: 'failed',
                metadata: [
                    'command' => 'leaderboards:update',
                    'invalid_type' => $type,
                    'failed_at' => Carbon::now()->toISOString()
                ]
            );
            
            return 1;
        }

        $this->info("Updating {$type} leaderboards...");

        // تسجيل بداية المهمة
        $auditService->log(
            action: 'scheduled_task_started',
            description: "بدء تحديث لوحة المتصدرين {$type}",
            severity: 'low',
            category: 'scheduled_task',
            metadata: [
                'command' => 'leaderboards:update',
                'leaderboard_type' => $type,
                'started_at' => Carbon::now()->toISOString()
            ]
        );

        try {
            $result = $incentiveService->updateLeaderboards($type);
            $this->info("{$type} leaderboards updated successfully!");

            // تسجيل نجاح المهمة
            $auditService->log(
                action: 'scheduled_task_completed',
                description: "تم تحديث لوحة المتصدرين {$type} بنجاح",
                severity: 'low',
                category: 'scheduled_task',
                metadata: [
                    'command' => 'leaderboards:update',
                    'leaderboard_type' => $type,
                    'entries_updated' => $result['entries_updated'] ?? 0,
                    'users_ranked' => $result['users_ranked'] ?? 0,
                    'completed_at' => Carbon::now()->toISOString()
                ]
            );

            return 0;
        } catch (\Exception $e) {
            // تسجيل فشل المهمة
            $auditService->log(
                action: 'scheduled_task_failed',
                description: "فشل في تحديث لوحة المتصدرين {$type}: " . $e->getMessage(),
                severity: 'high',
                category: 'scheduled_task',
                status: 'failed',
                metadata: [
                    'command' => 'leaderboards:update',
                    'leaderboard_type' => $type,
                    'error_message' => $e->getMessage(),
                    'failed_at' => Carbon::now()->toISOString()
                ]
            );

            $this->error("Failed to update {$type} leaderboards: " . $e->getMessage());
            return 1;
        }
    }
}
