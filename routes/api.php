<?php

use App\Http\Controllers\BniController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BriController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('bri')->group(function () {
    Route::post('account', [BriController::class, 'account']);
    Route::post('transaction', [BriController::class, 'transaction']);
});

Route::prefix('bni')->group(function () {
    Route::post('account', [BniController::class, 'account']);
});
