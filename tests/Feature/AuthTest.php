<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test user registration (happy path).
     */
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'authorization' => [
                        'token',
                        'type'
                    ]
                ]
            ]);
    }

    /**
     * Test user registration with invalid data (bad path).
     */
    public function test_user_registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => ' ',
            'password_confirmation' => 'mismatch'
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test user login (happy path).
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'authorization' => [
                        'token',
                        'type'
                    ]
                ]
            ]);
    }

    /**
     * Test user login with invalid credentials (bad path).
     */
    public function test_user_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401)->assertJson([
            'status' => 'error',
            'message' => 'Unauthorized'
        ]);
    }

    /**
     * Test user login with non-existent email (bad path).
     */
    public function test_user_login_fails_with_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401)->assertJson([
            'status' => 'error',
            'message' => 'Unauthorized'
        ]);
    }

    /**
     * Test user logout (happy path).
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
    }

    /**
     * Test user logout without authentication (bad path).
     */
    public function test_user_logout_fails_without_authentication()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
}
