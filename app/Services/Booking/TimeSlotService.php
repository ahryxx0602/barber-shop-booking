<?php

namespace App\Services\Booking;

use App\Enums\LeaveStatus;
use App\Enums\TimeSlotStatus;
use App\Models\BarberLeave;
use App\Models\TimeSlot;
use App\Models\WorkingSchedule;
use Carbon\Carbon;

class TimeSlotService
{
    public function generateForBarber(int $barberId, string $date): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedule = WorkingSchedule::where('barber_id', $barberId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_day_off', false)
            ->first();

        if (!$schedule) {
            return;
        }

        $current = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

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

    public function generateForBarberRange(int $barberId, int $days = 7): void
    {
        $schedules = WorkingSchedule::where('barber_id', $barberId)
            ->where('is_day_off', false)
            ->get()
            ->keyBy('day_of_week');

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

    public function generateForBarberWithSchedule(int $barberId, string $date, $schedules, $leaves = null): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = $schedules->get($dayOfWeek);

        if (!$schedule) {
            return;
        }

        if ($leaves && $leaves->isNotEmpty()) {
            $hasFullDayLeave = $leaves->contains(fn ($l) => $l->type === 'full_day');
            if ($hasFullDayLeave) {
                return;
            }
        }

        $current = Carbon::parse($date . ' ' . $schedule->start_time);
        $end = Carbon::parse($date . ' ' . $schedule->end_time);

        $slotsToInsert = [];
        while ($current->copy()->addMinutes(30)->lte($end)) {
            $slotStart = $current->format('H:i:s');
            $slotEnd = $current->copy()->addMinutes(30)->format('H:i:s');

            $isOnLeave = false;
            if ($leaves && $leaves->isNotEmpty()) {
                foreach ($leaves as $leave) {
                    if ($leave->type !== 'full_day' && $leave->start_time && $leave->end_time) {
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

    public function clearAndRegenerate(int $barberId, int $days = 7): void
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays($days - 1)->format('Y-m-d');

        TimeSlot::where('barber_id', $barberId)
            ->where('status', 'available')
            ->whereBetween('slot_date', [$startDate, $endDate])
            ->delete();

        $this->generateForBarberRange($barberId, $days);
    }
}
