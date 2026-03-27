<?php

namespace App\Repositories\Contracts\Client;

use App\Enums\OrderStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Phân trang đơn hàng với filter status, search, eager load.
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Thống kê đơn hàng tổng hợp (1 query aggregate).
     */
    public function getStats(): object;

    /**
     * Lấy đơn hàng của customer với eager load, phân trang.
     */
    public function paginateByCustomer(int $customerId, int $perPage = 10): LengthAwarePaginator;
}
