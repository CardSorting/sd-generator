<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cleanup:notifications', function () {
    $count = app(\App\Support\NotificationManager::class)->deleteOld(30);
    $this->info("Deleted {$count} old notifications.");
})->purpose('Clean up old notifications');

Artisan::command('sync:models', function () {
    app(\App\Http\Controllers\ModelController::class)->sync();
    $this->info('Models synced successfully.');
})->purpose('Sync available SD models');
