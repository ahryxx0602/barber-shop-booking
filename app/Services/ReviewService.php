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
     * Tạo review cho booking đã hoàn thành.
     * Cập nhật rating trung bình của barber.
     */
    public function store(array $data, User $customer): Review
    {
        return DB::transaction(function () use ($data, $customer) {
            $booking = Booking::findOrFail($data['booking_id']);

            abort_if($booking->customer_id !== $customer->id, 403);
            abort_if($booking->status !== BookingStatus::Completed, 422, 'Chỉ có thể đánh giá booking đã hoàn thành.');
            abort_if($booking->review !== null, 422, 'Booking này đã được đánh giá.');

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
     * Tính lại rating trung bình của barber từ tất cả reviews.
     */
    protected function updateBarberRating(int $barberId): void
    {
        $avgRating = Review::where('barber_id', $barberId)->avg('rating');

        Barber::where('id', $barberId)->update([
            'rating' => round($avgRating, 2),
        ]);
    }
}
