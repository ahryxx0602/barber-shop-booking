<?php

namespace App\Repositories\Eloquent\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\Admin\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('id', '!=', auth()->id());

        // Lọc theo role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Tìm kiếm theo tên / email / SĐT
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function countByRole(UserRole $role): int
    {
        return $this->model->where('role', $role)->count();
    }
}
