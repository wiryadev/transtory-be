<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddWalletTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->seed();
        $accountNumber = "01157690897";

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
            ->postJson('/api/wallet/add', [
                'banks_id' => "1",
                'account_no' => $accountNumber,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.wallet.banks_id', "1")
            ->assertJsonPath('data.wallet.account_no', $accountNumber)
            ->assertJson([
                'data' => [
                    'wallet' => [
                        'banks_id' => "1",
                        'account_no' => $accountNumber
                    ]
                ]
            ]);
    }
}
