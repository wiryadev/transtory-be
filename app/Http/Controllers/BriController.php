<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use DateTimeZone;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use GuzzleHttp\Exception\RequestException;

class BriController extends Controller
{
    private $accountResponse;

    public static function getToken()
    {
        $client = new Client();
        $url = env("BRI_BASE_URL") . "/oauth/client_credential/accesstoken?grant_type=client_credentials";

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

    public function account(Request $request)
    {
        $account = $request->input('account');
        if (!$account) {
            return ResponseFormatter::error(
                [
                    'account' => $request->all()
                ],
                "Account can not be empty",
                401
            );
        }

        try {
            $client = new Client();
            $url = env("BRI_BASE_URL") . "/v2/inquiry/" . $account;

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

            $this->accountResponse = $client->request('GET', $url, [
                'headers' => $headers
            ]);

            return ResponseFormatter::success(
                [
                    'response' => json_decode($this->accountResponse->getBody())->Data
                ],
                "Successful Request"
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->getMessage(),
                    'error' => $this->accountResponse,
                ],
                "Failed Request"
            );
        }
    }

    public function transaction(Request $request)
    {
        $account = $request->input('account');
        if (!$account) {
            return ResponseFormatter::error(
                [
                    'account' => $request->all()
                ],
                "Account can not be empty",
                401
            );
        }

        try {
            $client = new Client();
            $url = env("BRI_BASE_URL") . "/v2.0/statement";

            $briTimestamp = gmdate("Y-m-d\TH:i:s.000\Z");

            $token = $this::getToken();

            $data = [
                'accountNumber' => $account,
                'startDate' => $request->input('start_date', '2020-12-01'),
                'endDate' =>  $request->input('end_date', '2020-12-31')
            ];

            $payload = "path=" . "/v2.0/statement" . "&verb=" . "POST" .
                "&token=Bearer " . $token . "&timestamp=" . $briTimestamp .
                '&body=' . json_encode($data);

            $signature = base64_encode(hash_hmac('sha256', $payload, env('BRI_CLIENT_SECRET'), true));

            $headers = [
                'Authorization' => "Bearer $token",
                'BRI-Signature' => $signature,
                'BRI-Timestamp' => $briTimestamp,
                'Content-Type' => "application/json",
                'BRI-External-Id' => "1234"
            ];

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'json' => $data,
            ]);

            return ResponseFormatter::success(
                [
                    'response' => json_decode($response->getBody())->data
                ],
                "Successful Request"
            );
        } catch (RequestException $e) {
            if ($e->hasResponse()){
                $response = $e->getResponse();
                if ($response->getStatusCode() == '400') {
                    return ResponseFormatter::error(
                        [
                            'message' => json_decode($response->getBody())->responseDescription,
                        ],
                        "Failed Request"
                    );
                }
            }
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->getMessage(),
                    'error' => $e,
                ],
                "Failed Request"
            );
        }
    }
}
