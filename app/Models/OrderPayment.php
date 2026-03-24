<?php

namespace App\Models;

use App\Enums\OrderPaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'method' => OrderPaymentMethod::class,
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // order_payments ──────────── orders (1-1)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
