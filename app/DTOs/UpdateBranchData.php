<?php

/**
 * DTO: UpdateBranchData
 *
 * Đóng gói dữ liệu cần thiết để cập nhật chi nhánh.
 * Thay thế array $data trong BranchService::update().
 *
 * Dùng bởi: Admin\BranchController::update() → BranchService::update()
 */

namespace App\DTOs;

use App\Http\Requests\Admin\UpdateBranchRequest;

readonly class UpdateBranchData
{
    public function __construct(
        public string $name,
        public string $address,
        public ?string $phone = null,
        public ?string $description = null,
        public bool $is_active = true,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(UpdateBranchRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            address: $data['address'],
            phone: $data['phone'] ?? null,
            description: $data['description'] ?? null,
            is_active: (bool) ($data['is_active'] ?? true),
        );
    }
}
