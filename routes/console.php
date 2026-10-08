<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('devices:monitor-health')->everyMinute()->withoutOverlapping(5);

Schedule::command('subscriptions:sync-status')
    ->hourly()
    ->withoutOverlapping();
Schedule::command('payments:deliver-notifications --max=100')
    ->everyMinute()
    ->withoutOverlapping(5);
