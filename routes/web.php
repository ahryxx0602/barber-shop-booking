<?php

use App\Http\Controllers\Client\BarberController as ClientBarberController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Client-facing routes (public - no auth required for browsing)
Route::name('client.')->group(function () {
    Route::get('/barbers', [ClientBarberController::class, 'index'])->name('barbers.index');
    Route::get('/barbers/{barber}', [ClientBarberController::class, 'show'])->name('barbers.show');
    Route::get('/booking/slots', [ClientBookingController::class, 'getSlots'])->name('booking.slots');

    // Booking - accessible to both guests and authenticated users
    Route::get('/booking/create', [ClientBookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [ClientBookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}/confirmation', [ClientBookingController::class, 'confirmation'])->name('booking.confirmation');

    // Profile requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
    });
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return redirect(match ($role) {
        'admin' => route('admin.dashboard'),
        'barber' => route('barber.dashboard'),
        default => route('client.profile.show'),
    });
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile/breeze', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/breeze', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/breeze', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/barber.php';
require __DIR__ . '/admin.php';
