<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => 'First Steps',
                'description' => 'Memorize your first 10 verses',
                'icon' => 'first-steps.svg',
                'points_value' => 100,
                'winning_criteria' => [
                    'type' => 'verses_memorized',
                    'threshold' => 10
                ],
            ],
            [
                'name' => 'Dedicated Learner',
                'description' => 'Memorize 50 verses',
                'icon' => 'dedicated-learner.svg',
                'points_value' => 500,
                'winning_criteria' => [
                    'type' => 'verses_memorized',
                    'threshold' => 50
                ],
            ],
            [
                'name' => 'Quran Scholar',
                'description' => 'Memorize 100 verses',
                'icon' => 'quran-scholar.svg',
                'points_value' => 1000,
                'winning_criteria' => [
                    'type' => 'verses_memorized',
                    'threshold' => 100
                ],
            ],
            [
                'name' => 'Consistent Learner',
                'description' => 'Practice for 7 consecutive days',
                'icon' => 'consistent-learner.svg',
                'points_value' => 200,
                'winning_criteria' => [
                    'type' => 'consecutive_days',
                    'threshold' => 7
                ],
            ],
            [
                'name' => 'Perfect Review',
                'description' => 'Complete 5 perfect reviews',
                'icon' => 'perfect-review.svg',
                'points_value' => 300,
                'winning_criteria' => [
                    'type' => 'perfect_reviews',
                    'threshold' => 5
                ],
            ],
            [
                'name' => 'Point Collector',
                'description' => 'Earn 1000 points',
                'icon' => 'point-collector.svg',
                'points_value' => 500,
                'winning_criteria' => [
                    'type' => 'total_points',
                    'threshold' => 1000
                ],
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
} 