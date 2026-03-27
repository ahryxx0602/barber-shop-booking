<?php

namespace App\Repositories\Eloquent\Barber;

use App\Enums\LeaveStatus;
use App\Enums\TimeSlotStatus;
use App\Models\BarberLeave;
use App\Models\TimeSlot;
use App\Repositories\Contracts\Barber\BarberLeaveRepositoryInterface;
use Illuminate\Support\Collection;

class BarberLeaveRepository extends BaseRepository implements BarberLeaveRepositoryInterface
{
    public function __construct(BarberLeave $model)
    {
        parent::__construct($model);
    }

    public function getByBarber(int $barberId, ?string $from = null, ?string $to = null): Collection
    {
        $query = $this->model
            ->where('barber_id', $barberId)
            ->orderBy('leave_date', 'desc');

        if ($from) {
            $query->where('leave_date', '>=', $from);
        }
        if ($to) {
            $query->where('leave_date', '<=', $to);
        }

        return $query->get();
    }

    public function getAllWithRelations(?string $status = null): Collection
    {
        $query = $this->model
            ->with(['barber.user', 'reviewer'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function pendingCount(): int
    {
        return $this->model->where('status', LeaveStatus::Pending)->count();
    }

    public function existsForBarberOnDate(int $barberId, string $date): bool
    {
        return $this->model
            ->where('barber_id', $barberId)
            ->where('leave_date', $date)
            ->exists();
    }

    public function blockTimeSlots(BarberLeave $leave): void
    {
        $query = TimeSlot::where('barber_id', $leave->barber_id)
            ->where('slot_date', $leave->leave_date->format('Y-m-d'))
            ->where('status', TimeSlotStatus::Available->value);

        if ($leave->type === 'partial' && $leave->start_time && $leave->end_time) {
            $query->where('start_time', '>=', $leave->start_time)
                  ->where('end_time', '<=', $leave->end_time);
        }

        $query->update(['status' => TimeSlotStatus::Blocked->value]);
    }

    public function unblockTimeSlots(BarberLeave $leave): void
    {
        $query = TimeSlot::where('barber_id', $leave->barber_id)
            ->where('slot_date', $leave->leave_date->format('Y-m-d'))
            ->where('status', TimeSlotStatus::Blocked->value);

        if ($leave->type === 'partial' && $leave->start_time && $leave->end_time) {
            $query->where('start_time', '>=', $leave->start_time)
                  ->where('end_time', '<=', $leave->end_time);
        }

        $query->update(['status' => TimeSlotStatus::Available->value]);
    }
}
