<?php

namespace App\Console\Commands;

use App\Services\IncentiveService;
use Illuminate\Console\Command;

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
    public function handle(IncentiveService $incentiveService): int
    {
        $type = $this->option('type');

        if (!in_array($type, ['daily', 'weekly', 'monthly', 'yearly'])) {
            $this->error('Invalid leaderboard type. Must be one of: daily, weekly, monthly, yearly');
            return 1;
        }

        $this->info("Updating {$type} leaderboards...");

        try {
            $incentiveService->updateLeaderboards($type);
            $this->info("{$type} leaderboards updated successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to update {$type} leaderboards: " . $e->getMessage());
            return 1;
        }
    }
}
