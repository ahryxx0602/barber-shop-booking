<?php

namespace App\Providers;

use App\Events\BookingCancelled;
use App\Events\BookingCompleted;
use App\Events\BookingConfirmed;
use App\Listeners\NotifyWaitlistOnCancel;
use App\Listeners\RewardPointsForBooking;
use App\Listeners\SendBookingCancelledNotification;
use App\Listeners\SendBookingCompletedNotification;
use App\Listeners\SendBookingConfirmedNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Event listeners cho booking lifecycle
        Event::listen(BookingConfirmed::class, SendBookingConfirmedNotification::class);
        Event::listen(BookingCancelled::class, SendBookingCancelledNotification::class);
        Event::listen(BookingCancelled::class, NotifyWaitlistOnCancel::class);
        Event::listen(BookingCompleted::class, SendBookingCompletedNotification::class);
        Event::listen(BookingCompleted::class, RewardPointsForBooking::class);

        // Rate limiting
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
