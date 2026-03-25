<?php

namespace App\Models;

use App\Enums\CouponType;
use App\Enums\CouponAppliesTo;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'applies_to',
        'value',
        'min_amount',
        'max_discount',
        'expiry_date',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'applies_to' => CouponAppliesTo::class,
            'value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Kiểm tra coupon còn hiệu lực không.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expiry_date && $this->expiry_date->isPast()) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        return true;
    }
}
