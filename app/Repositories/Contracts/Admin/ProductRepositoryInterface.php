<?php

namespace App\Repositories\Contracts\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Phân trang sản phẩm với filter category, search, is_active.
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Giảm tồn kho (dùng lockForUpdate tránh race condition).
     */
    public function decreaseStock(int $productId, int $quantity): void;

    /**
     * Hoàn tồn kho khi hủy đơn.
     */
    public function increaseStock(int $productId, int $quantity): void;

    /**
     * Tìm sản phẩm theo slug.
     */
    public function findBySlug(string $slug): ?\Illuminate\Database\Eloquent\Model;
}
