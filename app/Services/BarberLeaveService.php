<?php

namespace App\Services;

use App\DTOs\Barber\CreateLeaveData;
use App\Enums\LeaveStatus;
use App\Models\BarberLeave;
use App\Repositories\Contracts\Barber\BarberLeaveRepositoryInterface;
use Illuminate\Support\Collection;

class BarberLeaveService
{
    public function __construct(
        protected BarberLeaveRepositoryInterface $leaveRepo,
    ) {}

    public function getLeaves(int $barberId, ?string $from = null, ?string $to = null): Collection
    {
        return $this->leaveRepo->getByBarber($barberId, $from, $to);
    }

    public function getAllLeaves(?string $status = null): Collection
    {
        return $this->leaveRepo->getAllWithRelations($status);
    }

    public function pendingCount(): int
    {
        return $this->leaveRepo->pendingCount();
    }

    public function store(int $barberId, CreateLeaveData $data): BarberLeave
    {
        return $this->leaveRepo->create([
            'barber_id'  => $barberId,
            'leave_date' => $data->leave_date,
            'type'       => $data->type,
            'start_time' => $data->start_time,
            'end_time'   => $data->end_time,
            'reason'     => $data->reason,
            'status'     => LeaveStatus::Pending,
        ]);
    }

    public function approve(BarberLeave $leave, int $adminId, ?string $note = null): void
    {
        $leave->update([
            'status'      => LeaveStatus::Approved,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_note'  => $note,
        ]);

        $this->leaveRepo->blockTimeSlots($leave);
    }

    public function reject(BarberLeave $leave, int $adminId, ?string $note = null): void
    {
        $leave->update([
            'status'      => LeaveStatus::Rejected,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_note'  => $note,
        ]);
    }

    public function cancel(BarberLeave $leave): void
    {
        if ($leave->status === LeaveStatus::Approved) {
            $this->leaveRepo->unblockTimeSlots($leave);
        }

        $this->leaveRepo->delete($leave);
    }
}
