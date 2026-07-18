<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SuperAdmin\BusinessController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
});

Route::middleware(['auth', 'active.user'])->group(function (): void {
    Route::get('/dashboard', function (): RedirectResponse {
        $user = request()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.businesses.index');
        }

        return redirect()->route('business.dashboard');
    })->name('dashboard');

    Route::get('/business/dashboard', function () {
        return view('dashboard', [
            'user' => request()->user(),
        ]);
    })->name('business.dashboard');

    Route::prefix('superadmin')
        ->name('superadmin.')
        ->middleware('role:superadmin')
        ->group(function (): void {
            Route::resource('businesses', BusinessController::class)
                ->only(['index', 'create', 'store', 'show']);
        });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});