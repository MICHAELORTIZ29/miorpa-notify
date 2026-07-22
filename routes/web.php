<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Business\DeviceController;
use App\Http\Controllers\Business\PaymentController as BusinessPaymentController;
use App\Http\Controllers\Business\UserController as BusinessUserController;
use App\Http\Controllers\SuperAdmin\BusinessController;
use App\Http\Controllers\Receiver\ReceiverLinkController;
use App\Http\Controllers\Business\DashboardController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Entrada principal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(
        match (auth()->user()->role_code) {
            User::ROLE_SUPERADMIN => 'superadmin.businesses.index',
            User::ROLE_ADMINISTRATOR => 'business.dashboard',
            User::ROLE_CASHIER => 'cashier.dashboard',
            default => 'login',
        }
    );
});

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get(
        '/login',
        [AuthenticatedSessionController::class, 'create']
    )->name('login');

    Route::post(
        '/login',
        [AuthenticatedSessionController::class, 'store']
    )->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Pantalla de suscripción suspendida
|--------------------------------------------------------------------------
|
| Esta ruta no usa active.user porque debe poder abrirse precisamente
| cuando el negocio está suspendido.
|
*/

Route::middleware('auth')->group(function () {
    Route::get(
        '/business/subscription-suspended',
        function () {
            return view('business.subscription-suspended');
        }
    )->name('business.subscription-suspended');
});

/*
|--------------------------------------------------------------------------
| Vinculación del receptor web
|--------------------------------------------------------------------------
*/

Route::prefix('receiver')
    ->name('receiver.')
    ->middleware([
        'auth',
        'active.user',
        'role:administrator,cashier',
    ])
    ->group(function () {
        Route::get(
            '/link',
            [ReceiverLinkController::class, 'create']
        )->name('link.create');

        Route::post(
            '/link',
            [ReceiverLinkController::class, 'store']
        )
            ->middleware('throttle:10,1')
            ->name('link.store');
    });

/*
|--------------------------------------------------------------------------
| Superadministrador
|--------------------------------------------------------------------------
*/

Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware([
        'auth',
        'active.user',
        'role:superadmin',
    ])
    ->group(function () {
        Route::patch(
            'businesses/{business}/suspend',
            [BusinessController::class, 'suspend']
        )->name('businesses.suspend');

        Route::patch(
            'businesses/{business}/activate',
            [BusinessController::class, 'activate']
        )->name('businesses.activate');

        Route::resource(
            'businesses',
            BusinessController::class
        )->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
        ]);
    });

/*
|--------------------------------------------------------------------------
| Administración del negocio
|--------------------------------------------------------------------------
*/

Route::prefix('business')
    ->name('business.')
    ->middleware([
        'auth',
        'active.user',
        'role:administrator',
    ])
    ->group(function () {
        Route::get(
            '/dashboard',
            DashboardController::class
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Usuarios y cajeros
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/users/{user}/deactivate',
            [BusinessUserController::class, 'deactivate']
        )->name('users.deactivate');

        Route::patch(
            '/users/{user}/activate',
            [BusinessUserController::class, 'activate']
        )->name('users.activate');

        Route::resource(
            'users',
            BusinessUserController::class
        )->only([
            'index',
            'create',
            'store',
            'edit',
            'update',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Dispositivos
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/devices',
            [DeviceController::class, 'index']
        )->name('devices.index');

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

/*
|--------------------------------------------------------------------------
| Pagos: administrador y cajero
|--------------------------------------------------------------------------
*/

Route::prefix('business')
    ->name('business.')
    ->middleware([
        'auth',
        'active.user',
        'role:administrator,cashier',
        'receiver.linked',
    ])
    ->group(function () {
        Route::get(
            '/payments',
            [BusinessPaymentController::class, 'index']
        )->name('payments.index');

        Route::get(
            '/payments/export',
            [
                BusinessPaymentController::class,
                'export',
            ]
        )->name('payments.export');

        /*
         * Esta ruta debe estar antes de /payments/{payment}.
         */
        Route::get(
            '/payments/live-status',
            [BusinessPaymentController::class, 'liveStatus']
        )->name('payments.live-status');

        Route::get(
            '/payments/{payment}',
            [BusinessPaymentController::class, 'show']
        )->name('payments.show');

        Route::patch(
            '/payments/{payment}/confirm',
            [BusinessPaymentController::class, 'confirm']
        )->name('payments.confirm');
    });

/*
|--------------------------------------------------------------------------
| Cajero
|--------------------------------------------------------------------------
*/

Route::get('/cashier/dashboard', function () {
    return redirect()->route('business.payments.index');
})
    ->middleware([
        'auth',
        'active.user',
        'role:cashier',
    ])
    ->name('cashier.dashboard');

/*
|--------------------------------------------------------------------------
| Cerrar sesión
|--------------------------------------------------------------------------
*/

Route::post(
    '/logout',
    [AuthenticatedSessionController::class, 'destroy']
)
    ->middleware('auth')
    ->name('logout');