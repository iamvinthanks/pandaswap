<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/unauthenticated', function () {
    $response['code']     = 401;
    $response['valid']    = false;
    $response['message']  = 'Unauthenticated.';
    return response()->json($response, 401);
})->name('api.unauthenticated');
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', [App\Http\Controllers\Api\AuthController::class, 'user']);
    // Binance Code
    Route::get('/verifcode', 'App\Http\Controllers\Api\BinanceController@verifcode');
    Route::post('/redeemcode', 'App\Http\Controllers\Api\BinanceController@redeemcode');

    // Crypto Payment
    Route::post('/crypto-payment/create', [App\Http\Controllers\Api\CryptoPaymentController::class, 'CreateBill']);
    // Route::post('/crypto-payment/check', [App\Http\Controllers\Api\CryptoPaymentController::class, 'CheckBill']);

    // Escrow
    Route::post('/createescrow', 'App\Http\Controllers\Api\EscrowController@createescrow');
    Route::put('/joinescrow', 'App\Http\Controllers\Api\EscrowController@joinescrow');
    Route::get('/escrowdetail/{escrow_id}', 'App\Http\Controllers\Api\EscrowController@getEscrowdetail');
    Route::post('/approveescrow/{escrow_id}/seller', 'App\Http\Controllers\Api\EscrowController@approveescrowseller');
    Route::post('/approveescrow/{escrow_id}/buyer', 'App\Http\Controllers\Api\EscrowController@approveescrowbuyer');
    Route::post('/paymentprocess','App\Http\Controllers\Api\PaymentController@createpayment');
    Route::get('/reklist', 'App\Http\Controllers\Api\MemberController@NorekUser');
});
