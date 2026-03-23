<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Pivot table: bookings ──── n:m ──── services
class BookingService extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'service_id',
        'price_snapshot',
        'duration_snapshot',
    ];
}
