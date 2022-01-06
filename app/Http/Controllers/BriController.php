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

            $token = env('BRI_TOKEN');
            $payload = "path=" . "/v2/inquiry/$account" . "&verb=" . "GET" .
                "&token=Bearer " . $token . "&timestamp=" . $briTimestamp .
                '&body=';

            $signature = base64_encode(hash_hmac('sha256', $payload, env('BRI_CLIENT_SECRET'), true));

            $headers = [
                'Authorization' => "Bearer 1n8Ta9NT5u19PgzusEbQbBAN7xIk",
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
                    'error' => $this->reqResponse
                ],
                "Failed Request"
            );
        }
    }
}
