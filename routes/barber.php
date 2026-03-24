<?php

use App\Http\Controllers\Barber\BookingController as BarberBookingController;
use App\Http\Controllers\Barber\CommissionController as BarberCommissionController;
use App\Http\Controllers\Barber\DashboardController as BarberDashboardController;
use App\Http\Controllers\Barber\LeaveController;
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

        // Quản lý ngày nghỉ
        Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
        Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

        // Hoa hồng cá nhân
        Route::get('/commissions', [BarberCommissionController::class, 'index'])->name('commissions.index');

        // Quan ly booking
        Route::get('/bookings/calendar', [BarberBookingController::class, 'calendar'])->name('bookings.calendar');
        Route::get('/bookings/events', [BarberBookingController::class, 'events'])->name('bookings.events');
        Route::get('/bookings', [BarberBookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/confirm', [BarberBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/reject', [BarberBookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/start', [BarberBookingController::class, 'start'])->name('bookings.start');
        Route::patch('/bookings/{booking}/complete', [BarberBookingController::class, 'complete'])->name('bookings.complete');
    });

