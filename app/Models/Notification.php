<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
    ];

    // notifications ──────────── users (n-1)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
