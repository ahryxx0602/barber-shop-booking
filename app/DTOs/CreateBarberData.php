<?php

/**
 * DTO: CreateBarberData
 *
 * Đóng gói dữ liệu cần thiết để tạo thợ cắt mới.
 * Bao gồm thông tin user (name, email, password) và barber (bio, experience).
 * Thay thế array $data trong BarberService::create().
 *
 * Dùng bởi: Admin\BarberController::store() → BarberService::create()
 */

namespace App\DTOs;

use App\Http\Requests\Admin\StoreBarberRequest;

readonly class CreateBarberData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $phone = null,
        public ?string $bio = null,
        public int $experience_years = 0,
        public bool $is_active = true,
        public ?int $branch_id = null,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(StoreBarberRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            phone: $data['phone'] ?? null,
            bio: $data['bio'] ?? null,
            experience_years: (int) ($data['experience_years'] ?? 0),
            is_active: (bool) ($data['is_active'] ?? true),
            branch_id: isset($data['branch_id']) ? (int) $data['branch_id'] : null,
        );
    }
}
