<?php

namespace App\Enums;

enum OrderPaymentMethod: string
{
    case Cod = 'cod';
    case VNPay = 'vnpay';
    case Momo = 'momo';

    /**
     * Tên hiển thị tiếng Việt.
     */
    public function label(): string
    {
        return match ($this) {
            self::Cod => 'Thanh toán khi nhận hàng (COD)',
            self::VNPay => 'VNPay',
            self::Momo => 'Ví MoMo',
        };
    }
}
