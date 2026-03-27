<?php

namespace App\Repositories\Eloquent\Admin;

use App\Repositories\Eloquent\BaseRepository;
use App\Models\Product;
use App\Repositories\Contracts\Admin\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Filter theo category
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Filter theo trạng thái active
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function decreaseStock(int $productId, int $quantity): void
    {
        $product = $this->model->lockForUpdate()->findOrFail($productId);

        if ($product->stock_quantity < $quantity) {
            throw new \InvalidArgumentException(
                "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity} trong kho."
            );
        }

        $product->decrement('stock_quantity', $quantity);
    }

    public function increaseStock(int $productId, int $quantity): void
    {
        $this->model->where('id', $productId)->increment('stock_quantity', $quantity);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->model->where('slug', $slug)->first();
    }
}
