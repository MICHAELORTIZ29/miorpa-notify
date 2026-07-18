<?php

use App\Http\Controllers\Api\V1\DeviceSessionController;
use App\Http\Controllers\Api\V1\PairingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentController;

Route::prefix('v1')->group(function () {
    Route::post(
        '/pairings/redeem',
        [PairingController::class, 'redeem']
    )
        ->middleware('throttle:10,1')
        ->name('api.v1.pairings.redeem');

    Route::prefix('device')
        ->middleware(['device.auth', 'throttle:120,1'])
        ->group(function () {
            Route::get(
                '/status',
                [DeviceSessionController::class, 'status']
            )->name('api.v1.device.status');
            Route::post(
                '/payments',
                [PaymentController::class, 'store']
            )->name('api.v1.device.payments.store');

            Route::post(
                '/heartbeat',
                [DeviceSessionController::class, 'heartbeat']
            )->name('api.v1.device.heartbeat');
        });
});