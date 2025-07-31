<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "total_points" => $this->faker->numberBetween(0, 10000),
            "username" => $this->faker->unique()->userName(),
            "profile_image" => $this->faker->optional()->imageUrl(200, 200, 'people'),
            "verses_memorized_count" => $this->faker->numberBetween(0, 6236), // Total Quran verses
        ];
    }

    /**
     * Create a profile for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'username' => $user->name . '_' . $this->faker->numberBetween(1, 999),
        ]);
    }

    /**
     * Create a beginner profile (0-1000 points).
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_points' => $this->faker->numberBetween(0, 1000),
            'verses_memorized_count' => $this->faker->numberBetween(0, 100),
        ]);
    }

    /**
     * Create an intermediate profile (1000-5000 points).
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_points' => $this->faker->numberBetween(1000, 5000),
            'verses_memorized_count' => $this->faker->numberBetween(100, 1000),
        ]);
    }

    /**
     * Create an advanced profile (5000+ points).
     */
    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_points' => $this->faker->numberBetween(5000, 10000),
            'verses_memorized_count' => $this->faker->numberBetween(1000, 3000),
        ]);
    }

    /**
     * Create a master profile (high achievements).
     */
    public function master(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_points' => $this->faker->numberBetween(8000, 15000),
            'verses_memorized_count' => $this->faker->numberBetween(3000, 6236),
        ]);
    }

    /**
     * Create a profile with a specific username.
     */
    public function withUsername(string $username): static
    {
        return $this->state(fn (array $attributes) => [
            'username' => $username,
        ]);
    }

    /**
     * Create a profile with a specific points count.
     */
    public function withPoints(int $points): static
    {
        return $this->state(fn (array $attributes) => [
            'total_points' => $points,
        ]);
    }

    /**
     * Create a profile with a specific verses count.
     */
    public function withVersesCount(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'verses_memorized_count' => $count,
        ]);
    }

    /**
     * Create a profile with a profile image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'profile_image' => $this->faker->imageUrl(200, 200, 'people'),
        ]);
    }

    /**
     * Create a profile without a profile image.
     */
    public function withoutImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'profile_image' => null,
        ]);
    }
}
