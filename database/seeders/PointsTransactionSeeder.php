<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PointsTransactionSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\PointsTransaction::factory()->count(10)->create();
    }
}