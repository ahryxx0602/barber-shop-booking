<?php

use App\Http\Controllers\Barber\BookingController as BarberBookingController;
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

        // Quan ly booking
        Route::get('/bookings', [BarberBookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/confirm', [BarberBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/reject', [BarberBookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/start', [BarberBookingController::class, 'start'])->name('bookings.start');
        Route::patch('/bookings/{booking}/complete', [BarberBookingController::class, 'complete'])->name('bookings.complete');
    });
