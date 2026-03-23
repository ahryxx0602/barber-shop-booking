<?php

namespace App\Services;

use App\Enums\TimeSlotStatus;
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

        while ($current->copy()->addMinutes(30)->lte($end)) {
            TimeSlot::firstOrCreate([
                'barber_id' => $barberId,
                'slot_date' => $date,
                'start_time' => $current->format('H:i:s'),
            ], [
                'end_time' => $current->copy()->addMinutes(30)->format('H:i:s'),
                'status' => TimeSlotStatus::Available,
            ]);
            $current->addMinutes(30);
        }
    }

    /**
     * Generate slots cho 1 barber trong nhiều ngày liên tiếp.
     * Mặc định generate cho 7 ngày tới (hôm nay + 6 ngày).
     */
    public function generateForBarberRange(int $barberId, int $days = 7): void
    {
        // Tối ưu N+1 Query (Issue #5): Pre-load tất cả schedules của barber 1 lần duy nhất
        // Thay vì mỗi ngày trong vòng lặp lại query DB 1 lần (giảm 7 SELECT queries xuống 1)
        $schedules = WorkingSchedule::where('barber_id', $barberId)
            ->where('is_day_off', false)
            ->get()
            ->keyBy('day_of_week');

        for ($i = 0; $i < $days; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $this->generateForBarberWithSchedule($barberId, $date, $schedules);
        }
    }

    /**
     * Generate slots cho 1 barber trong 1 ngày, dùng schedule đã pre-load.
     */
    public function generateForBarberWithSchedule(int $barberId, string $date, $schedules): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = $schedules->get($dayOfWeek);

        if (!$schedule) {
            return; // ngày nghỉ, hoặc không có schedule
        }

        $current = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

        // Chỗ này firstOrCreate có thể gom thành insert() nếu muốn tối ưu cực độ, 
        // nhưng với logic nghiệp vụ tạo slot theo ngày thì firstOrCreate vẫn an toàn hơn để tránh duplicate
        $slotsToInsert = [];
        while ($current->copy()->addMinutes(30)->lte($end)) {
            TimeSlot::firstOrCreate([
                'barber_id' => $barberId,
                'slot_date' => $date,
                'start_time' => $current->format('H:i:s'),
            ], [
                'end_time' => $current->copy()->addMinutes(30)->format('H:i:s'),
                'status' => TimeSlotStatus::Available,
            ]);
            $current->addMinutes(30);
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