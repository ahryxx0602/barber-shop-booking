<?php

namespace App\Services;

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

    // ──────────────────── CRUD ────────────────────

    public function createCoupon(CreateCouponData $data): Coupon
    {
        return $this->couponRepo->create($data->toArray());
    }

    public function updateCoupon(Coupon $coupon, UpdateCouponData $data): Coupon
    {
        return $this->couponRepo->update($coupon, $data->toArray());
    }

    public function deleteCoupon(Coupon $coupon): bool
    {
        return $this->couponRepo->delete($coupon);
    }

    // ──────────────────── Business Logic ────────────────────

    /**
     * @throws \InvalidArgumentException
     */
    public function validate(string $code, float $orderSubtotal, ?CouponAppliesTo $appliesTo = null): Coupon
    {
        $coupon = $this->couponRepo->findByCode($code);

        if (!$coupon) {
            throw new \InvalidArgumentException('Mã giảm giá không tồn tại.');
        }

        if (!$coupon->isValid()) {
            throw new \InvalidArgumentException('Mã giảm giá đã hết hiệu lực hoặc hết lượt sử dụng.');
        }

        if ($appliesTo !== null && $coupon->applies_to !== $appliesTo) {
            $label = $coupon->applies_to->label();
            throw new \InvalidArgumentException("Mã này chỉ áp dụng cho: {$label}.");
        }

        if ($orderSubtotal < $coupon->min_amount) {
            throw new \InvalidArgumentException(
                'Đơn tối thiểu ' . number_format($coupon->min_amount, 0, ',', '.') . 'đ để sử dụng mã này.'
            );
        }

        return $coupon;
    }

    public function calculateDiscount(Coupon $coupon, float $targetAmount): float
    {
        if ($coupon->type === CouponType::Fixed) {
            return min($coupon->value, $targetAmount);
        }

        $discount = $targetAmount * ($coupon->value / 100);

        if ($coupon->max_discount !== null) {
            $discount = min($discount, $coupon->max_discount);
        }

        return min($discount, $targetAmount);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function markUsed(Coupon $coupon): void
    {
        $affected = $this->couponRepo->atomicIncrementUsedCount($coupon->id);

        if ($affected === 0) {
            throw new \InvalidArgumentException('Mã giảm giá đã hết lượt sử dụng.');
        }
    }
}
