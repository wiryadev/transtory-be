<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_success()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->post('/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response->assertOk();
    }

    public function test_login_success_json()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response->assertOk();
    }

    public function test_login_success_json_path()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response
            ->assertJsonPath('data.user.name', "Admin")
            ->assertJsonPath('data.user.email', "admin@transtory.com");
    }

    public function test_login_success_json_response()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response
            ->assertJson([
                'data' => [
                    'user' => [
                        'name' => "Admin",
                        'email' => "admin@transtory.com"
                    ]
                ]
            ]);
    }

    /**
     * Check if Token exists and exactly 42 in length
     */
    public function test_login_success_json_token()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'token_type'
            ]
        ]);

        $this->assertTrue(strlen($response['data']['access_token']) == 42);
    }

    public function test_login_failed_no_data_json()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response
            ->assertUnauthorized()
            ->assertJsonPath('data.message', "Unauthorized")
            ->assertJson([
                'data' => [
                    'message' => "Unauthorized"
                ]
            ]);
    }

    public function test_login_failed_empty_email_json()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('data.message', "The email field is required.")
            ->assertJson([
                'data' => [
                    'message' => "The email field is required."
                ]
            ]);
    }

    public function test_login_failed_empty_password_json()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('data.message', "The password field is required.")
            ->assertJson([
                'data' => [
                    'message' => "The password field is required."
                ]
            ]);
    }

    public function test_login_failed_wrong_password_json()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => "env('DEFAULT_ADMIN_PASSWORD')",
            ]);

        $response
            ->assertUnauthorized()
            ->assertJsonPath('data.message', "Unauthorized")
            ->assertJson([
                'data' => [
                    'message' => "Unauthorized"
                ]
            ]);
    }

    public function test_login_failed_wrong_email_json()
    {
        $this->seed(UserSeeder::class);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.co.id",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $response
            ->assertUnauthorized()
            ->assertJsonPath('data.message', "Unauthorized")
            ->assertJson([
                'data' => [
                    'message' => "Unauthorized"
                ]
            ]);
    }
}
