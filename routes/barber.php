<?php

use App\Http\Controllers\Barber\DashboardController as BarberDashboardController;
use App\Http\Controllers\Barber\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:barber,admin'])
    ->prefix('barber')
    ->name('barber.')
    ->group(function () {
        Route::get('/dashboard', [BarberDashboardController::class, 'index'])->name('dashboard');

        // Lịch làm việc
        Route::get('/schedule', [ScheduleController::class, 'edit'])->name('schedule.edit');
        Route::put('/schedule', [ScheduleController::class, 'update'])->name('schedule.update');
    });
