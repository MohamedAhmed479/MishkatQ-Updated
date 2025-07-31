<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Badge>
 */
class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $badgeTypes = [
            'memorization' => [
                'name' => fake()->randomElement(['Memorization Master', 'Quran Guardian', 'Verse Virtuoso']),
                'description' => 'Complete memorization milestones',
                'winning_criteria' => ['type' => 'verses_memorized', 'threshold' => fake()->numberBetween(10, 100)]
            ],
            'review' => [
                'name' => fake()->randomElement(['Review Champion', 'Consistency King', 'Daily Devotee']),
                'description' => 'Maintain consistent review schedule',
                'winning_criteria' => ['type' => 'consecutive_days', 'threshold' => fake()->numberBetween(7, 30)]
            ],
            'streak' => [
                'name' => fake()->randomElement(['Streak Master', 'Unstoppable', 'Persistent Learner']),
                'description' => 'Maintain learning streaks',
                'winning_criteria' => ['type' => 'streak_days', 'threshold' => fake()->numberBetween(5, 50)]
            ]
        ];

        $type = fake()->randomElement(array_keys($badgeTypes));
        $badgeData = $badgeTypes[$type];

        return [
            'name' => $badgeData['name'],
            'description' => $badgeData['description'],
            'icon' => fake()->randomElement(['star', 'crown', 'medal', 'trophy', 'gem']),
            'points_value' => fake()->numberBetween(10, 500),
            'winning_criteria' => $badgeData['winning_criteria'],
            'is_active' => true,
        ];
    }

    /**
     * Create a memorization badge.
     */
    public function memorization(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement(['Memorization Master', 'Quran Guardian', 'Verse Virtuoso']),
            'description' => 'Complete memorization milestones',
            'winning_criteria' => ['type' => 'verses_memorized', 'threshold' => fake()->numberBetween(10, 100)],
        ]);
    }

    /**
     * Create a review badge.
     */
    public function review(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement(['Review Champion', 'Consistency King', 'Daily Devotee']),
            'description' => 'Maintain consistent review schedule',
            'winning_criteria' => ['type' => 'consecutive_days', 'threshold' => fake()->numberBetween(7, 30)],
        ]);
    }

    /**
     * Create an inactive badge.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a badge with specific points value.
     */
    public function withPoints(int $points): static
    {
        return $this->state(fn (array $attributes) => [
            'points_value' => $points,
        ]);
    }
} 