<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use DateTime;
use DateTimeZone;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BriController extends Controller
{
    private $reqResponse;

    public static function getToken()
    {
        $client = new Client();
        $url = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $body = [
            'client_id' => env('BRI_CLIENT_ID'),
            'client_secret' => env('BRI_CLIENT_SECRET'),
        ];

        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'form_params' => $body,
        ]);

        return json_decode($response->getBody())->access_token;
    }

    public function account(Request $request, String $account)
    {
        if (!$account) {
            return ResponseFormatter::error(
                [
                    'account' => $account
                ],
                "Account can not be empty",
                401
            );
        }

        try {
            $client = new Client();
            $url = "https://sandbox.partner.api.bri.co.id/v2/inquiry/" . $account;

            $briTimestamp = gmdate("Y-m-d\TH:i:s.000\Z");

            $token = $this::getToken();

            $payload = "path=" . "/v2/inquiry/$account" . "&verb=" . "GET" .
                "&token=Bearer " . $token . "&timestamp=" . $briTimestamp .
                '&body=';

            $signature = base64_encode(hash_hmac('sha256', $payload, env('BRI_CLIENT_SECRET'), true));

            $headers = [
                'Authorization' => "Bearer $token",
                'BRI-Signature' => $signature,
                'BRI-Timestamp' => $briTimestamp,
            ];

            $this->reqResponse = $client->request('GET', $url, [
                'headers' => $headers
            ]);

            return ResponseFormatter::success(
                [
                    'response' => json_decode($this->reqResponse->getBody())->Data
                ],
                "Successful Request"
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->getMessage(),
                    'error' => $this->reqResponse,
                ],
                "Failed Request"
            );
        }
    }
}
