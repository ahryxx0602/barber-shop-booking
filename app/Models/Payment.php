<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'paid_at',
    ];

    /**
     * Cast method & status sang Enum, paid_at sang datetime.
     */
    protected function casts(): array
    {
        return [
            'method'  => PaymentMethod::class,
            'status'  => PaymentStatus::class,
            'paid_at' => 'datetime',
            'amount'  => 'decimal:2',
        ];
    }

    // payments ──────────── bookings (1-1)
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
