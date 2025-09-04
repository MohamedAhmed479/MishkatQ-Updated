<?php

namespace App\Console\Commands;

use App\Services\AutoAdjustMemorizationPlanService;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoAdjustMemorizationPlanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memorization:auto-adjust';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-adjust memorization plans based on user performance';

    /**
     * Execute the console command.
     *
     * @param AutoAdjustMemorizationPlanService $service
     * @param AuditService $auditService
     * @return int
     */
    public function handle(AutoAdjustMemorizationPlanService $service, AuditService $auditService)
    {
        $this->info('Starting auto-adjustment of memorization plans...');

        // تسجيل بداية المهمة
        $auditService->log(
            action: 'scheduled_task_started',
            description: 'بدء عملية التعديل التلقائي لخطط الحفظ',
            severity: 'medium',
            category: 'scheduled_task',
            metadata: [
                'command' => 'memorization:auto-adjust',
                'started_at' => Carbon::now()->toISOString()
            ]
        );

        try {
            $result = $service->runAutoAdjustment();
            
            $this->info('Successfully completed auto-adjustment of memorization plans.');

            // تسجيل نجاح المهمة
            $auditService->log(
                action: 'scheduled_task_completed',
                description: 'تم إنجاز عملية التعديل التلقائي لخطط الحفظ بنجاح',
                severity: 'medium',
                category: 'scheduled_task',
                metadata: [
                    'command' => 'memorization:auto-adjust',
                    'adjustments_made' => $result['adjustments_made'] ?? 0,
                    'users_affected' => $result['users_affected'] ?? 0,
                    'completed_at' => Carbon::now()->toISOString()
                ]
            );

            return 0;
        } catch (\Exception $e) {
            // تسجيل فشل المهمة
            $auditService->log(
                action: 'scheduled_task_failed',
                description: 'فشل في عملية التعديل التلقائي لخطط الحفظ: ' . $e->getMessage(),
                severity: 'high',
                category: 'scheduled_task',
                status: 'failed',
                metadata: [
                    'command' => 'memorization:auto-adjust',
                    'error_message' => $e->getMessage(),
                    'failed_at' => Carbon::now()->toISOString()
                ]
            );

            $this->error('Error during auto-adjustment: ' . $e->getMessage());
            return 1;
        }
    }
}
 