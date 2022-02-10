<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Create new wallet with given account_no and banks Id
     */
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
                    'message' => $e->getMessage(),
                    'error' => $e
                ],
                "Failed to create new wallet",
                500
            );
        }
    }

    /**
     * Delete specific wallet based on given wallet Id
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'id' => ["required", "numeric"],
            ]);

            $deleted = Wallet::where('id', $request->id)->delete();
            return ResponseFormatter::success(
                [
                    'result' => $deleted
                ],
                "Wallet deleted successfully"
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->getMessage(),
                    'error' => $e
                ],
                "Failed to delete wallet",
                500
            );
        }
    }
}
