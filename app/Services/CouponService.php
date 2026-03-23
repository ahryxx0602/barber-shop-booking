<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Models\Coupon;

class CouponService
{
    /**
     * Validate mã giảm giá và trả về Coupon nếu hợp lệ.
     *
     * @throws \InvalidArgumentException
     */
    public function validate(string $code, float $totalPrice): Coupon
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            throw new \InvalidArgumentException('Mã giảm giá không tồn tại.');
        }

        if (!$coupon->isValid()) {
            throw new \InvalidArgumentException('Mã giảm giá đã hết hiệu lực hoặc hết lượt sử dụng.');
        }

        if ($totalPrice < $coupon->min_amount) {
            throw new \InvalidArgumentException(
                'Đơn tối thiểu ' . number_format($coupon->min_amount, 0, ',', '.') . 'đ để sử dụng mã này.'
            );
        }

        return $coupon;
    }

    /**
     * Tính số tiền giảm giá.
     */
    public function calculateDiscount(Coupon $coupon, float $totalPrice): float
    {
        if ($coupon->type === CouponType::Fixed) {
            return min($coupon->value, $totalPrice);
        }

        // Percent
        $discount = $totalPrice * ($coupon->value / 100);

        // Áp dụng giới hạn giảm tối đa nếu có
        if ($coupon->max_discount !== null) {
            $discount = min($discount, $coupon->max_discount);
        }

        return min($discount, $totalPrice);
    }

    /**
     * Tăng lượt sử dụng coupon.
     */
    public function markUsed(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
