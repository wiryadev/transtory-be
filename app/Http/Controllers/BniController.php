<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use GuzzleHttp\Exception\RequestException;

class BniController extends Controller
{

    public function getToken()
    {
        $client = new Client();
        $url = env('BRI_BASE_URL') . "api/oauth/token";

        $headers = [
            'Content-Type' => "application/x-www-form-urlencoded",
            'Authorization' => "Basic " . env('BNI_BASIC_TOKEN'),
        ];

        $body = [
            'grant_type' => "client_credentials",
        ];

        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'form_params' => $body,
        ]);

        return json_decode($response->getBody())->access_token;
    }

    public function account(Request $request)
    {
        try {
            $accountNo = $request->account_no;
            $token = $this->getToken();

            $client = new Client();
            $url = env('BRI_BASE_URL') . "p2pl/inquiry/account/history?access_token=" . $token;

            $headers = [
                'Content-Type' => "application/json",
                'X-API-Key' => env('BNI_API_KEY'),
            ];

            $data = [
                'request' => [
                    'header' => [
                        'signature' => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyZXF1ZXN0Ijp7ImhlYWRlciI6eyJjb21wYW55SWQiOiJUcmFuc3RvcnkiLCJwYXJlbnRDb21wYW55SWQiOiIiLCJyZXF1ZXN0VXVpZCI6IjQxM0RERjMzNkExNzRGMDEifSwiYWNjb3VudE51bWJlciI6IjAxMTU0NzYxMTcifX0.dBOZpCe_tYjH1mf3OqC-Hld4TV7ItBIK31Z53AQVr5Q",
                        // 'signature' => $this->generateSignature($accountNo),
                        'companyId' => "Transtory",
                        'parentCompanyId' => "",
                        'requestUuid' => "413DDF336A174F01"
                    ],
                    'accountNumber' => $accountNo,
                ],
            ];

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'json' => $data,
            ]);

            return ResponseFormatter::success(
                [
                    'response' => json_decode($response->getBody())->response
                ],
                "Successful Request"
            );
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return ResponseFormatter::error(
                    [
                        'response' => json_decode($response->getBody())->response,
                    ],
                    "Failed Request",
                    $response->getStatusCode(),
                );
            }
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->getMessage(),
                    'error' => $e->getTrace(),
                ],
                "Failed Request"
            );
        }
    }

    public function generateSignature(String $accountNo)
    // public function generateSignature(Request $accountNo)
    {
        // if (!$accountNo) {
        //     $accountNo = $accountNo->account_no;
        // }
        // Create token header as a JSON string
        $header = JSON_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]);
        // Create token payload as a JSON string
        $payload = JSON_encode([
            'clientId' => 'IDBNIU0FOREJPWA==',
            'accountNo' => $accountNo
        ]);
        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($header)
        );
        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($payload)
        );
        // Create Signature Hash
        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            '(your-API-key-secret)',
            true
        );
        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($signature)
        );
        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return $jwt;
    }
}
