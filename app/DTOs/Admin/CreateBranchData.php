<?php

/**
 * DTO: CreateBranchData
 *
 * Đóng gói dữ liệu cần thiết để tạo chi nhánh mới.
 * Thay thế array $data trong BranchService::create().
 *
 * Dùng bởi: Admin\BranchController::store() → BranchService::create()
 */

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\StoreBranchRequest;

readonly class CreateBranchData
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
    public static function fromRequest(StoreBranchRequest $request): self
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
