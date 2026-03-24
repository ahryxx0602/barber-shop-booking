<?php

namespace App\Services;

use App\DTOs\CreateProductData;
use App\DTOs\UpdateProductData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Lấy danh sách sản phẩm có phân trang, filter category, search.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query();

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

    /**
     * Tạo sản phẩm mới + upload ảnh + auto slug.
     */
    public function create(CreateProductData $data, ?UploadedFile $image = null): Product
    {
        $productData = [
            'name'           => $data->name,
            'slug'           => Str::slug($data->name),
            'description'    => $data->description,
            'price'          => $data->price,
            'stock_quantity' => $data->stock_quantity,
            'sku'            => $data->sku ?? $this->generateSku(),
            'category'       => $data->category,
            'is_active'      => $data->is_active,
        ];

        if ($image) {
            $productData['image'] = $image->store('products', 'public');
        }

        // Đảm bảo slug unique
        $productData['slug'] = $this->uniqueSlug($productData['slug']);

        return Product::create($productData);
    }

    /**
     * Cập nhật sản phẩm + thay ảnh nếu có.
     */
    public function update(Product $product, UpdateProductData $data, ?UploadedFile $image = null): Product
    {
        $productData = [
            'name'           => $data->name,
            'slug'           => Str::slug($data->name),
            'description'    => $data->description,
            'price'          => $data->price,
            'stock_quantity' => $data->stock_quantity,
            'sku'            => $data->sku ?? $product->sku,
            'category'       => $data->category,
            'is_active'      => $data->is_active,
        ];

        if ($image) {
            // Xóa ảnh cũ
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $productData['image'] = $image->store('products', 'public');
        }

        // Đảm bảo slug unique (bỏ qua chính nó)
        if ($productData['slug'] !== $product->slug) {
            $productData['slug'] = $this->uniqueSlug($productData['slug'], $product->id);
        }

        $product->update($productData);

        return $product->fresh();
    }

    /**
     * Xóa sản phẩm — chỉ khi chưa có order nào chứa SP này.
     */
    public function delete(Product $product): bool
    {
        // Check xem SP đã có trong đơn hàng chưa
        if ($product->orderItems()->exists()) {
            throw new \InvalidArgumentException(
                'Không thể xóa sản phẩm đã có trong đơn hàng. Hãy ẩn sản phẩm thay vì xóa.'
            );
        }

        // Xóa ảnh
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return $product->delete();
    }

    /**
     * Giảm tồn kho khi đặt hàng (dùng lockForUpdate tránh race condition).
     */
    public function decreaseStock(int $productId, int $quantity): void
    {
        $product = Product::lockForUpdate()->findOrFail($productId);

        if ($product->stock_quantity < $quantity) {
            throw new \InvalidArgumentException(
                "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity} trong kho."
            );
        }

        $product->decrement('stock_quantity', $quantity);
    }

    /**
     * Hoàn tồn kho khi hủy đơn hàng.
     */
    public function increaseStock(int $productId, int $quantity): void
    {
        Product::where('id', $productId)->increment('stock_quantity', $quantity);
    }

    /**
     * Tạo SKU tự động.
     */
    protected function generateSku(): string
    {
        return 'SP-' . strtoupper(Str::random(8));
    }

    /**
     * Đảm bảo slug unique.
     */
    protected function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $query = Product::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if (!$query->exists()) {
            return $slug;
        }

        $count = 1;
        while (Product::where('slug', "{$slug}-{$count}")->exists()) {
            $count++;
        }

        return "{$slug}-{$count}";
    }
}
