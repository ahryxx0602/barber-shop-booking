<?php

namespace App\Models;

use App\Enums\TimeSlotStatus;
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

    protected function casts(): array
    {
        return [
            'status' => TimeSlotStatus::class,
            'slot_date' => 'date',
        ];
    }

    // time_slots ──────────── barbers (n-1)
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    // time_slots ──────────── bookings (1-1)
    public function booking()
    {
        return $this->hasOne(Booking::class);
    }
}
