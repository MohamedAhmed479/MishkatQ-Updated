<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SpacedRepetitionSeeder extends Seeder
{

    public function run(): void
    {
        \App\Models\SpacedRepetition::factory()->count(10)->create();
    }
}
