<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
        } catch (ValidationException $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->validator->getMessageBag()->first(),
                    'error' => $e->getMessage()
                ],
                "Failed to create new wallet",
                422
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

    public function update(Request $request)
    {
        try {
            $request->validate([
                'wallets_id' => ["required", "numeric"],
                'account_no' => ["required", "string", "max:25", "unique:wallets"]
            ]);

            $wallet = Wallet::where('id', $request->wallets_id)->first();
            if (Auth::user()->id != $wallet['users_id']) {
                return ResponseFormatter::error(
                    [
                        'error' => "User and wallet mismatch"
                    ],
                    "Unauthorized",
                    401
                );
            }

            $updated = Wallet::where('id', $request->wallets_id)
                ->update([
                    'account_no' => $request->account_no,
                ]);

            return ResponseFormatter::success(
                [
                    'result' => $updated
                ],
                "Wallet updated successfully"
            );
        } catch (ValidationException $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->validator->getMessageBag()->first(),
                    'error' => $e->getMessage()
                ],
                "Failed to update wallet",
                422
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->getMessage(),
                    'error' => $e
                ],
                "Failed to update wallet",
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
                'wallets_id' => ["required", "numeric"],
            ]);

            $wallet = Wallet::where('id', $request->wallets_id)->first();
            if (Auth::user()->id != $wallet['users_id']) {
                return ResponseFormatter::error(
                    [
                        'error' => "User and wallet mismatch"
                    ],
                    "Unauthorized",
                    401
                );
            }

            $deleted = Wallet::where('id', $request->wallets_id)->delete();
            return ResponseFormatter::success(
                [
                    'result' => $deleted
                ],
                "Wallet deleted successfully"
            );
        } catch (ValidationException $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->validator->getMessageBag()->first(),
                    'error' => $e->getMessage()
                ],
                "Failed to delete wallet",
                422
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
