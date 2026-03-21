<?php

namespace App\Services;

use App\Models\Barber;
use App\Models\WorkingSchedule;
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
    ) {}

    /**
     * Chuẩn bị dữ liệu schedule cho view (dùng chung cho Admin + Barber).
     */
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

    /**
     * Chuẩn bị dữ liệu tổng quan schedule cho tất cả barbers (Admin index).
     */
    public function getAllBarbersScheduleData(): array
    {
        $barbers = Barber::with(['user', 'workingSchedules'])->latest()->get();

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

    /**
     * Lưu schedule cho barber + regenerate time slots.
     */
    public function updateSchedule(Barber $barber, array $schedulesData): void
    {
        DB::transaction(function () use ($barber, $schedulesData) {
            foreach ($schedulesData as $data) {
                $isDayOff = !isset($data['is_working']) || !$data['is_working'];

                WorkingSchedule::updateOrCreate(
                    [
                        'barber_id'   => $barber->id,
                        'day_of_week' => $data['day_of_week'],
                    ],
                    [
                        'start_time' => $isDayOff ? '00:00:00' : $data['start_time'] . ':00',
                        'end_time'   => $isDayOff ? '00:00:00' : $data['end_time'] . ':00',
                        'is_day_off' => $isDayOff,
                    ]
                );
            }
        });

        $this->timeSlotService->clearAndRegenerate($barber->id);
    }
}
