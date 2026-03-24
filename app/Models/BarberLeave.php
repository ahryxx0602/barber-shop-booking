<?php

namespace App\Models;

use App\Enums\LeaveStatus;
use Illuminate\Database\Eloquent\Model;

class BarberLeave extends Model
{
    protected $fillable = [
        'barber_id',
        'leave_date',
        'type',
        'start_time',
        'end_time',
        'reason',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'leave_date'  => 'date',
            'status'      => LeaveStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    // barber_leaves ──────────── barbers (n-1)
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    // barber_leaves ──────────── users (n-1) admin đã duyệt
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
