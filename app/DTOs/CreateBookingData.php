<?php

/**
 * DTO: CreateBookingData
 *
 * Đóng gói dữ liệu cần thiết để tạo booking mới.
 * Thay thế array $data trong BookingService::create(),
 * giúp type-safe, IDE autocomplete, và dễ maintain hơn.
 *
 * Dùng bởi: Client\BookingController → BookingService::create()
 */

namespace App\DTOs;

use App\Http\Requests\Client\StoreBookingRequest;

readonly class CreateBookingData
{
    public function __construct(
        public int $barber_id,
        public int $time_slot_id,
        public array $service_ids,
        public ?string $note = null,
        public ?string $coupon_code = null,
        public ?string $guest_name = null,
        public ?string $guest_email = null,
        public ?string $guest_phone = null,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(StoreBookingRequest $request): self
    {
        $data = $request->validated();

        return new self(
            barber_id: $data['barber_id'],
            time_slot_id: $data['time_slot_id'],
            service_ids: $data['service_ids'],
            note: $data['note'] ?? null,
            coupon_code: $data['coupon_code'] ?? null,
            guest_name: $data['guest_name'] ?? null,
            guest_email: $data['guest_email'] ?? null,
            guest_phone: $data['guest_phone'] ?? null,
        );
    }
}
