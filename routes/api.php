<?php

use App\Http\Controllers\Api\Payment\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::controller(PaymentController::class)->group(function () {
//     Route::get('/payment/initialize/{user}/{userId}', 'initializePayment')->name('callback');
//     Route::get('/payment/callback', 'handleCallback');
//     Route::post('/payment/refund', 'refundPayment');
// });

// Route::middleware('auth:sanctum')->group(function () {

    Route::get('/payment/initialize', [PaymentController::class, 'initializePayment']);

 
    Route::post('/payment/callback', [PaymentController::class, 'handleCallback']);

   
    Route::post('/payment/refund', [PaymentController::class, 'processRefund']);


// });