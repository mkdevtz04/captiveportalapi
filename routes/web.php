<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
Route::get('/', [PaymentController::class, 'index']);

// Payment API routes
Route::prefix('api')->group(function () {
    Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
    Route::post('/payment/callback', [PaymentController::class, 'callback']);
    Route::get('/payment/status',    [PaymentController::class, 'checkStatus']);
});
