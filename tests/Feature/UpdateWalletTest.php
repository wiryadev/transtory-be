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

        $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/login', [
                'email' => "admin@transtory.com",
                'password' => env('DEFAULT_ADMIN_PASSWORD'),
            ]);

        $newAccountNo = "888801000157510";

        $response = $this
            ->withHeaders([
                'Accept' => "application/json"
            ])
            ->postJson('/api/wallet/update', [
                'wallets_id' => "1",
                'account_no' => $newAccountNo,
            ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.result', 1)
            ->assertJsonPath('meta.message', "Wallet updated successfully")
            ->assertJson([
                'data' => [
                    'result' => 1
                ]
            ]);
    }
}
