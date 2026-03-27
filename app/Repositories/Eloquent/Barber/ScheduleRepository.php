<?php

namespace App\Repositories\Eloquent\Barber;

use App\Models\Barber;
use App\Models\WorkingSchedule;
use App\Repositories\Contracts\Barber\ScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ScheduleRepository extends BaseRepository implements ScheduleRepositoryInterface
{
    public function __construct(WorkingSchedule $model)
    {
        parent::__construct($model);
    }

    public function upsertSchedules(array $data, array $uniqueKeys, array $updateColumns): void
    {
        // Tối ưu N+1 Query: Dùng upsert thay vì updateOrCreate trong vòng lặp
        // để gom tất cả thay đổi thành 1 câu query duy nhất
        WorkingSchedule::upsert($data, $uniqueKeys, $updateColumns);
    }

    public function getBarbersByBranch(?int $branchId = null): Collection
    {
        $query = Barber::with(['user', 'workingSchedules', 'branch'])->latest();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }
}
