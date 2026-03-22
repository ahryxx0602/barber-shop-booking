<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Barber = 'barber';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Quản trị viên',
            self::Barber => 'Thợ cắt tóc',
            self::Customer => 'Khách hàng',
        };
    }
}
