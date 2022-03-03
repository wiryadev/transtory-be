<?php

namespace Tests\Feature;

use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateWalletTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_wallet_success()
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
        $newAccountNo = "888801000157510";

        $response = $this
            ->withHeaders([
                'Accept' => "application/json",
                'Authorization' => "Bearer $token",
            ])
            ->postJson('/api/wallet/update', [
                'wallets_id' => "1",
                'account_no' => $newAccountNo,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.result', 1)
            ->assertJsonPath('meta.message', "Wallet updated successfully")
            ->assertJson([
                'data' => [
                    'result' => 1
                ]
            ]);
    }

    public function test_update_wallet_failed_account_no()
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
            ->postJson('/api/wallet/update', [
                'wallets_id' => "1",
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('data.message', "The account no field is required.")
            ->assertJson([
                'data' => [
                    'message' => "The account no field is required."
                ]
            ]);
    }
}
