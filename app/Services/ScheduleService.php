<?php

namespace App\Services;

use App\DTOs\Barber\UpdateScheduleData;
use App\Models\Barber;
use App\Repositories\Contracts\Barber\ScheduleRepositoryInterface;
use App\Services\Booking\TimeSlotService;
use Illuminate\Support\Facades\DB;

class ScheduleService
{
    public const DAY_LABELS = [
        0 => 'Chủ nhật',
        1 => 'Thứ 2',
        2 => 'Thứ 3',
        3 => 'Thứ 4',
        4 => 'Thứ 5',
        5 => 'Thứ 6',
        6 => 'Thứ 7',
    ];

    public function __construct(
        protected TimeSlotService $timeSlotService,
        protected ScheduleRepositoryInterface $scheduleRepo,
    ) {}

    public function getScheduleData(Barber $barber): array
    {
        $schedules = $barber->workingSchedules->keyBy('day_of_week');

        $days = [];
        $daysJson = [];

        foreach (self::DAY_LABELS as $dayNum => $label) {
            $schedule = $schedules->get($dayNum);
            $isDayOff = $schedule ? (bool) $schedule->is_day_off : true;
            $startTime = $schedule && !$schedule->is_day_off ? substr($schedule->start_time, 0, 5) : '08:00';
            $endTime = $schedule && !$schedule->is_day_off ? substr($schedule->end_time, 0, 5) : '18:00';

            $days[] = [
                'day_of_week' => $dayNum,
                'label'       => $label,
                'is_day_off'  => $isDayOff,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
            ];

            $daysJson[] = [
                'day_of_week' => $dayNum,
                'is_working'  => !$isDayOff,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
            ];
        }

        return compact('days', 'daysJson');
    }

    public function getAllBarbersScheduleData(?int $branchId = null): array
    {
        $barbers = $this->scheduleRepo->getBarbersByBranch($branchId);

        $barbersData = [];
        foreach ($barbers as $barber) {
            $schedules = $barber->workingSchedules->keyBy('day_of_week');
            $days = [];
            foreach (self::DAY_LABELS as $dayNum => $label) {
                $schedule = $schedules->get($dayNum);
                $days[] = [
                    'label'      => $label,
                    'is_day_off' => $schedule ? (bool) $schedule->is_day_off : true,
                    'start_time' => $schedule && !$schedule->is_day_off ? substr($schedule->start_time, 0, 5) : null,
                    'end_time'   => $schedule && !$schedule->is_day_off ? substr($schedule->end_time, 0, 5) : null,
                ];
            }
            $barbersData[] = [
                'barber' => $barber,
                'days'   => $days,
            ];
        }

        return $barbersData;
    }

    public function updateSchedule(Barber $barber, UpdateScheduleData $data): void
    {
        DB::transaction(function () use ($barber, $data) {
            $upsertData = [];
            foreach ($data->schedules as $item) {
                $isDayOff = !$item->is_working;
                $upsertData[] = [
                    'barber_id'   => $barber->id,
                    'day_of_week' => $item->day_of_week,
                    'start_time'  => $isDayOff ? '00:00:00' : $item->start_time . ':00',
                    'end_time'    => $isDayOff ? '00:00:00' : $item->end_time . ':00',
                    'is_day_off'  => $isDayOff,
                ];
            }

            $this->scheduleRepo->upsertSchedules(
                $upsertData,
                ['barber_id', 'day_of_week'],
                ['start_time', 'end_time', 'is_day_off']
            );
        });

        $this->timeSlotService->clearAndRegenerate($barber->id);
    }
}
