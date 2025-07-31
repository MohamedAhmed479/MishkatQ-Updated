<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemorizationProgress>
 */
class MemorizationProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::first();
        $chapter = Chapter::inRandomOrder()->first() ?? Chapter::first();
        $totalVerses = fake()->numberBetween(10, 50);
        $versesMemorized = fake()->numberBetween(0, $totalVerses);
        
        $status = match (true) {
            $versesMemorized === 0 => 'not_started',
            $versesMemorized === $totalVerses => 'completed',
            default => 'in_progress'
        };

        return [
            'user_id' => $user ? $user->id : 1,
            'chapter_id' => $chapter ? $chapter->id : 1,
            'verses_memorized' => $versesMemorized,
            'total_verses' => $totalVerses,
            'status' => $status,
            'last_reviewed_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Create progress for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create progress for a specific chapter.
     */
    public function forChapter(Chapter $chapter): static
    {
        return $this->state(fn (array $attributes) => [
            'chapter_id' => $chapter->id,
            'total_verses' => $chapter->verses_count ?? fake()->numberBetween(10, 50),
        ]);
    }

    /**
     * Create not started progress.
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'verses_memorized' => 0,
            'status' => 'not_started',
            'last_reviewed_at' => null,
        ]);
    }

    /**
     * Create in progress memorization.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $totalVerses = $attributes['total_verses'] ?? fake()->numberBetween(10, 50);
            $versesMemorized = fake()->numberBetween(1, $totalVerses - 1);
            
            return [
                'verses_memorized' => $versesMemorized,
                'status' => 'in_progress',
                'last_reviewed_at' => fake()->dateTimeBetween('-7 days', 'now'),
            ];
        });
    }

    /**
     * Create completed memorization.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $totalVerses = $attributes['total_verses'] ?? fake()->numberBetween(10, 50);
            
            return [
                'verses_memorized' => $totalVerses,
                'status' => 'completed',
                'last_reviewed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ];
        });
    }
} 