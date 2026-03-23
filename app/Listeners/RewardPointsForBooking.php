<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Log;

class RewardPointsForBooking
{
    public function __construct(
        protected LoyaltyService $loyaltyService,
    ) {}

    public function handle(BookingCompleted $event): void
    {
        $booking = $event->booking;

        try {
            $point = $this->loyaltyService->rewardForBooking($booking);

            if ($point) {
                Log::channel('booking')->info('Loyalty points awarded', [
                    'booking_code' => $booking->booking_code,
                    'customer_id' => $booking->customer_id,
                    'points' => $point->points,
                ]);
            }
        } catch (\Throwable $e) {
            // Không để lỗi loyalty ảnh hưởng flow chính
            Log::error('Failed to award loyalty points', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
