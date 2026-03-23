<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'customer_id',
        'barber_id',
        'rating',
        'comment',
    ];

    // reviews ──────────── bookings (1-1)
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // reviews ──────────── users (n-1) khách đánh giá
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // reviews ──────────── barbers (n-1) thợ được đánh giá
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
