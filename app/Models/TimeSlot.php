<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = [
        'barber_id',
        'slot_date',
        'start_time',
        'end_time',
        'status',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
    }
}
