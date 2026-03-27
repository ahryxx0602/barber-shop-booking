<?php

namespace App\Repositories\Contracts\Admin;

use Illuminate\Database\Eloquent\Model;

interface CouponRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Tìm coupon theo mã code.
     */
    public function findByCode(string $code): ?Model;

    /**
     * Tăng used_count atomic (chỉ khi chưa vượt max_usage).
     * Trả về số dòng bị ảnh hưởng (0 = hết lượt).
     */
    public function atomicIncrementUsedCount(int $couponId): int;
}
