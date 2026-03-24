<?php

namespace App\Services;

use App\Enums\LeaveStatus;
use App\Enums\TimeSlotStatus;
use App\Models\BarberLeave;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BarberLeaveService
{
    /**
     * Lấy danh sách ngày nghỉ của barber trong khoảng thời gian.
     */
    public function getLeaves(int $barberId, ?string $from = null, ?string $to = null): Collection
    {
        $query = BarberLeave::where('barber_id', $barberId)
            ->orderBy('leave_date', 'desc');

        if ($from) {
            $query->where('leave_date', '>=', $from);
        }
        if ($to) {
            $query->where('leave_date', '<=', $to);
        }

        return $query->get();
    }

    /**
     * Lấy tất cả đơn nghỉ (cho Admin).
     */
    public function getAllLeaves(?string $status = null): Collection
    {
        $query = BarberLeave::with(['barber.user', 'reviewer'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Đếm số đơn chờ duyệt.
     */
    public function pendingCount(): int
    {
        return BarberLeave::where('status', LeaveStatus::Pending)->count();
    }

    /**
     * Tạo đơn xin nghỉ mới (status = pending).
     */
    public function store(int $barberId, array $data): BarberLeave
    {
        return BarberLeave::create([
            'barber_id'  => $barberId,
            'leave_date' => $data['leave_date'],
            'type'       => $data['type'] ?? 'full_day',
            'start_time' => $data['start_time'] ?? null,
            'end_time'   => $data['end_time'] ?? null,
            'reason'     => $data['reason'] ?? null,
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
        $this->blockTimeSlots($leave);
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
            $this->unblockTimeSlots($leave);
        }

        $leave->delete();
    }

    /**
     * Block time slots cho ngày nghỉ.
     */
    private function blockTimeSlots(BarberLeave $leave): void
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

    /**
     * Khôi phục time slots khi huỷ ngày nghỉ đã duyệt.
     */
    private function unblockTimeSlots(BarberLeave $leave): void
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
