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

            // Roles
            // RoleSeeder::class,
            // ManagerRoleSeeder::class,
            // RegularUserRoleSeeder::class,

            // Quran Data
            ChaptersSeeder::class,
            FetchQuranVersesSeeder::class,
            JuzsSeeder::class,
            TafsirSeeder::class,
            ReciterSeeder::class,
            RecitationSeeder::class,
        ]);
    }
}
