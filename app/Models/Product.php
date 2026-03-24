<?php

namespace App\Models;

use App\Enums\ProductCategory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image',
        'stock_quantity',
        'sku',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'category' => ProductCategory::class,
            'is_active' => 'boolean',
        ];
    }

    // products ──────────── order_items (1-n) sản phẩm trong đơn hàng
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope: chỉ sản phẩm đang bán.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: lọc theo danh mục.
     */
    public function scopeByCategory($query, ProductCategory $category)
    {
        return $query->where('category', $category->value);
    }
}
