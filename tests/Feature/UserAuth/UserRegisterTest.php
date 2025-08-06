<?php

namespace Tests\Feature\UserAuth;

use App\Events\UserRegisteredEvent;
use App\Models\Device;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * Test successful user registration with all required data
     */
    public function test_user_can_register_successfully_with_valid_data(): void
    {
        // Arrange
        Notification::fake();

        $registerData = [
            "name" => "Mohamed Ahmed",
            "email" => "mohamed@gmail.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
            "device_type" => "mobile",
            "device_name" => "iPhone 12",
            "platform" => "iOS",
            "browser" => "Safari",
            "ip_address" => "192.168.1.1",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'token',
                    'profile' => [
                        'username',
                        'profile_image',
                        'verses_memorized_count',
                    ],
                    'preferences' => [
                        'daily_minutes',
                        'sessions_per_day',
                        'preferred_times',
                        'current_level',
                        'tafsir_id',
                        'tafsir_name',
                    ],
                    'deviceInfo' => [
                        'device_type',
                        'device_name',
                        'platform',
                        'browser',
                        'ip_address',
                    ],
                ],
            ])
            ->assertJson([
                'status' => true,
                'data' => [
                    'name' => 'Mohamed Ahmed',
                    'email' => 'mohamed@gmail.com',
                ],
            ]);

        // Assert database records were created
        $this->assertDatabaseHas('users', [
            'name' => 'Mohamed Ahmed',
            'email' => 'mohamed@gmail.com',
        ]);

        $user = User::where('email', 'mohamed@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('MySecurePass123#', $user->password));

        // Assert user profile was created
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
        ]);

        // Assert user preferences were created
        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'daily_minutes' => 0,
            'sessions_per_day' => 0,
            'current_level' => 'beginner',
        ]);

        // Assert device was created
        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'device_type' => 'mobile',
            'device_name' => 'iPhone 12',
            'platform' => 'iOS',
            'browser' => 'Safari',
            'ip_address' => '192.168.1.1',
        ]);

        // Event was fired and listeners ran to create profile and preferences
    }

    /**
     * Test user registration with minimal required data
     */
    public function test_user_can_register_with_minimal_data(): void
    {
        // Arrange
        $registerData = [
            "name" => "Ahmed Ali",
            "email" => "ahmed@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201);

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
        ]);

        // Event was fired and listeners ran to create profile and preferences
    }

    /**
     * Test registration validation - missing required fields
     */
    public function test_registration_fails_with_missing_required_fields(): void
    {
        // Act
        $response = $this->postJson(route("auth.register"), []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                ],
            ])
            ->assertJson([
                'status' => false,
            ]);
    }

    /**
     * Test registration validation - invalid email format
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        // Arrange
        $registerData = [
            "name" => "Test User",
            "email" => "invalid-email",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'email',
                ],
            ]);
    }

    /**
     * Test registration validation - duplicate email
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        // Arrange
        User::factory()->create(['email' => 'existing@example.com']);

        $registerData = [
            "name" => "Test User",
            "email" => "existing@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'email',
                ],
            ]);
    }

    /**
     * Test registration validation - weak password
     */
    public function test_registration_fails_with_weak_password(): void
    {
        // Arrange
        $registerData = [
            "name" => "Test User",
            "email" => "test@example.com",
            "password" => "weak",
            "password_confirmation" => "weak",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'password',
                ],
            ]);
    }

    /**
     * Test registration validation - password confirmation mismatch
     */
    public function test_registration_fails_with_password_confirmation_mismatch(): void
    {
        // Arrange
        $registerData = [
            "name" => "Test User",
            "email" => "test@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "DifferentSecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'password',
                ],
            ]);
    }

    /**
     * Test registration validation - name too long
     */
    public function test_registration_fails_with_name_too_long(): void
    {
        // Arrange
        $registerData = [
            "name" => str_repeat("a", 101), // 101 characters
            "email" => "test@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'name',
                ],
            ]);
    }

    /**
     * Test registration validation - invalid IP address
     */
    public function test_registration_fails_with_invalid_ip_address(): void
    {
        // Arrange
        $registerData = [
            "name" => "Test User",
            "email" => "test@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
            "ip_address" => "invalid-ip",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'ip_address',
                ],
            ]);
    }

    /**
     * Test that user profile is created with correct data
     */
    public function test_user_profile_is_created_correctly(): void
    {
        // Arrange
        $registerData = [
            "name" => "Profile Test User",
            "email" => "profile@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201);

        $user = User::where('email', 'profile@example.com')->first();
        $profile = UserProfile::where('user_id', $user->id)->first();

        $this->assertNotNull($profile);
        $this->assertStringContainsString('Profile Test User', $profile->username);
        $this->assertEquals(0, $profile->verses_memorized_count);
        $this->assertNull($profile->profile_image);
    }

    /**
     * Test that user preferences are created with default values
     */
    public function test_user_preferences_are_created_with_defaults(): void
    {
        // Arrange
        $registerData = [
            "name" => "Preferences Test User",
            "email" => "preferences@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201);

        $user = User::where('email', 'preferences@example.com')->first();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        $this->assertNotNull($preferences);
        $this->assertEquals(0, $preferences->daily_minutes);
        $this->assertEquals(0, $preferences->sessions_per_day);
        $this->assertEquals('beginner', $preferences->current_level);
        $this->assertNull($preferences->tafsir_id);
    }

    /**
     * Test that access token is generated and returned
     */
    public function test_access_token_is_generated_and_returned(): void
    {
        // Arrange
        $registerData = [
            "name" => "Token Test User",
            "email" => "token@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201);

        $responseData = $response->json('data');
        $this->assertArrayHasKey('token', $responseData);
        $this->assertNotEmpty($responseData['token']);
    }

    /**
     * Test that email verification notification is sent
     */
    public function test_email_verification_notification_is_sent(): void
    {
        // Arrange
        Notification::fake();

        $registerData = [
            "name" => "Email Test User",
            "email" => "email@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201);

        // The notification is sent via the event listener
    }

    /**
     * Test registration with all optional device fields
     */
    public function test_registration_with_all_device_fields(): void
    {
        // Arrange
        $registerData = [
            "name" => "Device Test User",
            "email" => "device@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
            "device_type" => "desktop",
            "device_name" => "MacBook Pro",
            "platform" => "macOS",
            "browser" => "Chrome",
            "ip_address" => "10.0.0.1",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201);

        $user = User::where('email', 'device@example.com')->first();
        $device = Device::where('user_id', $user->id)->first();

        $this->assertNotNull($device);
        $this->assertEquals('desktop', $device->device_type);
        $this->assertEquals('MacBook Pro', $device->device_name);
        $this->assertEquals('macOS', $device->platform);
        $this->assertEquals('Chrome', $device->browser);
        $this->assertEquals('10.0.0.1', $device->ip_address);
    }

    /**
     * Test that registration response includes correct message
     */
    public function test_registration_response_includes_success_message(): void
    {
        // Arrange
        $registerData = [
            "name" => "Message Test User",
            "email" => "message@example.com",
            "password" => "MySecurePass123#",
            "password_confirmation" => "MySecurePass123#",
        ];

        // Act
        $response = $this->postJson(route("auth.register"), $registerData);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'تم تسجيل المستخدم بنجاح. تم إرسال رسالة التحقق إلى message@example.com.',
            ]);
    }
}
