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

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_services')
                    ->withPivot('price_snapshot', 'duration_snapshot');
    }
}
