<?php

/**
 * DTO: UpdateBarberData
 *
 * Đóng gói dữ liệu cần thiết để cập nhật thợ cắt.
 * Khác với CreateBarberData: password là optional (chỉ đổi khi có).
 * Thay thế array $data trong BarberService::update().
 *
 * Dùng bởi: Admin\BarberController::update() → BarberService::update()
 */

namespace App\DTOs;

use App\Http\Requests\Admin\UpdateBarberRequest;

readonly class UpdateBarberData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public ?string $phone = null,
        public ?string $bio = null,
        public int $experience_years = 0,
        public bool $is_active = false,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(UpdateBarberRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            phone: $data['phone'] ?? null,
            bio: $data['bio'] ?? null,
            experience_years: (int) ($data['experience_years'] ?? 0),
            is_active: (bool) ($data['is_active'] ?? false),
        );
    }
}
