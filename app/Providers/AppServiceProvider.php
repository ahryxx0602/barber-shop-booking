<?php

namespace App\Providers;

use App\Events\BookingCancelled;
use App\Events\BookingCompleted;
use App\Events\BookingConfirmed;
use App\Listeners\SendBookingCancelledNotification;
use App\Listeners\SendBookingCompletedNotification;
use App\Listeners\SendBookingConfirmedNotification;
use Illuminate\Support\Facades\Event;
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
        Event::listen(BookingConfirmed::class, SendBookingConfirmedNotification::class);
        Event::listen(BookingCancelled::class, SendBookingCancelledNotification::class);
        Event::listen(BookingCompleted::class, SendBookingCompletedNotification::class);
    }
}
