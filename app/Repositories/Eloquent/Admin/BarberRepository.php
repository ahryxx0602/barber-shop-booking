<?php

namespace App\Repositories\Eloquent\Admin;

use App\Models\Barber;
use App\Repositories\Contracts\Admin\BarberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BarberRepository extends BaseRepository implements BarberRepositoryInterface
{
    public function __construct(Barber $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with('user', 'branch');

        // Filter theo chi nhánh
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // Tìm theo tên thợ
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }
}
