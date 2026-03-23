<?php

namespace App\Enums;

enum LoyaltyPointType: string
{
    case Earn = 'earn';
    case Spend = 'spend';

    public function label(): string
    {
        return match ($this) {
            self::Earn => 'Tích điểm',
            self::Spend => 'Tiêu điểm',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Earn => 'green',
            self::Spend => 'red',
        };
    }
}
