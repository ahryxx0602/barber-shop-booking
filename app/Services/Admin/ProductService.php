<?php

namespace App\Services\Admin;

use App\DTOs\Admin\CreateProductData;
use App\DTOs\Admin\UpdateProductData;
use App\Models\Product;
use App\Repositories\Contracts\Admin\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepo,
    ) {}

    /**
     * Lấy danh sách sản phẩm có phân trang, filter category, search.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepo->paginateWithFilters($filters, $perPage);
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

        return $this->productRepo->create($productData);
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

        return $this->productRepo->update($product, $productData);
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

        return $this->productRepo->delete($product);
    }

    /**
     * Giảm tồn kho khi đặt hàng (dùng lockForUpdate tránh race condition).
     */
    public function decreaseStock(int $productId, int $quantity): void
    {
        $this->productRepo->decreaseStock($productId, $quantity);
    }

    /**
     * Hoàn tồn kho khi hủy đơn hàng.
     */
    public function increaseStock(int $productId, int $quantity): void
    {
        $this->productRepo->increaseStock($productId, $quantity);
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
