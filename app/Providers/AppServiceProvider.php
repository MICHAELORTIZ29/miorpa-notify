<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('device-payments', function ($request) {
            return Limit::perMinute(300)->by(
                'payments:'.$request->attributes->get('device')->id);
        });
        RateLimiter::for('device-review', fn ($request) => Limit::perMinute(20)->by('review:'.$request->attributes->get('device')->id));
        RateLimiter::for('device-health', function ($request) {
            return Limit::perMinute(30)->by(
                'health:'.$request->attributes->get('device')->id);
        });
    }
}
