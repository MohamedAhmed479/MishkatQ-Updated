<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $revelationPlaces = ['makkah', 'madinah'];
        
        return [
            'name_ar' => $this->faker->unique()->words(2, true),
            'name_en' => $this->faker->unique()->words(2, true),
            'revelation_place' => $this->faker->randomElement($revelationPlaces),
            'revelation_order' => $this->faker->unique()->numberBetween(1, 114),
            'verses_count' => $this->faker->numberBetween(3, 286),
        ];
    }

    /**
     * Create a Makkah chapter.
     */
    public function makkah(): static
    {
        return $this->state(fn (array $attributes) => [
            'revelation_place' => 'makkah',
        ]);
    }

    /**
     * Create a Madinah chapter.
     */
    public function madinah(): static
    {
        return $this->state(fn (array $attributes) => [
            'revelation_place' => 'madinah',
        ]);
    }

    /**
     * Create a chapter with specific verse count.
     */
    public function withVersesCount(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'verses_count' => $count,
        ]);
    }

    /**
     * Create a short chapter (3-10 verses).
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'verses_count' => $this->faker->numberBetween(3, 10),
        ]);
    }

    /**
     * Create a medium chapter (11-50 verses).
     */
    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'verses_count' => $this->faker->numberBetween(11, 50),
        ]);
    }

    /**
     * Create a long chapter (51+ verses).
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'verses_count' => $this->faker->numberBetween(51, 286),
        ]);
    }

    /**
     * Create a chapter with specific revelation order.
     */
    public function withRevelationOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'revelation_order' => $order,
        ]);
    }
} 