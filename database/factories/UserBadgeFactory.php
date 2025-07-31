<?php

namespace Database\Factories;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserBadge>
 */
class UserBadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::first();
        $badge = Badge::inRandomOrder()->first() ?? Badge::first();
        $awardedAt = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'user_id' => $user ? $user->id : 1,
            'badge_id' => $badge ? $badge->id : 1,
            'awarded_at' => $awardedAt,
        ];
    }

    /**
     * Create a user badge for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a user badge for a specific badge.
     */
    public function forBadge(Badge $badge): static
    {
        return $this->state(fn (array $attributes) => [
            'badge_id' => $badge->id,
        ]);
    }

    /**
     * Create a user badge awarded today.
     */
    public function awardedToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'awarded_at' => now(),
        ]);
    }

    /**
     * Create a user badge awarded recently.
     */
    public function awardedRecently(): static
    {
        return $this->state(fn (array $attributes) => [
            'awarded_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create a user badge awarded in the past.
     */
    public function awardedInPast(): static
    {
        return $this->state(fn (array $attributes) => [
            'awarded_at' => fake()->dateTimeBetween('-1 year', '-30 days'),
        ]);
    }

    /**
     * Create a user badge awarded on a specific date.
     */
    public function awardedOn(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'awarded_at' => $date,
        ]);
    }
} 