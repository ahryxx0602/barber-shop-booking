<?php

namespace App\Repositories\Contracts\Admin;

use App\Enums\UserRole;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Phân trang danh sách user với filter role và search.
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Đếm số user theo role.
     */
    public function countByRole(UserRole $role): int;
}
