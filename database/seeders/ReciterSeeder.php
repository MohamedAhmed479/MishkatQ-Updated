<?php

namespace Database\Seeders;

use App\Models\Reciter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReciterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reciters = [
            [
                'id' => 1,
                'reciter_name' => "AbdulBaset AbdulSamad",
            ],
            [
                'id' => 7,
                'reciter_name' => "Mishary Al-Afasy",
            ],
            [
                'id' => 9,
                'reciter_name' => "Muhammad Siddiq al-Minshawi",
            ]
        ];

        foreach($reciters as $reciter) {
            Reciter::create([
                'id' => $reciter['id'],
                'reciter_name' => $reciter['reciter_name'],
            ]);
        }
    }
}
