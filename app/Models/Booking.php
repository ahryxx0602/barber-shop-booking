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
        'discount_amount',
        'coupon_code',
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
            'discount_amount' => 'decimal:2',
        ];
    }

    // bookings ──────────── users (n-1) khách hàng
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // bookings ──────────── barbers (n-1) thợ cắt
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    // bookings ──────────── time_slots (n-1) khung giờ
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    // bookings ──────────── services (n-m) qua pivot booking_services
    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_services')
                    ->withPivot('price_snapshot', 'duration_snapshot');
    }

    // bookings ──────────── payments (1-1) thanh toán
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // bookings ──────────── reviews (1-1) đánh giá
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
