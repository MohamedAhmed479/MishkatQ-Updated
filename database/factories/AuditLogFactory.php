<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $actions = ['created', 'updated', 'deleted', 'login', 'logout', 'viewed', 'downloaded'];
        $action = fake()->randomElement($actions);
        $severity = fake()->randomElement(['low', 'medium', 'high', 'critical']);
        $status = fake()->randomElement(['success', 'failed', 'warning']);
        $category = fake()->randomElement(['auth', 'data', 'admin', 'security', 'user']);
        
        return [
            'user_id' => $user->id,
            'user_type' => 'User',
            'user_name' => $user->name,
            'user_email' => $user->email,
            'action' => $action,
            'description' => fake()->sentence(),
            'model_type' => fake()->optional()->randomElement(['User', 'MemorizationPlan', 'Badge', 'Chapter']),
            'model_id' => fake()->optional()->numberBetween(1, 1000),
            'model_name' => fake()->optional()->words(2, true),
            'old_values' => fake()->optional() ? json_encode(['field' => 'old_value']) : null,
            'new_values' => fake()->optional() ? json_encode(['field' => 'new_value']) : null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'method' => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'url' => fake()->url(),
            'request_data' => fake()->optional() ? json_encode(['param' => 'value']) : null,
            'severity' => $severity,
            'is_sensitive' => fake()->boolean(20), // 20% chance of being sensitive
            'session_id' => fake()->uuid(),
            'device_info' => fake()->optional()->sentence(),
            'status' => $status,
            'category' => $category,
            'metadata' => fake()->optional() ? json_encode(['key' => 'value']) : null,
            'performed_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Create an audit log for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);
    }

    /**
     * Create a login audit log.
     */
    public function login(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'login',
            'description' => 'User logged in successfully',
            'category' => 'auth',
            'severity' => 'low',
            'status' => 'success',
        ]);
    }

    /**
     * Create a logout audit log.
     */
    public function logout(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'logout',
            'description' => 'User logged out',
            'category' => 'auth',
            'severity' => 'low',
            'status' => 'success',
        ]);
    }

    /**
     * Create a creation audit log.
     */
    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'created',
            'description' => 'Resource created successfully',
            'status' => 'success',
        ]);
    }

    /**
     * Create an update audit log.
     */
    public function updated(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'updated',
            'description' => 'Resource updated successfully',
            'status' => 'success',
        ]);
    }

    /**
     * Create a deletion audit log.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'deleted',
            'description' => 'Resource deleted successfully',
            'status' => 'success',
        ]);
    }

    /**
     * Create a high severity audit log.
     */
    public function highSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'high',
            'is_sensitive' => true,
        ]);
    }

    /**
     * Create a critical severity audit log.
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'critical',
            'is_sensitive' => true,
            'status' => 'warning',
        ]);
    }

    /**
     * Create a failed audit log.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'severity' => fake()->randomElement(['medium', 'high']),
        ]);
    }

    /**
     * Create an admin action audit log.
     */
    public function adminAction(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'Admin',
            'category' => 'admin',
            'severity' => fake()->randomElement(['medium', 'high']),
        ]);
    }

    /**
     * Create a security-related audit log.
     */
    public function security(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'security',
            'severity' => fake()->randomElement(['high', 'critical']),
            'is_sensitive' => true,
        ]);
    }
} 