<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
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

            // Quran Data
//            ChaptersSeeder::class,
//            FetchQuranVersesSeeder::class,
//            JuzsSeeder::class,
//            TafsirSeeder::class,
//            ReciterSeeder::class,
//            RecitationSeeder::class,
        ]);
    }
}
