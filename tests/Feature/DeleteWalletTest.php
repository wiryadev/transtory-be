<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteWalletTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_delete_wallet_success()
    {

        $this->seed();
        $this->seed(WalletSeeder::class);

        $loginResponse = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $token = $loginResponse['data']['access_token'];

        $response = $this
            ->withHeaders([
                'Accept' => "application/json",
                'Authorization' => "Bearer $token",
            ])
            ->postJson('/api/wallet/delete', [
                'wallets_id' => "1",
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.result', 1)
            ->assertJsonPath('meta.message', "Wallet deleted successfully")
            ->assertJson([
                'data' => [
                    'result' => 1
                ]
            ]);
    }

    public function test_delete_wallet_failed_wallets_id()
    {

        $this->seed();
        $this->seed(WalletSeeder::class);

        $loginResponse = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $token = $loginResponse['data']['access_token'];

        $response = $this
            ->withHeaders([
                'Accept' => "application/json",
                'Authorization' => "Bearer $token",
            ])
            ->postJson('/api/wallet/delete');

        $response
            ->assertUnprocessable()
            ->assertJsonPath('data.message', "The wallets id field is required.")
            ->assertJson([
                'data' => [
                    'message' => "The wallets id field is required."
                ]
            ]);
    }
}
