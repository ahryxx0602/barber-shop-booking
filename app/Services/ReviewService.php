<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Tao review cho booking da hoan thanh.
     * Cap nhat rating trung binh cua barber.
     */
    public function store(array $data, User $customer): Review
    {
        return DB::transaction(function () use ($data, $customer) {
            $booking = Booking::findOrFail($data['booking_id']);

            abort_if($booking->customer_id !== $customer->id, 403);
            abort_if($booking->status !== BookingStatus::Completed, 422, 'Chi co the danh gia booking da hoan thanh.');
            abort_if($booking->review !== null, 422, 'Booking nay da duoc danh gia.');

            $review = Review::create([
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'barber_id' => $booking->barber_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            $this->updateBarberRating($booking->barber_id);

            return $review;
        });
    }

    /**
     * Tinh lai rating trung binh cua barber tu tat ca reviews.
     */
    protected function updateBarberRating(int $barberId): void
    {
        $avgRating = Review::where('barber_id', $barberId)->avg('rating');

        Barber::where('id', $barberId)->update([
            'rating' => round($avgRating, 2),
        ]);
    }
}
