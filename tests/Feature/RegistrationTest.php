<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test basic successful user registration
     */
    public function test_register_success()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->post('/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response->assertCreated();
    }

    /**
     * Test successful user registration JSON API
     * 
     * use "/api" routing
     */
    public function test_register_success_json()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
            ]);

        $response->assertOk();
    }

    /**
     * Test successful user registration JSON API
     * and check if actual response contains expected data
     * at a specified given path
     * 
     * use "/api" routing
     */
    public function test_register_success_json_path()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
            ]);

        $response
            ->assertJsonPath('data.user.name', "Test Case")
            ->assertJsonPath('data.user.email', "test.case@gmail.com")
            ->assertJsonPath('data.user.is_admin', 0);
    }

    /**
     * Test successful user registration JSON API
     * and check if expected response exists within actual response
     * 
     * use "/api" routing
     */
    public function test_register_success_json_response()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
            ]);

        $response->assertJson([
            'data' => [
                'user' => [
                    'name' => "Test Case",
                    'email' => "test.case@gmail.com",
                    'is_admin' => 0
                ]
            ]
        ]);
    }

    public function test_register_failed_empty_name()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->post('/register', [
                'email' => "test.case@gmail.com",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response->assertUnprocessable();
    }

    public function test_register_failed_empty_name_json()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/register', [
                'email' => "test.case@gmail.com",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response
            ->assertJsonPath('data.message', "The name field is required.")
            ->assertJson([
                'data' => [
                    'message' => "The name field is required.",
                    'error' => "The given data was invalid."
                ]
            ]);
    }

    public function test_register_failed_empty_email()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->post('/register', [
                'name' => "Test Case",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response->assertUnprocessable();
    }

    public function test_register_failed_empty_email_json()
    {
        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/register', [
                'name' => "Test Case",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response
            ->assertJsonPath('data.message', "The email field is required.")
            ->assertJson([
                'data' => [
                    'message' => "The email field is required.",
                    'error' => "The given data was invalid."
                ]
            ]);
    }

    public function test_register_failed_email_duplicate()
    {
        $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->post('/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->post('/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response->assertRedirect();
    }

    public function test_register_failed_email_duplicate_json()
    {
        $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->post('/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/register', [
                'name' => "Test Case",
                'email' => "test.case@gmail.com",
                'password' => "test.case",
                'password_confirmation' => "test.case"
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('data.message', "The email has already been taken.")
            ->assertJson([
                'data' => [
                    'message' => "The email has already been taken.",
                    'error' => "The given data was invalid."
                ]
            ]);
    }
}
