<?php

namespace Database\Factories;

use App\Models\SpacedRepetition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReviewRecord>
 */
class ReviewRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $spacedRepetition = SpacedRepetition::factory()->create();
        $performanceRating = fake()->numberBetween(0, 5);
        $successful = $performanceRating >= 3; // Consider 3+ as successful
        $reviewDate = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'spaced_repetition_id' => $spacedRepetition->id,
            'review_date' => $reviewDate,
            'performance_rating' => $performanceRating,
            'successful' => $successful,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create a review record for a specific spaced repetition.
     */
    public function forSpacedRepetition(SpacedRepetition $spacedRepetition): static
    {
        return $this->state(fn (array $attributes) => [
            'spaced_repetition_id' => $spacedRepetition->id,
        ]);
    }

    /**
     * Create a successful review record.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'performance_rating' => fake()->numberBetween(3, 5),
            'successful' => true,
        ]);
    }

    /**
     * Create a failed review record.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'performance_rating' => fake()->numberBetween(0, 2),
            'successful' => false,
        ]);
    }

    /**
     * Create a review record with specific performance rating.
     */
    public function withRating(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'performance_rating' => $rating,
            'successful' => $rating >= 3,
        ]);
    }

    /**
     * Create a review record with notes.
     */
    public function withNotes(string $notes): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => $notes,
        ]);
    }

    /**
     * Create a review record for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'review_date' => now(),
        ]);
    }

    /**
     * Create a review record for yesterday.
     */
    public function yesterday(): static
    {
        return $this->state(fn (array $attributes) => [
            'review_date' => now()->subDay(),
        ]);
    }

    /**
     * Create a review record for a specific date.
     */
    public function onDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'review_date' => $date,
        ]);
    }
} 