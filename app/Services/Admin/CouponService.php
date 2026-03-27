<?php

namespace App\Services\Admin;

use App\DTOs\Admin\CreateCouponData;
use App\DTOs\Admin\UpdateCouponData;
use App\Enums\CouponAppliesTo;
use App\Enums\CouponType;
use App\Models\Coupon;
use App\Repositories\Contracts\Admin\CouponRepositoryInterface;

class CouponService
{
    public function __construct(
        private CouponRepositoryInterface $couponRepo,
    ) {}

    // ──────────────────── CRUD (mới) ────────────────────

    /**
     * Tạo mã giảm giá mới.
     */
    public function createCoupon(CreateCouponData $data): Coupon
    {
        return $this->couponRepo->create($data->toArray());
    }

    /**
     * Cập nhật mã giảm giá.
     */
    public function updateCoupon(Coupon $coupon, UpdateCouponData $data): Coupon
    {
        return $this->couponRepo->update($coupon, $data->toArray());
    }

    /**
     * Xóa mã giảm giá.
     */
    public function deleteCoupon(Coupon $coupon): bool
    {
        return $this->couponRepo->delete($coupon);
    }

    // ──────────────────── Business Logic (giữ nguyên) ────────────────────

    /**
     * Validate mã giảm giá và trả về Coupon nếu hợp lệ.
     *
     * @throws \InvalidArgumentException
     */
    public function validate(string $code, float $totalPrice, ?CouponAppliesTo $appliesTo = null): Coupon
    {
        $coupon = $this->couponRepo->findByCode($code);

        if (!$coupon) {
            throw new \InvalidArgumentException('Mã giảm giá không tồn tại.');
        }

        if (!$coupon->isValid()) {
            throw new \InvalidArgumentException('Mã giảm giá đã hết hiệu lực hoặc hết lượt sử dụng.');
        }

        // Kiểm tra loại áp dụng
        if ($appliesTo !== null && $coupon->applies_to !== $appliesTo) {
            $label = $coupon->applies_to->label();
            throw new \InvalidArgumentException("Mã này chỉ áp dụng cho: {$label}.");
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
     * Tăng lượt sử dụng coupon (atomic với conditional WHERE).
     *
     * @throws \InvalidArgumentException nếu coupon đã hết lượt sử dụng
     */
    public function markUsed(Coupon $coupon): void
    {
        $affected = $this->couponRepo->atomicIncrementUsedCount($coupon->id);

        if ($affected === 0) {
            throw new \InvalidArgumentException('Mã giảm giá đã hết lượt sử dụng.');
        }
    }
}
