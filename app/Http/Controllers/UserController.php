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
                "Registration Failed",
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
                "Authentication Failed",
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

    public function user(Request $request)
    {
        try {
            $user = User::with('wallets')->where('id', $request->user()->id)->get();
            return ResponseFormatter::success(
                $user,
                "Fetch user data completed"
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

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'old_password' => ["required", "string"],
                'new_password' => ["required", "string", new Password],
                'confirm_password' => ["required", "string"],
            ]);

            $authUserId = Auth::user()->id;
            $user = User::where('id', $authUserId)->first();

            if (!Hash::check($request->old_password, $user->password)) {
                throw new Exception("Invalid Credentials");
            }

            if ($request->new_password != $request->confirm_password) {
                throw new Exception("Password confirmation does not match");
            }

            $result = $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return ResponseFormatter::success(
                [
                    'result' => $result,
                ],
                "Password Update Succeed"
            );
        } catch (ValidationException $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->validator->getMessageBag()->first(),
                    'error' => $e->getMessage()
                ],
                "Password Update Failed",
                422
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => $e->getMessage(),
                    'error' => $e,
                ],
                "Password Update Failed",
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
