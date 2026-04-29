<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Database Backup to R2 - Daily at Midnight
use Illuminate\Support\Facades\Schedule;
// Schedule::command('db:backup-to-r2')->dailyAt('00:00')->timezone('Asia/Kolkata');
Schedule::command('db:backup-to-r2')->everyFiveMinutes()->timezone('Asia/Kolkata');
