<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tự động generate time slots cho 7 ngày tới, chạy hằng ngày lúc 00:30
Schedule::command('slots:generate')->dailyAt('00:30');

