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
            'message' => "Lịch hẹn #{$booking->booking_code} đã hoàn thành. "
                       . "Cảm ơn bạn đã sử dụng dịch vụ! "
                       . "Hãy để lại đánh giá cho thợ cắt tóc nhé.",
        ]);
    }
}
