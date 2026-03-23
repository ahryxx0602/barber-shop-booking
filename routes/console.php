<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tự động generate time slots cho 7 ngày tới, chạy hằng ngày lúc 00:30
Schedule::command('slots:generate')->dailyAt('00:30');

// Tự động huỷ booking pending quá 30 phút, chạy mỗi 5 phút
Schedule::command('bookings:expire')->everyFiveMinutes();

// Dọn dẹp log cũ hơn 30 ngày, chạy mỗi Chủ nhật lúc 02:00
Schedule::command('logs:cleanup')->weeklyOn(0, '02:00');

