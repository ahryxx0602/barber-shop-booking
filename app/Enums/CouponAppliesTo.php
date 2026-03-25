<?php

namespace App\Enums;

enum CouponAppliesTo: string
{
    case Product = 'product';
    case Shipping = 'shipping';
    case Booking = 'booking';

    public function label(): string
    {
        return match ($this) {
            self::Product => 'Sản phẩm',
            self::Shipping => 'Phí vận chuyển',
            self::Booking => 'Đặt lịch cắt tóc',
        };
    }
}
