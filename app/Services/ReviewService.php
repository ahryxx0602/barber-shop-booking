<?php

namespace App\Services;

use App\DTOs\Client\StoreReviewData;
use App\Enums\BookingStatus;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Repositories\Contracts\Client\ReviewRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function __construct(
        protected ReviewRepositoryInterface $reviewRepo,
    ) {}

    public function store(StoreReviewData $data, User $customer): Review
    {
        return DB::transaction(function () use ($data, $customer) {
            $booking = Booking::with('review')->findOrFail($data->booking_id);

            abort_if($booking->customer_id !== $customer->id, 403);
            abort_if($booking->status !== BookingStatus::Completed, 422, 'Chỉ có thể đánh giá booking đã hoàn thành.');
            abort_if($booking->review !== null, 422, 'Booking này đã được đánh giá.');

            $review = $this->reviewRepo->create([
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'barber_id' => $booking->barber_id,
                'rating' => $data->rating,
                'comment' => $data->comment,
            ]);

            $this->updateBarberRating($booking->barber_id);

            return $review;
        });
    }

    protected function updateBarberRating(int $barberId): void
    {
        $avgRating = $this->reviewRepo->getAverageRatingForBarber($barberId);

        Barber::where('id', $barberId)->update([
            'rating' => round($avgRating, 2),
        ]);
    }
}
