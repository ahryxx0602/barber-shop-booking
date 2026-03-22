<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Models\Notification;

class SendBookingConfirmedNotification
{
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;
        $booking->loadMissing(['customer', 'barber.user', 'services']);

        Notification::create([
            'user_id' => $booking->customer_id,
            'type' => 'booking_confirmed',
            'title' => 'Lich hen da xac nhan',
            'message' => "Lich hen #{$booking->booking_code} da duoc xac nhan boi {$booking->barber->user->name}. "
                       . "Ngay: {$booking->booking_date->format('d/m/Y')}, "
                       . "Gio: {$booking->start_time}.",
        ]);
    }
}
