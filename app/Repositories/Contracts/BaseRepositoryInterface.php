<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * Lấy tất cả bản ghi.
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Tìm bản ghi theo ID.
     */
    public function find(int $id, array $columns = ['*']): ?Model;

    /**
     * Tìm bản ghi theo ID, throw 404 nếu không tìm thấy.
     */
    public function findOrFail(int $id, array $columns = ['*']): Model;

    /**
     * Tạo bản ghi mới.
     */
    public function create(array $data): Model;

    /**
     * Cập nhật bản ghi theo ID.
     */
    public function update(Model $model, array $data): Model;

    /**
     * Xóa bản ghi.
     */
    public function delete(Model $model): bool;

    /**
     * Phân trang danh sách bản ghi.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
}
