<?php

use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:customer,admin'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    });