<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LeaderboardSeeder extends Seeder
{

    public function run(): void
    {
        \App\Models\Leaderboard::factory()->count(10)->create();
    }
}
