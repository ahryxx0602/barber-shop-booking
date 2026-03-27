<?php

namespace App\Repositories\Contracts\Barber;

use Illuminate\Database\Eloquent\Collection;

interface ScheduleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Upsert schedules (gom tất cả thay đổi thành 1 query).
     */
    public function upsertSchedules(array $data, array $uniqueKeys, array $updateColumns): void;

    /**
     * Lấy danh sách barbers với eager load cho schedule overview.
     */
    public function getBarbersByBranch(?int $branchId = null): Collection;
}
