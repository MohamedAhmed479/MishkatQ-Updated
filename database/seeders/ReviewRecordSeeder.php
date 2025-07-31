<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReviewRecordSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\ReviewRecord::factory()->count(10)->create();
    }
}