<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointsTransaction>
 */
class PointsTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        
        $activityTypes = [
            'memorization' => [
                'points' => fake()->numberBetween(10, 50),
                'description' => 'Memorized new verses'
            ],
            'review' => [
                'points' => fake()->numberBetween(5, 25),
                'description' => 'Completed daily review'
            ],
            'streak' => [
                'points' => fake()->numberBetween(15, 100),
                'description' => 'Maintained learning streak'
            ],
            'badge' => [
                'points' => fake()->numberBetween(50, 500),
                'description' => 'Earned achievement badge'
            ],
            'daily_login' => [
                'points' => fake()->numberBetween(1, 10),
                'description' => 'Daily login bonus'
            ]
        ];

        $activityType = fake()->randomElement(array_keys($activityTypes));
        $activityData = $activityTypes[$activityType];

        return [
            'user_id' => $user->id,
            'points' => $activityData['points'],
            'activity_type' => $activityType,
            'description' => $activityData['description'],
            'transactionable_type' => null,
            'transactionable_id' => null,
        ];
    }

    /**
     * Create a transaction for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a memorization transaction.
     */
    public function memorization(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => 'memorization',
            'points' => fake()->numberBetween(10, 50),
            'description' => 'Memorized new verses',
        ]);
    }

    /**
     * Create a review transaction.
     */
    public function review(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => 'review',
            'points' => fake()->numberBetween(5, 25),
            'description' => 'Completed daily review',
        ]);
    }

    /**
     * Create a streak transaction.
     */
    public function streak(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => 'streak',
            'points' => fake()->numberBetween(15, 100),
            'description' => 'Maintained learning streak',
        ]);
    }

    /**
     * Create a badge transaction.
     */
    public function badge(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => 'badge',
            'points' => fake()->numberBetween(50, 500),
            'description' => 'Earned achievement badge',
        ]);
    }

    /**
     * Create a transaction with specific points.
     */
    public function withPoints(int $points): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => $points,
        ]);
    }

    /**
     * Create a transaction with polymorphic relationship.
     */
    public function withTransactionable(string $type, int $id): static
    {
        return $this->state(fn (array $attributes) => [
            'transactionable_type' => $type,
            'transactionable_id' => $id,
        ]);
    }
} 