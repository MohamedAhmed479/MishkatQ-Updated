<?php

namespace App\Console\Commands;

use App\Services\AutoAdjustMemorizationPlanService;
use Illuminate\Console\Command;

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
     * @return int
     */
    public function handle(AutoAdjustMemorizationPlanService $service)
    {
        $this->info('Starting auto-adjustment of memorization plans...');

        try {
            $service->runAutoAdjustment();
            $this->info('Successfully completed auto-adjustment of memorization plans.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error during auto-adjustment: ' . $e->getMessage());
            return 1;
        }
    }
}
 