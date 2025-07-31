<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserBadgeSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\UserBadge::factory()->count(10)->create();
    }
}