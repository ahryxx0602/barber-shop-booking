<?php

/**
 * DTO: UpdateServiceData
 *
 * Đóng gói dữ liệu cần thiết để cập nhật dịch vụ.
 * Thay thế array $data trong ServiceService::update().
 *
 * Dùng bởi: Admin\ServiceController::update() → ServiceService::update()
 */

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\UpdateServiceRequest;

readonly class UpdateServiceData
{
    public function __construct(
        public string $name,
        public float $price,
        public int $duration_minutes,
        public ?string $description = null,
        public bool $is_active = true,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(UpdateServiceRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            duration_minutes: (int) $data['duration_minutes'],
            description: $data['description'] ?? null,
            is_active: (bool) ($data['is_active'] ?? true),
        );
    }

    /**
     * Chuyển DTO thành array để lưu vào DB.
     */
    public function toArray(): array
    {
        return [
            'name'             => $this->name,
            'description'      => $this->description,
            'price'            => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'is_active'        => $this->is_active,
        ];
    }
}
