<?php

namespace App\Repositories\Eloquent\Admin;

use App\Repositories\Eloquent\BaseRepository;
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
                $query->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->increment('used_count');
    }
}
