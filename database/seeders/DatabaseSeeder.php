<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ⏱️ Start timing
        $start = microtime(true);
        $this->command->info('🚀 Starting full database seeding...');

        $this->call([
            // Quran Data
            ChaptersSeeder::class,
            FetchQuranVersesSeeder::class,
            JuzsSeeder::class,
            TafsirSeeder::class,
            ReciterSeeder::class,
            RecitationSeeder::class,

            // Admin
            // AdminSeeder::class,

            // Badges
            BadgeSeeder::class,

            // Devices
            DeviceSeeder::class,

            // Users
            UserSeeder::class,

            // User Profiles
            UserProfileSeeder::class,

            // Plan Items
            PlanItemSeeder::class,

            // Memorization Plans
            MemorizationPlanSeeder::class,

            // User Preferences
            UserPreferenceSeeder::class,

            // Review Records
            ReviewRecordSeeder::class,

            // Audit Logs
            AuditLogSeeder::class,

            // Leaderboards
            LeaderboardSeeder::class,

            // User Badges
            UserBadgeSeeder::class,

            // Points Transactions
            PointsTransactionSeeder::class,

            // Memorization Progress
            MemorizationProgressSeeder::class,

            // Spaced Repetitions
            SpacedRepetitionSeeder::class,

            // Roles
            // RoleSeeder::class,
            // ManagerRoleSeeder::class,
            // RegularUserRoleSeeder::class,
        ]);

        // ⏱️ End timing
        $end = microtime(true);
        $duration = $end - $start;

        $this->command->info('✅ Seeding completed successfully!');
        $this->command->info('🕒 Total time: ' . $this->formatDuration($duration));
    }

    protected function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = round($seconds % 60, 2);

        if ($minutes > 0) {
            return "{$minutes} min {$remainingSeconds} sec";
        }

        return "{$remainingSeconds} sec";
    }
}
