<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_id',
        'shipping_address_id',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'shipping_fee',
        'shipping_distance_km',
        'total_amount',
        'status',
        'note',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'shipping_distance_km' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    // orders ──────────── users (n-1) khách hàng đặt đơn
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // orders ──────────── order_items (1-n) danh sách sản phẩm trong đơn
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // orders ──────────── shipping_addresses (n-1) địa chỉ giao hàng
    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    // orders ──────────── order_payments (1-1) thông tin thanh toán
    public function payment()
    {
        return $this->hasOne(OrderPayment::class);
    }
}
