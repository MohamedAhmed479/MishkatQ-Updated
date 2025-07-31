<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leaderboard>
 */
class LeaderboardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $periodType = fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']);
        $periodStart = fake()->dateTimeBetween('-1 year', 'now');
        
        // Calculate period end based on type
        $periodEnd = match ($periodType) {
            'daily' => clone $periodStart,
            'weekly' => (clone $periodStart)->modify('+6 days'),
            'monthly' => (clone $periodStart)->modify('+1 month -1 day'),
            'yearly' => (clone $periodStart)->modify('+1 year -1 day'),
        };

        return [
            'user_id' => $user->id,
            'total_points' => fake()->numberBetween(100, 10000),
            'rank' => fake()->numberBetween(1, 100),
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodEnd->format('Y-m-d'),
            'period_type' => $periodType,
        ];
    }

    /**
     * Create a leaderboard entry for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a daily leaderboard entry.
     */
    public function daily(): static
    {
        $periodStart = fake()->dateTimeBetween('-30 days', 'now');
        
        return $this->state(fn (array $attributes) => [
            'period_type' => 'daily',
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodStart->format('Y-m-d'),
        ]);
    }

    /**
     * Create a weekly leaderboard entry.
     */
    public function weekly(): static
    {
        $periodStart = fake()->dateTimeBetween('-12 weeks', 'now');
        $periodEnd = (clone $periodStart)->modify('+6 days');
        
        return $this->state(fn (array $attributes) => [
            'period_type' => 'weekly',
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodEnd->format('Y-m-d'),
        ]);
    }

    /**
     * Create a monthly leaderboard entry.
     */
    public function monthly(): static
    {
        $periodStart = fake()->dateTimeBetween('-12 months', 'now');
        $periodEnd = (clone $periodStart)->modify('+1 month -1 day');
        
        return $this->state(fn (array $attributes) => [
            'period_type' => 'monthly',
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodEnd->format('Y-m-d'),
        ]);
    }

    /**
     * Create a yearly leaderboard entry.
     */
    public function yearly(): static
    {
        $periodStart = fake()->dateTimeBetween('-5 years', 'now');
        $periodEnd = (clone $periodStart)->modify('+1 year -1 day');
        
        return $this->state(fn (array $attributes) => [
            'period_type' => 'yearly',
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodEnd->format('Y-m-d'),
        ]);
    }

    /**
     * Create a leaderboard entry with specific rank.
     */
    public function withRank(int $rank): static
    {
        return $this->state(fn (array $attributes) => [
            'rank' => $rank,
        ]);
    }

    /**
     * Create a leaderboard entry with specific points.
     */
    public function withPoints(int $points): static
    {
        return $this->state(fn (array $attributes) => [
            'total_points' => $points,
        ]);
    }
} 