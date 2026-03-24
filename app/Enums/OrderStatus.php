<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Shipping = 'shipping';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    /**
     * Tên hiển thị tiếng Việt.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xác nhận',
            self::Confirmed => 'Đã xác nhận',
            self::Shipping => 'Đang giao hàng',
            self::Delivered => 'Đã giao',
            self::Cancelled => 'Đã hủy',
        };
    }

    /**
     * Màu Tailwind tương ứng cho badge UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Confirmed => 'blue',
            self::Shipping => 'indigo',
            self::Delivered => 'green',
            self::Cancelled => 'red',
        };
    }

    /**
     * FSM guard: kiểm tra chuyển trạng thái hợp lệ.
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($target, [self::Shipping, self::Cancelled]),
            self::Shipping => $target === self::Delivered,
            self::Delivered, self::Cancelled => false,
        };
    }
}
