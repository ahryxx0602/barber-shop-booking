<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barber\UpdateScheduleRequest;
use App\Models\WorkingSchedule;
use App\Services\TimeSlotService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ScheduleController extends Controller
{
    /**
     * Tên ngày trong tuần tiếng Việt.
     */
    private const DAY_LABELS = [
        0 => 'Chủ nhật',
        1 => 'Thứ 2',
        2 => 'Thứ 3',
        3 => 'Thứ 4',
        4 => 'Thứ 5',
        5 => 'Thứ 6',
        6 => 'Thứ 7',
    ];

    public function __construct(
        private readonly TimeSlotService $timeSlotService
    ) {}

    /**
     * Hiển thị trang cài đặt lịch làm việc.
     */
    public function edit(): View
    {
        $barber = auth()->user()->barber;

        // Lấy schedule hiện tại, index theo day_of_week
        $schedules = $barber->workingSchedules->keyBy('day_of_week');

        // Chuẩn bị data cho 7 ngày
        $days = [];
        foreach (self::DAY_LABELS as $dayNum => $label) {
            $schedule = $schedules->get($dayNum);
            $days[] = [
                'day_of_week' => $dayNum,
                'label'       => $label,
                'is_day_off'  => $schedule ? (bool) $schedule->is_day_off : true,
                'start_time'  => $schedule && !$schedule->is_day_off ? substr($schedule->start_time, 0, 5) : '08:00',
                'end_time'    => $schedule && !$schedule->is_day_off ? substr($schedule->end_time, 0, 5) : '18:00',
            ];
        }

        // Pre-format dữ liệu cho Alpine.js (tránh dùng fn() trong Blade @json)
        $daysJson = [];
        foreach ($days as $d) {
            $daysJson[] = [
                'day_of_week' => $d['day_of_week'],
                'is_working'  => !$d['is_day_off'],
                'start_time'  => $d['start_time'],
                'end_time'    => $d['end_time'],
            ];
        }

        return view('barber.schedule.edit', compact('days', 'daysJson'));
    }

    /**
     * Lưu lịch làm việc.
     */
    public function update(UpdateScheduleRequest $request): RedirectResponse
    {
        $barber = auth()->user()->barber;
        $schedulesData = $request->validated()['schedules'];

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

        // Bước 4.4: Generate lại time slots cho 7 ngày tới sau khi cập nhật schedule
        $this->timeSlotService->clearAndRegenerate($barber->id);

        return redirect()
            ->route('barber.schedule.edit')
            ->with('success', 'Lịch làm việc đã được cập nhật thành công!');
    }
}

