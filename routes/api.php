<?php

use App\Http\Controllers\Api\V1\PairingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware('throttle:10,1')
    ->group(function () {
        Route::post(
            '/pairings/redeem',
            [PairingController::class, 'redeem']
        )->name('api.v1.pairings.redeem');
    });