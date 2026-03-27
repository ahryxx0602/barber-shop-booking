<?php

/**
 * DTO: ShippingAddressData
 *
 * Đóng gói dữ liệu địa chỉ giao hàng.
 * Dùng cho cả tạo mới và cập nhật shipping address.
 *
 * Dùng bởi: Client\ShippingAddressController → ShippingAddress::create()
 */

namespace App\DTOs\Client;

use Illuminate\Http\Request;

readonly class ShippingAddressData
{
    public function __construct(
        public string $recipient_name,
        public string $phone,
        public string $address,
        public ?string $ward = null,
        public ?string $district = null,
        public ?string $city = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public bool $is_default = false,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            recipient_name: $data['recipient_name'],
            phone: $data['phone'],
            address: $data['address'],
            ward: $data['ward'] ?? null,
            district: $data['district'] ?? null,
            city: $data['city'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            is_default: (bool) ($data['is_default'] ?? false),
        );
    }

    /**
     * Tạo DTO từ array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            recipient_name: $data['recipient_name'],
            phone: $data['phone'],
            address: $data['address'],
            ward: $data['ward'] ?? null,
            district: $data['district'] ?? null,
            city: $data['city'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            is_default: (bool) ($data['is_default'] ?? false),
        );
    }
}
