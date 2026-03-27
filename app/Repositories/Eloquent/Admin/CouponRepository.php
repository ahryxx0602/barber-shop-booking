<?php

namespace App\Repositories\Eloquent\Admin;

use App\Models\Coupon;
use App\Repositories\Contracts\Admin\CouponRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    public function __construct(Coupon $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): ?Model
    {
        return $this->model->where('code', strtoupper(trim($code)))->first();
    }

    public function atomicIncrementUsedCount(int $couponId): int
    {
        // Atomic update — chỉ tăng nếu chưa vượt max_usage
        return $this->model->where('id', $couponId)
            ->where(function ($query) {
                $query->whereNull('max_usage')
                      ->orWhereColumn('used_count', '<', 'max_usage');
            })
            ->increment('used_count');
    }
}
