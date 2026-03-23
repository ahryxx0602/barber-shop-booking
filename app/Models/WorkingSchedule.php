<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingSchedule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'barber_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_day_off',
    ];

    // working_schedules ──────────── barbers (n-1)
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
