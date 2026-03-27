<?php

namespace App\Repositories\Contracts\Admin;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BarberRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Phân trang danh sách barber với filter branch_id và search tên user.
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator;
}
