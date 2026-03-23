<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'experience_years',
        'rating',
        'is_active',
    ];

    // users ──────────── barbers (1-1)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // barbers ─────────── working_schedules (1-n)
    public function workingSchedules()
    {
        return $this->hasMany(WorkingSchedule::class);
    }

    // barbers ─────────── time_slots (1-n)
    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    // barbers ─────────── bookings (1-n)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // barbers ─────────── reviews (1-n)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
