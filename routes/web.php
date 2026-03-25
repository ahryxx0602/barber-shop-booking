<?php

use App\Enums\UserRole;
use App\Http\Controllers\Client\BarberController as ClientBarberController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\FavoriteBarberController as ClientFavoriteBarberController;
use App\Http\Controllers\Client\PaymentController as ClientPaymentController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\OrderPaymentController as ClientOrderPaymentController;
use App\Http\Controllers\Client\ShippingAddressController as ClientShippingAddressController;
use App\Http\Controllers\Client\ShopController as ClientShopController;
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
    Route::get('/branches', [\App\Http\Controllers\Client\BranchController::class, 'index'])->name('branches.index');
    Route::get('/booking/slots', [ClientBookingController::class, 'getSlots'])->name('booking.slots');

    // Shop — public (ai cũng xem được)
    Route::get('/shop', [ClientShopController::class, 'index'])->name('shop.index');
    Route::get('/shop/{product:slug}', [ClientShopController::class, 'show'])->name('shop.show');
    Route::get('/coupons', [ClientShopController::class, 'coupons'])->name('coupons');

    // Cart — session-based (không cần auth)
    Route::get('/cart', [ClientShopController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [ClientShopController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/update', [ClientShopController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove', [ClientShopController::class, 'removeFromCart'])->name('cart.remove');

    // Booking - accessible to both guests and authenticated users
    Route::post('/booking/apply-coupon', [ClientBookingController::class, 'applyCoupon'])->name('booking.apply-coupon');
    Route::get('/booking/create', [ClientBookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [ClientBookingController::class, 'store'])->middleware('throttle:5,1')->name('booking.store');
    Route::get('/booking/{booking}/confirmation', [ClientBookingController::class, 'confirmation'])->name('booking.confirmation');

    // Payment — callback routes từ cổng thanh toán (phải đặt TRƯỚC wildcard {booking})
    Route::get('/payment/vnpay/return', [ClientPaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
    Route::post('/payment/vnpay/ipn', [ClientPaymentController::class, 'vnpayIPN'])->withoutMiddleware(['csrf'])->name('payment.vnpay.ipn');
    Route::get('/payment/momo/return', [ClientPaymentController::class, 'momoReturn'])->name('payment.momo.return');

    // Order Payment — callback routes
    Route::get('/order-payment/vnpay/return', [ClientOrderPaymentController::class, 'vnpayReturn'])->name('order-payment.vnpay.return');
    Route::get('/order-payment/momo/return', [ClientOrderPaymentController::class, 'momoReturn'])->name('order-payment.momo.return');

    // Payment — chọn phương thức & thanh toán online (VNPay / Momo Sandbox)
    Route::get('/payment/{booking}', [ClientPaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{booking}', [ClientPaymentController::class, 'process'])->middleware('throttle:5,1')->name('payment.process');

    // Profile & booking management requires authentication
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/loyalty', [ClientProfileController::class, 'loyalty'])->name('profile.loyalty');
        Route::get('/profile/favorites', [ClientProfileController::class, 'favorites'])->name('profile.favorites');
        Route::patch('/booking/{booking}/cancel', [ClientBookingController::class, 'cancel'])->name('booking.cancel');
        Route::post('/reviews', [ClientReviewController::class, 'store'])->name('reviews.store');
        Route::post('/barbers/{barber}/favorite', [ClientFavoriteBarberController::class, 'toggle'])->name('barbers.favorite');
        Route::post('/waitlist', [ClientWaitlistController::class, 'store'])->name('waitlist.store');

        // Checkout & Orders (cần đăng nhập)
        Route::get('/checkout', [ClientShopController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/shipping-fee', [ClientShopController::class, 'getShippingFee'])->name('checkout.shipping-fee');
        Route::post('/checkout/apply-coupon', [ClientShopController::class, 'applyCoupon'])->name('checkout.apply-coupon');
        Route::post('/checkout/place-order', [ClientOrderController::class, 'placeOrder'])->name('order.place');
        Route::get('/order-success/{order}', [ClientOrderController::class, 'orderSuccess'])->name('shop.order-success');
        Route::get('/orders', [ClientOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [ClientOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/cancel', [ClientOrderController::class, 'cancel'])->name('orders.cancel');

        // Shipping Addresses (AJAX)
        Route::post('/addresses', [ClientShippingAddressController::class, 'store'])->name('addresses.store');
        Route::patch('/addresses/{address}/default', [ClientShippingAddressController::class, 'setDefault'])->name('addresses.default');
        Route::delete('/addresses/{address}', [ClientShippingAddressController::class, 'destroy'])->name('addresses.destroy');
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

    Route::get('/notifications/poll', function () {
        $unreadNotifications = auth()->user()->notifications()
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();

        return response()->json([
            'count' => $unreadCount,
            'html' => view('partials.notification-items', compact('unreadNotifications'))->render()
        ]);
    })->name('notifications.poll');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/barber.php';
require __DIR__ . '/admin.php';
