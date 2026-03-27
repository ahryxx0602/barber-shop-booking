<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Services\Client\WaitlistService;
use Illuminate\Support\Facades\Log;

class NotifyWaitlistOnCancel
{
    public function __construct(
        protected WaitlistService $waitlistService,
    ) {}

    public function handle(BookingCancelled $event): void
    {
        try {
            $booking = $event->booking;
            $this->waitlistService->notifyWaiters(
                $booking->barber_id,
                $booking->booking_date->format('Y-m-d'),
                $booking->start_time,
            );
        } catch (\Exception $e) {
            Log::error('Waitlist notification failed: ' . $e->getMessage());
        }
    }
}
