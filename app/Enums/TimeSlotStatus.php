<?php

namespace App\Enums;

enum TimeSlotStatus: string
{
    case Available = 'available';
    case Booked = 'booked';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Trống',
            self::Booked => 'Đã đặt',
        };
    }
}
