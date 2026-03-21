<?php

use App\Http\Controllers\Barber\DashboardController as BarberDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:barber,admin'])
    ->prefix('barber')
    ->name('barber.')
    ->group(function () {
        Route::get('/dashboard', [BarberDashboardController::class, 'index'])->name('dashboard');
    });
