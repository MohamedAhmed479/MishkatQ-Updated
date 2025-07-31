<?php

namespace Database\Factories;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $deviceType = $this->faker->randomElement(['iOS', 'Android', 'Windows Phone', 'Web']);
        
        return [
            "user_id" => User::factory(),
            "token_id" => null,
            "device_type" => $deviceType,
            "device_name" => $this->generateDeviceName($deviceType),
            "platform" => $deviceType,
            "browser" => $this->generateBrowser($deviceType),
            "ip_address" => $this->faker->ipv4(),
        ];
    }

    /**
     * Create a device for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create an iOS device.
     */
    public function ios(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'iOS',
            'platform' => 'iOS',
            'device_name' => $this->faker->randomElement(['iPhone 15', 'iPhone 14', 'iPhone 13', 'iPad Pro', 'iPad Air']),
            'browser' => 'Safari',
        ]);
    }

    /**
     * Create an Android device.
     */
    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'Android',
            'platform' => 'Android',
            'device_name' => $this->faker->randomElement(['Samsung Galaxy S24', 'Google Pixel 8', 'OnePlus 12', 'Xiaomi 14']),
            'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Samsung Internet']),
        ]);
    }

    /**
     * Create a web device.
     */
    public function web(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'Web',
            'platform' => 'Web',
            'device_name' => $this->faker->randomElement(['Desktop', 'Laptop', 'Tablet']),
            'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
        ]);
    }

    /**
     * Create a device with a specific token.
     */
    public function withToken(PersonalAccessToken $token): static
    {
        return $this->state(fn (array $attributes) => [
            'token_id' => $token->id,
        ]);
    }

    /**
     * Create a device with a specific IP address.
     */
    public function withIpAddress(string $ipAddress): static
    {
        return $this->state(fn (array $attributes) => [
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Generate a realistic device name based on device type.
     */
    private function generateDeviceName(string $deviceType): string
    {
        return match ($deviceType) {
            'iOS' => $this->faker->randomElement(['iPhone 15', 'iPhone 14', 'iPhone 13', 'iPad Pro', 'iPad Air', 'iPad Mini']),
            'Android' => $this->faker->randomElement(['Samsung Galaxy S24', 'Google Pixel 8', 'OnePlus 12', 'Xiaomi 14', 'Huawei P60']),
            'Windows Phone' => $this->faker->randomElement(['Lumia 950', 'Lumia 930', 'Lumia 830']),
            'Web' => $this->faker->randomElement(['Desktop', 'Laptop', 'Tablet', 'Workstation']),
            default => $this->faker->words(2, true),
        };
    }

    /**
     * Generate a realistic browser based on device type.
     */
    private function generateBrowser(string $deviceType): string
    {
        return match ($deviceType) {
            'iOS' => 'Safari',
            'Android' => $this->faker->randomElement(['Chrome', 'Firefox', 'Samsung Internet', 'Opera']),
            'Windows Phone' => 'Edge',
            'Web' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera']),
            default => 'Chrome',
        };
    }
}
