<?php

/**
 * DTO: StoreReviewData
 *
 * Đóng gói dữ liệu cần thiết để tạo review cho booking.
 * Bao gồm booking_id, rating (1-5), và comment (optional).
 * Thay thế array $data trong ReviewService::store().
 *
 * Dùng bởi: Client\ReviewController::store() → ReviewService::store()
 */

namespace App\DTOs;

use App\Http\Requests\Client\StoreReviewRequest;

readonly class StoreReviewData
{
    public function __construct(
        public int $booking_id,
        public int $rating,
        public ?string $comment = null,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(StoreReviewRequest $request): self
    {
        $data = $request->validated();

        return new self(
            booking_id: (int) $data['booking_id'],
            rating: (int) $data['rating'],
            comment: $data['comment'] ?? null,
        );
    }
}
