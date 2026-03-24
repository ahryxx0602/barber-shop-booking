<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'barber_id',
        'booking_id',
        'booking_amount',
        'commission_rate',
        'commission_amount',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'booking_amount'    => 'decimal:2',
            'commission_rate'   => 'decimal:2',
            'commission_amount' => 'decimal:2',
        ];
    }

    // commissions ──────────── barbers (n-1) thợ cắt
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    // commissions ──────────── bookings (n-1) booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
