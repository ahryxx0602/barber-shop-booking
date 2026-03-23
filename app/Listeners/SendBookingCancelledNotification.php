<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Jobs\SendBookingNotificationJob;

class SendBookingCancelledNotification
{
    public function handle(BookingCancelled $event): void
    {
        $booking = $event->booking;
        $booking->loadMissing(['customer', 'barber.user']);

        // Thông báo cho khách hàng
        $customerMessage = "Lịch hẹn #{$booking->booking_code} đã bị huỷ. "
                         . "Lý do: {$booking->cancel_reason}.";

        SendBookingNotificationJob::dispatch($booking->customer_id, $customerMessage);

        // Thông báo cho thợ cắt
        $barberMessage = "Lịch hẹn #{$booking->booking_code} ngày {$booking->booking_date->format('d/m/Y')} "
                       . "lúc {$booking->start_time} đã bị huỷ.";

        SendBookingNotificationJob::dispatch($booking->barber->user_id, $barberMessage);
    }
}
