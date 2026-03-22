<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'customer_id',
        'barber_id',
        'time_slot_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_price',
        'status',
        'note',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'booking_date' => 'date',
            'cancelled_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_services')
                    ->withPivot('price_snapshot', 'duration_snapshot');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
