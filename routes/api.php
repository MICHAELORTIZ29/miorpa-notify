<?php

use App\Http\Controllers\Api\V1\DeviceSessionController;
use App\Http\Controllers\Api\V1\NotificationCaptureController;
use App\Http\Controllers\Api\V1\PairingController;
use App\Http\Controllers\Api\V1\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post(
        '/pairings/redeem',
        [PairingController::class, 'redeem']
    )
        ->middleware('throttle:10,1')
        ->name('api.v1.pairings.redeem');

    Route::prefix('device')
        ->middleware(['device.auth'])
        ->group(function () {
            Route::get(
                '/status',
                [DeviceSessionController::class, 'status']
            )->middleware('throttle:device-health')->name('api.v1.device.status');
            Route::post(
                '/payments',
                [PaymentController::class, 'store']
            )->middleware('throttle:device-payments')->name('api.v1.device.payments.store');

            Route::post('/notification-captures', [NotificationCaptureController::class, 'sync'])
                ->middleware('throttle:device-review')->name('api.v1.device.notification-captures');

            Route::post(
                '/heartbeat',
                [DeviceSessionController::class, 'heartbeat']
            )->middleware('throttle:device-health')->name('api.v1.device.heartbeat');
        });
});
