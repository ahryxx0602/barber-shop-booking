<?php

namespace App\Repositories\Eloquent\Admin;

use App\Models\Branch;
use App\Repositories\Contracts\Admin\BranchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BranchRepository extends BaseRepository implements BranchRepositoryInterface
{
    public function __construct(Branch $model)
    {
        parent::__construct($model);
    }

    public function paginateWithBarberCount(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->withCount('barbers')->latest()->paginate($perPage);
    }

    public function getActiveBranches(): Collection
    {
        return $this->model->where('is_active', true)->orderBy('name')->get();
    }
}
