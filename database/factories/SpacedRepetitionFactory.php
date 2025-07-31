<?php

namespace Database\Factories;

use App\Models\PlanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpacedRepetition>
 */
class SpacedRepetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planItem = PlanItem::factory()->create();
        $intervalIndex = fake()->numberBetween(0, 10);
        $scheduledDate = fake()->dateTimeBetween('now', '+30 days');
        $easeFactor = fake()->randomFloat(2, 1.3, 2.5);
        $repetitionCount = fake()->numberBetween(0, 20);

        return [
            'plan_item_id' => $planItem->id,
            'interval_index' => $intervalIndex,
            'scheduled_date' => $scheduledDate->format('Y-m-d'),
            'ease_factor' => $easeFactor,
            'repetition_count' => $repetitionCount,
            'last_reviewed_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Create a spaced repetition for a specific plan item.
     */
    public function forPlanItem(PlanItem $planItem): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_item_id' => $planItem->id,
        ]);
    }

    /**
     * Create an initial spaced repetition (first review).
     */
    public function initial(): static
    {
        return $this->state(fn (array $attributes) => [
            'interval_index' => 0,
            'repetition_count' => 0,
            'last_reviewed_at' => null,
        ]);
    }

    /**
     * Create a spaced repetition with specific interval.
     */
    public function withInterval(int $intervalIndex): static
    {
        return $this->state(fn (array $attributes) => [
            'interval_index' => $intervalIndex,
        ]);
    }

    /**
     * Create a spaced repetition scheduled for today.
     */
    public function scheduledToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Create a spaced repetition scheduled for tomorrow.
     */
    public function scheduledTomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => now()->addDay()->format('Y-m-d'),
        ]);
    }

    /**
     * Create a spaced repetition with specific ease factor.
     */
    public function withEaseFactor(float $easeFactor): static
    {
        return $this->state(fn (array $attributes) => [
            'ease_factor' => $easeFactor,
        ]);
    }

    /**
     * Create a spaced repetition with specific repetition count.
     */
    public function withRepetitionCount(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'repetition_count' => $count,
        ]);
    }

    /**
     * Create an overdue spaced repetition.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => fake()->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
        ]);
    }
} 