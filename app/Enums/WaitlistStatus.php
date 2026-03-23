<?php

namespace App\Enums;

enum WaitlistStatus: string
{
    case Waiting = 'waiting';
    case Notified = 'notified';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Đang chờ',
            self::Notified => 'Đã thông báo',
            self::Expired => 'Hết hạn',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Waiting => 'yellow',
            self::Notified => 'green',
            self::Expired => 'gray',
        };
    }
}
