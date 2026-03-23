<?php

namespace App\Models;

use App\Enums\WaitlistStatus;
use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'user_id',
        'barber_id',
        'desired_date',
        'desired_time',
        'status',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => WaitlistStatus::class,
            'desired_date' => 'date',
            'notified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
