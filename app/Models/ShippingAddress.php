<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'address',
        'ward',
        'district',
        'city',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_default' => 'boolean',
        ];
    }

    // shipping_addresses ──────────── users (n-1)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // shipping_addresses ──────────── orders (1-n) đơn hàng sử dụng địa chỉ này
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
