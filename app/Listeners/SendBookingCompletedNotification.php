<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Models\Notification;

class SendBookingCompletedNotification
{
    public function handle(BookingCompleted $event): void
    {
        $booking = $event->booking;
        $booking->loadMissing(['customer', 'barber.user']);

        Notification::create([
            'user_id' => $booking->customer_id,
            'type' => 'booking_completed',
            'title' => 'Lich hen hoan thanh',
            'message' => "Lich hen #{$booking->booking_code} da hoan thanh. "
                       . "Cam on ban da su dung dich vu! "
                       . "Hay de lai danh gia cho tho cat toc nhe.",
        ]);
    }
}
