<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
                    'message' => "Something went wrong",
                    'error' => $e
                ],
                "Registration Failed",
                400
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => "email|required",
                'password' => "required",
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    [
                        'message' => "Unauthorized"
                    ],
                    "Authentication Failed",
                    401,
                );
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password)) {
                throw new Exception("Invalid Credentials");
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => "Bearer",
                    'user' => $user,
                ],
                "Authentication Succeed"
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
                    'message' => "Something went wrong",
                    'error' => $e,
                ],
                "Authentication Failed",
                401,
            );
        }
    }

    /**
     * Log out to revoke token
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success(
            $token,
            "Token Revoked"
        );
    }
}
