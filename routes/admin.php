<?php

use App\Http\Controllers\Admin\BarberController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('barbers', BarberController::class)->except(['show']);

        // Lịch làm việc
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('schedules/{barber}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('schedules/{barber}', [ScheduleController::class, 'update'])->name('schedules.update');
    });
