<?php

namespace Tests\Feature\UserAuth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test verification link successfully verifies email.
     */
    public function test_verification_link_verifies_email_successfully()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        // Generate a temporary signed URL matching the route name and params
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $response = $this->get($url);

        // Assert
        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /**
     * Test invalid signature.
     */
    public function test_invalid_signature()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'invalidsig@example.com',
            'email_verified_at' => null,
        ]);

        $validUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Break the signature
        $parsed = parse_url($validUrl);
        parse_str($parsed['query'], $query);

        $query['signature'] = 'invalidsignature';

        $tamperedUrl = $parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'] . '?' . http_build_query($query);

        // Act
        $response = $this->get($tamperedUrl);

        // Assert
        $response->assertStatus(403);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    /**
     * Test expired signature.
     */
    public function test_expired_signature()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'expired@example.com',
            'email_verified_at' => null,
        ]);

        // Create an URL that expired in the past
        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->subMinutes(5), // already expired
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $response = $this->get($expiredUrl);

        // Assert
        $response->assertStatus(403);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_already_verified_user_returns_ok_and_stays_verified()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'already@example.com',
            'email_verified_at' => Carbon::now(),
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $response = $this->get($url);

        // Assert
        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
