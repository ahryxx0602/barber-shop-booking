<?php

namespace App\Models;

use App\Enums\LoyaltyPointType;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'related_type',
        'related_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => LoyaltyPointType::class,
            'points' => 'integer',
        ];
    }

    // loyalty_points ──────────── users (n-1) khách hàng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic: liên kết tới Booking hoặc Coupon
    public function related()
    {
        return $this->morphTo();
    }
}
