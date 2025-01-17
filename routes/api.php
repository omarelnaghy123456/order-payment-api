<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('jwt.auth')->group(function () {
    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Payments
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::post('/process', [PaymentController::class, 'process']);
        Route::get('/{transactionId}/verify', [PaymentController::class, 'verify']);
    });
});
