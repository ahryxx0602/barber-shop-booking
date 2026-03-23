<?php

use App\Enums\UserRole;
use App\Http\Controllers\Client\BarberController as ClientBarberController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\FavoriteBarberController as ClientFavoriteBarberController;
use App\Http\Controllers\Client\PaymentController as ClientPaymentController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\Client\WaitlistController as ClientWaitlistController;
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
    Route::post('/booking', [ClientBookingController::class, 'store'])->middleware('throttle:5,1')->name('booking.store');
    Route::get('/booking/{booking}/confirmation', [ClientBookingController::class, 'confirmation'])->name('booking.confirmation');

    // Payment — callback routes từ cổng thanh toán (phải đặt TRƯỚC wildcard {booking})
    Route::get('/payment/vnpay/return', [ClientPaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
    Route::post('/payment/vnpay/ipn', [ClientPaymentController::class, 'vnpayIPN'])->withoutMiddleware(['csrf'])->name('payment.vnpay.ipn');
    Route::get('/payment/momo/return', [ClientPaymentController::class, 'momoReturn'])->name('payment.momo.return');

    // Payment — chọn phương thức & thanh toán online (VNPay / Momo Sandbox)
    Route::get('/payment/{booking}', [ClientPaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{booking}', [ClientPaymentController::class, 'process'])->middleware('throttle:5,1')->name('payment.process');

    // Profile & booking management requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/loyalty', [ClientProfileController::class, 'loyalty'])->name('profile.loyalty');
        Route::patch('/booking/{booking}/cancel', [ClientBookingController::class, 'cancel'])->name('booking.cancel');
        Route::post('/reviews', [ClientReviewController::class, 'store'])->name('reviews.store');
        Route::post('/barbers/{barber}/favorite', [ClientFavoriteBarberController::class, 'toggle'])->name('barbers.favorite');
        Route::post('/waitlist', [ClientWaitlistController::class, 'store'])->name('waitlist.store');
    });
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return redirect(match ($role) {
        UserRole::Admin => route('admin.dashboard'),
        UserRole::Barber => route('barber.dashboard'),
        default => route('client.profile.show'),
    });
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:admin,barber'])->group(function () {
    Route::get('/profile/breeze', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/breeze', [ProfileController::class, 'update'])->name('profile.update');
});

// Notifications - all authenticated users
Route::middleware('auth')->group(function () {
    Route::patch('/notifications/read-all', function () {
        auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        return back();
    })->name('notifications.read-all');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/barber.php';
require __DIR__ . '/admin.php';
