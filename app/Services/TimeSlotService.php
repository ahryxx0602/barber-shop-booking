<?php

namespace App\Services;

use App\Enums\LeaveStatus;
use App\Enums\TimeSlotStatus;
use App\Models\BarberLeave;
use App\Models\TimeSlot;
use App\Models\WorkingSchedule;
use Carbon\Carbon;

class TimeSlotService
{
    /**
     * Generate slots cho 1 barber trong 1 ngày cụ thể.
     * Dựa theo working_schedule của barber đó.
     */
    public function generateForBarber(int $barberId, string $date): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedule = WorkingSchedule::where('barber_id', $barberId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_day_off', false)
            ->first();

        if (!$schedule) {
            return; // ngày nghỉ, không generate
        }

        $current = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

        // Batch upsert thay vì firstOrCreate loop (giảm từ ~40 queries xuống 1)
        $slotsToInsert = [];
        while ($current->copy()->addMinutes(30)->lte($end)) {
            $slotsToInsert[] = [
                'barber_id'  => $barberId,
                'slot_date'  => $date,
                'start_time' => $current->format('H:i:s'),
                'end_time'   => $current->copy()->addMinutes(30)->format('H:i:s'),
                'status'     => TimeSlotStatus::Available->value,
            ];
            $current->addMinutes(30);
        }

        if (!empty($slotsToInsert)) {
            TimeSlot::upsert($slotsToInsert, ['barber_id', 'slot_date', 'start_time'], ['end_time']);
        }
    }

    /**
     * Generate slots cho 1 barber trong nhiều ngày liên tiếp.
     * Mặc định generate cho 7 ngày tới (hôm nay + 6 ngày).
     * Tự động skip ngày thợ đã đăng ký nghỉ (approved).
     */
    public function generateForBarberRange(int $barberId, int $days = 7): void
    {
        // Pre-load tất cả schedules
        $schedules = WorkingSchedule::where('barber_id', $barberId)
            ->where('is_day_off', false)
            ->get()
            ->keyBy('day_of_week');

        // Pre-load tất cả leave đã duyệt trong khoảng ngày generate
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays($days - 1)->format('Y-m-d');
        $leaves = BarberLeave::where('barber_id', $barberId)
            ->where('status', LeaveStatus::Approved)
            ->whereBetween('leave_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($l) => $l->leave_date->format('Y-m-d'));

        for ($i = 0; $i < $days; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $dayLeaves = $leaves->get($date, collect());
            $this->generateForBarberWithSchedule($barberId, $date, $schedules, $dayLeaves);
        }
    }

    /**
     * Generate slots cho 1 barber trong 1 ngày, dùng schedule đã pre-load.
     * Kiểm tra leave: full_day → skip cả ngày, partial → skip slot trùng giờ nghỉ.
     */
    public function generateForBarberWithSchedule(int $barberId, string $date, $schedules, $leaves = null): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = $schedules->get($dayOfWeek);

        if (!$schedule) {
            return; // ngày nghỉ, hoặc không có schedule
        }

        // Check leave approved: nếu nghỉ full_day → không tạo slot
        if ($leaves && $leaves->isNotEmpty()) {
            $hasFullDayLeave = $leaves->contains(fn ($l) => $l->type === 'full_day');
            if ($hasFullDayLeave) {
                return; // Nghỉ cả ngày, skip
            }
        }

        $current = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

        // Batch upsert — giảm từ ~40 queries xuống 1 cho mỗi ngày
        $slotsToInsert = [];
        while ($current->copy()->addMinutes(30)->lte($end)) {
            $slotStart = $current->format('H:i:s');
            $slotEnd = $current->copy()->addMinutes(30)->format('H:i:s');

            // Kiểm tra slot có nằm trong giờ nghỉ partial không
            $isOnLeave = false;
            if ($leaves && $leaves->isNotEmpty()) {
                foreach ($leaves as $leave) {
                    if ($leave->type !== 'full_day' && $leave->start_time && $leave->end_time) {
                        // Slot bắt đầu trước khi leave kết thúc VÀ slot kết thúc sau khi leave bắt đầu
                        if ($slotStart < $leave->end_time && $slotEnd > $leave->start_time) {
                            $isOnLeave = true;
                            break;
                        }
                    }
                }
            }

            if (!$isOnLeave) {
                $slotsToInsert[] = [
                    'barber_id'  => $barberId,
                    'slot_date'  => $date,
                    'start_time' => $slotStart,
                    'end_time'   => $slotEnd,
                    'status'     => TimeSlotStatus::Available->value,
                ];
            }

            $current->addMinutes(30);
        }

        if (!empty($slotsToInsert)) {
            TimeSlot::upsert($slotsToInsert, ['barber_id', 'slot_date', 'start_time'], ['end_time']);
        }
    }

    /**
     * Xoá các slot "available" (chưa được đặt) rồi generate lại.
     * Dùng khi barber thay đổi working schedule — chỉ xoá slot chưa book,
     * slot đã booked giữ nguyên để không ảnh hưởng booking hiện tại.
     */
    public function clearAndRegenerate(int $barberId, int $days = 7): void
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays($days - 1)->format('Y-m-d');

        // Chỉ xoá slot available (chưa có ai đặt)
        TimeSlot::where('barber_id', $barberId)
            ->where('status', 'available')
            ->whereBetween('slot_date', [$startDate, $endDate])
            ->delete();

        // Generate lại
        $this->generateForBarberRange($barberId, $days);
    }
}