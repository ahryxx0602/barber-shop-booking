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
            'message' => "Lịch hẹn #{$booking->booking_code} đã bị hủy. "
                       . "Lý do: {$booking->cancel_reason}.",
        ]);

        Notification::create([
            'user_id' => $booking->barber->user_id,
            'message' => "Lịch hẹn #{$booking->booking_code} ngày {$booking->booking_date->format('d/m/Y')} "
                       . "lúc {$booking->start_time} đã bị hủy.",
        ]);
    }
}
