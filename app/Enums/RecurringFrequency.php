<?php

namespace App\Enums;

enum RecurringFrequency: string
{
    case None = 'none';
    case Weekly = 'weekly';
    case Biweekly = 'biweekly';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Không lặp',
            self::Weekly => 'Hàng tuần',
            self::Biweekly => 'Cách 2 tuần',
            self::Monthly => 'Hàng tháng',
        };
    }

    /**
     * Số ngày cách giữa các lần lặp.
     */
    public function daysInterval(): int
    {
        return match ($this) {
            self::None => 0,
            self::Weekly => 7,
            self::Biweekly => 14,
            self::Monthly => 28,
        };
    }
}
