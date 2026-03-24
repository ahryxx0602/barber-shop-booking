<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    // order_items ──────────── orders (n-1)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // order_items ──────────── products (n-1)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
