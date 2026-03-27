<?php

namespace App\Repositories\Contracts\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BranchRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Phân trang danh sách branch kèm đếm số barber.
     */
    public function paginateWithBarberCount(int $perPage = 10): LengthAwarePaginator;

    /**
     * Lấy danh sách branch đang hoạt động, sắp xếp theo tên.
     */
    public function getActiveBranches(): Collection;
}
