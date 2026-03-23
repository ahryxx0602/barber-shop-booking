<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case VNPay = 'vnpay';
    case Momo = 'momo';

    /**
     * Tên hiển thị tiếng Việt cho giao diện.
     */
    public function label(): string
    {
        return match ($this) {
            self::Cash  => 'Tiền mặt tại quán',
            self::VNPay => 'VNPay',
            self::Momo  => 'Ví MoMo',
        };
    }

    /**
     * Icon Material Symbols tương ứng.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Cash  => 'payments',
            self::VNPay => 'credit_card',
            self::Momo  => 'account_balance_wallet',
        };
    }
}
