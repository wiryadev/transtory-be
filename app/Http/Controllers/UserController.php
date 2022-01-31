<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ["required", "string", "max:255"],
                'email' => ["required", "string", "email", "max:255", "unique:users"],
                'password' => ["required", "string", new Password],
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => "Bearer",
                    'user' => $user
                ],
                "Registration Succeed"
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => "Something went wrong",
                    'error' => $e
                ],
                "Registration Failed",
                500
            );
        }
    }
}
