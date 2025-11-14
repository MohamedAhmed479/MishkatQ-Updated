<?php

namespace Tests\Feature\UserAuth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * Test OTP verification success case.
     */
    public function test_verify_otp_success(){
        Cache::flush();

        // Arrange
        $email = "test@example.com";

        // Simulate storing OTP in cache
        Cache::put("password_reset_code_{$email}", "123456", 300);
        Cache::put("password_reset_token_{$email}", "generated-reset-token", 300);

        // Act
        $response = $this->postJson(route('password.verify.otp'), [
            'email' => $email,
            'code' => '123456',
        ]);

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data',
                ]);
    }

    /**
     * Test OTP verification failure case with invalid code.
     */
    public function test_verify_otp_failure_invalid_code(){
        Cache::flush();

        // Arrange
        $email = "test@example.come";

        // Simulate storing OTP in cache
        Cache::put("password_reset_code_{$email}", "123456", 300);
        Cache::put("password_reset_token_{$email}", "generated-reset-token", 300);

        // Act
        $response = $this->postJson(route('password.verify.otp'), [
            'email' => $email,
            'code' => '654321',
        ]);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'status',
                    'message',
                ]);
    }

    /**
     * Test OTP verification failure case when code expired.
     */
    public function test_verify_otp_when_code_expired(){
        Cache::flush();

        // Arrange
        $email = "test@example.come";

        // Act
        $response = $this->postJson(route('password.verify.otp'), [
            'email' => $email,
            'code' => '123456',
        ]);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'status',
                    'message',
                ]);
    }

    /**
     * Test reset password success case.
     */
    public function test_reset_password_success(){
        Cache::flush();

        // Arrange
        $email = "test@example.com";

        $user = User::factory()->create(['email' => $email]);

        Cache::put("password_reset_token_{$email}", 'reset-token-123', 300);

        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::PASSWORD_RESET);

        // Act
        $response = $this->postJson(route('password.store'), [
            'email' => $email,
            'token' => 'reset-token-123',
            'password' => 'MySecurePass123#',
            'password_confirmation' => 'MySecurePass123#',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
            ]);
    }

    /**
     * Test reset password failure case with invalid token.
     */
    public function test_reset_password_failure_invalid_token()
    {
        Cache::flush();

        // Arrange
        $email = 'test@example.com';
        User::factory()->create(['email' => $email]);

        Cache::put("password_reset_token_{$email}", 'correct-token', 300);

        // Act
        $response = $this->postJson(route('password.store'), [
            'email' => $email,
            'token' => 'wrong-token',
            'password' => 'MySecurePass123#',
            'password_confirmation' => 'MySecurePass123#',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
            ]);
    }

    /**
     * Test reset password failure case when token expired.
     */
    public function test_reset_password_failure_when_token_expired(){
        Cache::flush();

        // Arrange
        $email = "test@example.com";

        User::factory()->create(['email' => $email]);

        // Act
        $response = $this->postJson(route('password.store'), [
            'email' => $email,
            'token' => 'some-token',
            'password' => 'MySecurePass123#',
            'password_confirmation' => 'MySecurePass123#',
        ]);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'status',
                    'message',
                ]);
    }

    /**
     * Test reset password failure case with invalid password confirmation.
     */
    public function test_reset_password_failure_invalid_password_confirmation(){
        Cache::flush();

        // Arrange
        $email = "test@example.com";

        User::factory()->create(['email' => $email]);

        Cache::put("password_reset_token_{$email}", 'reset-token-123', 300);

        // Act
        $response = $this->postJson(route('password.store'), [
            'email' => $email,
            'token' => 'reset-token-123',
            'password' => 'MySecurePass123#',
            'password_confirmation' => 'DifferentPass123#',
        ]);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'status',
                    'message',
                ]);
    }

    /**
     * Test reset password failure case with weak password.
     */
    public function test_reset_password_failure_weak_password(){
        Cache::flush();

        // Arrange
        $email = "test@example.com";

        User::factory()->create(['email' => $email]);

        Cache::put("password_reset_token_{$email}", 'reset-token-123', 300);

        // Act
        $response = $this->postJson(route('password.store'), [
            'email' => $email,
            'token' => 'reset-token-123',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'status',
                    'message',
                ]);
    }

}
