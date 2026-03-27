<?php

/**
 * DTO: CreateProductData
 *
 * Đóng gói dữ liệu cần thiết để tạo sản phẩm mới.
 * Image (UploadedFile) truyền riêng qua parameter giống pattern BarberService.
 *
 * Dùng bởi: Admin\ProductController::store() → ProductService::create()
 */

namespace App\DTOs\Admin;

use App\Enums\ProductCategory;
use Illuminate\Http\Request;

readonly class CreateProductData
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public float $price = 0,
        public int $stock_quantity = 0,
        public ?string $sku = null,
        public ProductCategory $category = ProductCategory::Other,
        public bool $is_active = true,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (float) ($data['price'] ?? 0),
            stock_quantity: (int) ($data['stock_quantity'] ?? 0),
            sku: $data['sku'] ?? null,
            category: ProductCategory::from($data['category'] ?? 'other'),
            is_active: (bool) ($data['is_active'] ?? true),
        );
    }
}
