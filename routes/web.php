<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SuperAdmin\BusinessController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Business\UserController as BusinessUserController;
use App\Http\Controllers\Business\DeviceController;


Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
});

Route::middleware(['auth', 'active.user'])->group(function (): void {
    Route::prefix('business')
        ->name('business.')
        ->middleware(['auth', 'active.user', 'role:administrator'])
        ->group(function () {
            Route::view('/dashboard', 'dashboard')
                ->name('dashboard');

            Route::patch(
                '/users/{user}/deactivate',
                [BusinessUserController::class, 'deactivate']
            )->name('users.deactivate');

            Route::patch(
                '/users/{user}/activate',
                [BusinessUserController::class, 'activate']
            )->name('users.activate');

            Route::resource('users', BusinessUserController::class)
                ->only(['index', 'create', 'store', 'edit', 'update']);

            Route::get('/devices', [DeviceController::class, 'index'])
                ->name('devices.index');

            Route::post(
                '/devices/pairing-codes',
                [DeviceController::class, 'storePairingCode']
            )->name('devices.pairing-codes.store');

            Route::patch(
                '/devices/pairing-codes/{pairingCode}/revoke',
                [DeviceController::class, 'revokePairingCode']
            )->name('devices.pairing-codes.revoke');

            Route::patch(
                '/devices/{device}/deactivate',
                [DeviceController::class, 'deactivate']
            )->name('devices.deactivate');

            Route::patch(
                '/devices/{device}/activate',
                [DeviceController::class, 'activate']
            )->name('devices.activate');

            Route::patch(
                '/devices/{device}/revoke',
                [DeviceController::class, 'revoke']
            )->name('devices.revoke');
        });

    Route::get('/business/dashboard', function () {
        return view('dashboard', [
            'user' => request()->user(),
        ]);
    })->name('business.dashboard');

    Route::prefix('superadmin')
        ->name('superadmin.')
        ->middleware(['auth', 'active.user', 'role:superadmin'])
        ->group(function () {
            Route::patch(
                'businesses/{business}/suspend',
                [BusinessController::class, 'suspend']
            )->name('businesses.suspend');

            Route::patch(
                'businesses/{business}/activate',
                [BusinessController::class, 'activate']
            )->name('businesses.activate');

            Route::resource('businesses', BusinessController::class)
                ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        });
    Route::view('/cashier/dashboard', 'dashboard')
        ->middleware(['auth', 'active.user', 'role:cashier'])
        ->name('cashier.dashboard');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});