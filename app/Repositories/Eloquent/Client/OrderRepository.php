<?php

namespace App\Repositories\Eloquent\Client;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Contracts\Client\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['customer', 'payment'])->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                // M2: order_code dùng prefix match (index-friendly) thay vì %like%
                $q->where('order_code', 'like', "{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "{$search}%")
                        ->orWhere('phone', 'like', "{$search}%");
                  });
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getStats(): object
    {
        // M6: Gom stats vào 1 query aggregate thay vì 4 queries riêng
        return $this->model->selectRaw("
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as shipping_orders,
            SUM(CASE WHEN status = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ? THEN total_amount ELSE 0 END) as this_month_revenue
        ", [
            OrderStatus::Pending->value,
            OrderStatus::Shipping->value,
            OrderStatus::Delivered->value,
            now()->month,
            now()->year,
        ])->first();
    }

    public function paginateByCustomer(int $customerId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate($perPage);
    }
}
