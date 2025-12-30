<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Quote Expiry Scheduled Commands
|--------------------------------------------------------------------------
|
| These commands run every minute to process expired quotes and timeout
| stale responses. The outbox processor handles reliable event publishing.
|
*/

Schedule::command('quotes:expire-requests')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('quotes:timeout-responses')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('outbox:process')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
