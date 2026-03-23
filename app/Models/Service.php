<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_minutes',
        'image',
        'is_active',
    ];

    // services ──────────── bookings (n-m) qua pivot booking_services
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_services')
                    ->withPivot('price_snapshot', 'duration_snapshot');
    }
}
