<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Database Backup to R2 - Daily at Midnight
use Illuminate\Support\Facades\Schedule;
Schedule::command('db:backup-to-r2')->daily()->at('00:00');
