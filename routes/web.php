<?php

use App\Http\Controllers\Client\BarberController as ClientBarberController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Client-facing routes (public - no auth required for browsing)
Route::name('client.')->group(function () {
    Route::get('/barbers', [ClientBarberController::class, 'index'])->name('barbers.index');
    Route::get('/barbers/{barber}', [ClientBarberController::class, 'show'])->name('barbers.show');
    Route::get('/booking/slots', [ClientBookingController::class, 'getSlots'])->name('booking.slots');

    // Booking requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/booking/create', [ClientBookingController::class, 'create'])->name('booking.create');
        Route::post('/booking', [ClientBookingController::class, 'store'])->name('booking.store');
        Route::get('/booking/{booking}/confirmation', [ClientBookingController::class, 'confirmation'])->name('booking.confirmation');
    });
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return redirect(match ($role) {
        'admin'  => route('admin.dashboard'),
        'barber' => route('barber.dashboard'),
        default  => route('customer.dashboard'),
    });
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/customer.php';
require __DIR__.'/barber.php';
require __DIR__.'/admin.php';
