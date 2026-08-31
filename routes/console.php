<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// Pull new punches from every device every 10 minutes. withoutOverlapping()
// stops a slow/unreachable device from stacking up duplicate runs.
Schedule::command('attendance:sync')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onOneServer();
