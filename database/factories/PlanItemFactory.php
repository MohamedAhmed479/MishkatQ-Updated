<?php

namespace Database\Factories;

use App\Models\MemorizationPlan;
use App\Models\Verse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanItem>
 */
class PlanItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $surahId = $this->faker->numberBetween(1, 114);

        $verseIds = Verse::where('chapter_id', $surahId)->pluck('id')->toArray();

        sort($verseIds);
        $startIndex = $this->faker->numberBetween(0, count($verseIds) - 2);
        $startId = $verseIds[$startIndex];
        $endId = $verseIds[$this->faker->numberBetween($startIndex, count($verseIds) - 1)];

        return [
            "plan_id" => MemorizationPlan::factory(),
            "quran_surah_id" => $surahId,
            "verse_start_id" => $startId,
            "verse_end_id" => $endId,
            "target_date" => $this->faker->date(),
            "is_completed" => $this->faker->boolean(),
            "sequence" => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Create a plan item for a specific memorization plan.
     */
    public function forPlan(MemorizationPlan $plan): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_id' => $plan->id,
        ]);
    }

    /**
     * Create a completed plan item.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }

    /**
     * Create an incomplete plan item.
     */
    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
        ]);
    }

    /**
     * Create a plan item with a specific target date.
     */
    public function withTargetDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'target_date' => $date,
        ]);
    }

    /**
     * Create a plan item with a target date in the future.
     */
    public function futureTarget(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_date' => $this->faker->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
        ]);
    }

    /**
     * Create a plan item with a target date in the past.
     */
    public function pastTarget(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_date' => $this->faker->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
        ]);
    }

    /**
     * Create a plan item for a specific surah.
     */
    public function forSurah(int $surahId): static
    {
        return $this->state(fn (array $attributes) => [
            'quran_surah_id' => $surahId,
        ]);
    }
}
