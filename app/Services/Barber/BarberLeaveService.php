<?php

namespace App\Services\Barber;

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

    /**
     * Lấy danh sách ngày nghỉ của barber trong khoảng thời gian.
     */
    public function getLeaves(int $barberId, ?string $from = null, ?string $to = null): Collection
    {
        return $this->leaveRepo->getByBarber($barberId, $from, $to);
    }

    /**
     * Lấy tất cả đơn nghỉ (cho Admin).
     */
    public function getAllLeaves(?string $status = null): Collection
    {
        return $this->leaveRepo->getAllWithRelations($status);
    }

    /**
     * Đếm số đơn chờ duyệt.
     */
    public function pendingCount(): int
    {
        return $this->leaveRepo->pendingCount();
    }

    /**
     * Tạo đơn xin nghỉ mới (status = pending).
     */
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

    /**
     * Admin duyệt đơn nghỉ → block time slots.
     */
    public function approve(BarberLeave $leave, int $adminId, ?string $note = null): void
    {
        $leave->update([
            'status'      => LeaveStatus::Approved,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_note'  => $note,
        ]);

        // Block time slots
        $this->leaveRepo->blockTimeSlots($leave);
    }

    /**
     * Admin từ chối đơn nghỉ.
     */
    public function reject(BarberLeave $leave, int $adminId, ?string $note = null): void
    {
        $leave->update([
            'status'      => LeaveStatus::Rejected,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_note'  => $note,
        ]);
    }

    /**
     * Barber huỷ đơn nghỉ (chỉ khi pending hoặc approved).
     * Nếu đã approved → unblock time slots.
     */
    public function cancel(BarberLeave $leave): void
    {
        if ($leave->status === LeaveStatus::Approved) {
            $this->leaveRepo->unblockTimeSlots($leave);
        }

        $this->leaveRepo->delete($leave);
    }
}
