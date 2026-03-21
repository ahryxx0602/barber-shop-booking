<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
