<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $preferredTimes = $this->generatePreferredTimes();
        
        return [
            "user_id" => User::factory(),
            "daily_minutes" => $this->faker->numberBetween(15, 120),
            "sessions_per_day" => $this->faker->numberBetween(1, 5),
            "current_level" => $this->faker->randomElement(["beginner", "intermediate", "advanced"]),
            "preferred_times" => json_encode($preferredTimes),
            "tafsir_id" => $this->faker->numberBetween(1, 13),
        ];
    }

    /**
     * Create preferences for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create beginner preferences.
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_level' => 'beginner',
            'daily_minutes' => $this->faker->numberBetween(15, 30),
            'sessions_per_day' => $this->faker->numberBetween(1, 2),
        ]);
    }

    /**
     * Create intermediate preferences.
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_level' => 'intermediate',
            'daily_minutes' => $this->faker->numberBetween(30, 60),
            'sessions_per_day' => $this->faker->numberBetween(2, 3),
        ]);
    }

    /**
     * Create advanced preferences.
     */
    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_level' => 'advanced',
            'daily_minutes' => $this->faker->numberBetween(60, 120),
            'sessions_per_day' => $this->faker->numberBetween(3, 5),
        ]);
    }

    /**
     * Create morning person preferences.
     */
    public function morningPerson(): static
    {
        return $this->state(fn (array $attributes) => [
            'preferred_times' => json_encode(['06:00', '07:00', '08:00', '09:00']),
        ]);
    }

    /**
     * Create evening person preferences.
     */
    public function eveningPerson(): static
    {
        return $this->state(fn (array $attributes) => [
            'preferred_times' => json_encode(['18:00', '19:00', '20:00', '21:00']),
        ]);
    }

    /**
     * Create night owl preferences.
     */
    public function nightOwl(): static
    {
        return $this->state(fn (array $attributes) => [
            'preferred_times' => json_encode(['22:00', '23:00', '00:00', '01:00']),
        ]);
    }

    /**
     * Create light study preferences (15-30 minutes).
     */
    public function lightStudy(): static
    {
        return $this->state(fn (array $attributes) => [
            'daily_minutes' => $this->faker->numberBetween(15, 30),
            'sessions_per_day' => $this->faker->numberBetween(1, 2),
        ]);
    }

    /**
     * Create intensive study preferences (60+ minutes).
     */
    public function intensiveStudy(): static
    {
        return $this->state(fn (array $attributes) => [
            'daily_minutes' => $this->faker->numberBetween(60, 120),
            'sessions_per_day' => $this->faker->numberBetween(3, 5),
        ]);
    }

    /**
     * Create preferences with specific daily minutes.
     */
    public function withDailyMinutes(int $minutes): static
    {
        return $this->state(fn (array $attributes) => [
            'daily_minutes' => $minutes,
        ]);
    }

    /**
     * Create preferences with specific sessions per day.
     */
    public function withSessionsPerDay(int $sessions): static
    {
        return $this->state(fn (array $attributes) => [
            'sessions_per_day' => $sessions,
        ]);
    }

    /**
     * Create preferences with specific tafsir.
     */
    public function withTafsir(int $tafsirId): static
    {
        return $this->state(fn (array $attributes) => [
            'tafsir_id' => $tafsirId,
        ]);
    }

    /**
     * Generate realistic preferred times.
     */
    private function generatePreferredTimes(): array
    {
        $times = [];
        $numTimes = $this->faker->numberBetween(2, 4);
        
        $availableTimes = [
            '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
            '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
            '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'
        ];
        
        for ($i = 0; $i < $numTimes; $i++) {
            $time = $this->faker->randomElement($availableTimes);
            if (!in_array($time, $times)) {
                $times[] = $time;
            }
        }
        
        sort($times);
        return $times;
    }
}
