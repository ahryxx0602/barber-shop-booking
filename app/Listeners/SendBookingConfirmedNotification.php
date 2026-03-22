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
            'message' => "Lịch hẹn #{$booking->booking_code} đã được xác nhận bởi {$booking->barber->user->name}. "
                       . "Ngày: {$booking->booking_date->format('d/m/Y')}, "
                       . "Giờ: {$booking->start_time}.",
        ]);
    }
}
