<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Models\Notification;

class SendBookingCancelledNotification
{
    public function handle(BookingCancelled $event): void
    {
        $booking = $event->booking;
        $booking->loadMissing(['customer', 'barber.user']);

        Notification::create([
            'user_id' => $booking->customer_id,
            'type' => 'booking_cancelled',
            'title' => 'Lich hen da huy',
            'message' => "Lich hen #{$booking->booking_code} da bi huy. "
                       . "Ly do: {$booking->cancel_reason}.",
        ]);

        Notification::create([
            'user_id' => $booking->barber->user_id,
            'type' => 'booking_cancelled',
            'title' => 'Lich hen da huy',
            'message' => "Lich hen #{$booking->booking_code} ngay {$booking->booking_date->format('d/m/Y')} "
                       . "luc {$booking->start_time} da bi huy.",
        ]);
    }
}
