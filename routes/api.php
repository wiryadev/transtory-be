<?php

use App\Http\Controllers\BniController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('password/update', [UserController::class, 'updatePassword']);

    Route::prefix('wallet')->group(function () {
        Route::post('add', [WalletController::class, 'add']);
        Route::post('update', [WalletController::class, 'update']);
        Route::post('delete', [WalletController::class, 'delete']);
    });
});

Route::prefix('bri')->group(function () {
    Route::post('account', [BriController::class, 'account']);
    Route::post('transaction', [BriController::class, 'transaction']);
});

Route::prefix('bni')->group(function () {
    Route::post('account', [BniController::class, 'account']);
});
