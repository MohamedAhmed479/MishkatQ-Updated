<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MemorizationProgressSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\MemorizationProgress::factory()->count(10)->create();
    }
}