<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Jobs\SendBookingNotificationJob;

class SendBookingConfirmedNotification
{
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;
        $booking->loadMissing(['customer', 'barber.user', 'services']);

        $message = "Lịch hẹn #{$booking->booking_code} đã được xác nhận bởi {$booking->barber->user->name}. "
                 . "Ngày: {$booking->booking_date->format('d/m/Y')}, "
                 . "Giờ: {$booking->start_time}.";

        // Dispatch job bất đồng bộ thay vì ghi trực tiếp
        SendBookingNotificationJob::dispatch($booking->customer_id, $message);
    }
}
