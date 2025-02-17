<?php

use App\Models\Url;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('urls:delete-expired', function () {
    Url::where('expiry_time', '<', now())->delete();
})->purpose('Delete expired URLs from the database');

Schedule::command('urls:delete-expired')->everyFiveMinutes();