<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Jobs\SendBookingNotificationJob;
use App\Mail\BookingConfirmedMail;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmedNotification
{
    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;
        $booking->loadMissing(['customer', 'barber.user', 'services']);

        $message = "Lịch hẹn #{$booking->booking_code} đã được xác nhận bởi {$booking->barber->user->name}. "
                 . "Ngày: {$booking->booking_date->format('d/m/Y')}, "
                 . "Giờ: {$booking->start_time}.";

        // Dispatch job gửi thông báo vào database
        SendBookingNotificationJob::dispatch(
            $booking->customer_id, 
            $message, 
            'Lịch hẹn đã xác nhận', 
            'booking_confirmed'
        );

        // Gửi email xác nhận cho khách hàng
        if ($booking->customer && $booking->customer->email) {
            Mail::to($booking->customer->email)->queue(new BookingConfirmedMail($booking));
        }
    }
}
