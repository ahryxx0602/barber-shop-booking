<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Failed   = 'failed';
    case Refunded = 'refunded';

    /**
     * Tên hiển thị tiếng Việt.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Chờ thanh toán',
            self::Paid     => 'Đã thanh toán',
            self::Failed   => 'Thất bại',
            self::Refunded => 'Đã hoàn tiền',
        };
    }

    /**
     * Màu Tailwind tương ứng cho UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending  => 'yellow',
            self::Paid     => 'green',
            self::Failed   => 'red',
            self::Refunded => 'gray',
        };
    }
}
