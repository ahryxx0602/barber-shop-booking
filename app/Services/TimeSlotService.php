<?php

namespace App\Services;

use App\Models\TimeSlot;
use App\Models\WorkingSchedule;
use Carbon\Carbon;

class TimeSlotService
{
    /**
     * Generate slots cho 1 barber trong 1 ngày cụ thể
     * Dựa theo working_schedule của barber đó
     */
    public function generateForBarber(int $barberId, string $date): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedule = WorkingSchedule::where('barber_id', $barberId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_day_off', false)
            ->first();

        if (!$schedule)
            return; // ngày nghỉ, không generate

        $current = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

        while ($current->copy()->addMinutes(30)->lte($end)) {
            TimeSlot::firstOrCreate([
                'barber_id' => $barberId,
                'slot_date' => $date,
                'start_time' => $current->format('H:i:s'),
            ], [
                'end_time' => $current->copy()->addMinutes(30)->format('H:i:s'),
                'status' => 'available',
            ]);
            $current->addMinutes(30);
        }
    }
}