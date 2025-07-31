<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemorizationPlan>
 */
class MemorizationPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+3 months');

        return [
            "user_id" => User::factory(),
            "name" => $this->faker->sentence(3),
            "description" => $this->faker->paragraph(),
            "start_date" => $startDate->format('Y-m-d'),
            "end_date" => $endDate->format('Y-m-d'),
            "status" => $this->faker->randomElement(['active', 'paused', 'completed']),
        ];
    }

    /**
     * Create a plan for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create an active plan.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Create a paused plan.
     */
    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paused',
        ]);
    }

    /**
     * Create a completed plan.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Create a plan with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create a short-term plan (1-2 weeks).
     */
    public function shortTerm(): static
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 week');
        $endDate = (clone $startDate)->modify('+2 weeks');

        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);
    }

    /**
     * Create a long-term plan (3-6 months).
     */
    public function longTerm(): static
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate = (clone $startDate)->modify('+6 months');

        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);
    }

    /**
     * Create a plan that starts today.
     */
    public function startingToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->format('Y-m-d'),
        ]);
    }
}
