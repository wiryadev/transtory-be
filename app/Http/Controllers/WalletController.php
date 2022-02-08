<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function add(Request $request)
    {
        try {
            $request->validate([
                'banks_id' => ["required", "numeric"],
                'account_no' => ["required", "string", "max:25", "unique:wallets"]
            ]);

            $wallet = Wallet::create([
                'users_id' => Auth::user()->id,
                'banks_id' => $request->banks_id,
                'account_no' => $request->account_no,
            ]);

            return ResponseFormatter::success(
                [
                    'wallet' => $wallet
                ],
                "Wallet created successfully"
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => "Something went wrong",
                    'error' => $e
                ],
                "Failed to create new wallet",
                500
            );
        }
    }
}
