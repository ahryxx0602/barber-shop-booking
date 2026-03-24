<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Jobs\SendBookingNotificationJob;

class SendBookingCompletedNotification
{
    public function handle(BookingCompleted $event): void
    {
        $booking = $event->booking;
        $booking->loadMissing(['customer', 'barber.user']);

        $message = "Lịch hẹn #{$booking->booking_code} đã hoàn thành. "
                 . "Cảm ơn bạn đã sử dụng dịch vụ! "
                 . "Hãy để lại đánh giá cho thợ cắt tóc nhé.";

        SendBookingNotificationJob::dispatch(
            $booking->customer_id, 
            $message,
            'Lịch hẹn hoàn thành',
            'booking_completed'
        );

        if ($booking->customer && $booking->customer->email) {
            \Illuminate\Support\Facades\Mail::to($booking->customer->email)
                ->queue(new \App\Mail\BookingCompletedMail($booking));
        }
    }
}
