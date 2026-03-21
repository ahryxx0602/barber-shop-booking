<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\WorkingSchedule;
use App\Services\TimeSlotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScheduleController extends Controller
{
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
     * Danh sách lịch làm việc của tất cả thợ.
     */
    public function index(): View
    {
        $barbers = Barber::with(['user', 'workingSchedules'])->latest()->get();

        // Chuẩn bị dữ liệu cho view
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

        return view('admin.schedules.index', compact('barbersData'));
    }

    /**
     * Trang chỉnh sửa lịch làm việc cho 1 thợ cụ thể (admin chỉnh cho thợ).
     */
    public function edit(Barber $barber): View
    {
        $barber->load('user', 'workingSchedules');
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

        return view('admin.schedules.edit', compact('barber', 'days', 'daysJson'));
    }

    /**
     * Lưu lịch làm việc cho 1 thợ cụ thể (admin cập nhật).
     */
    public function update(Request $request, Barber $barber): RedirectResponse
    {
        $request->validate([
            'schedules'               => ['required', 'array', 'size:7'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'schedules.*.is_working'  => ['sometimes', 'boolean'],
            'schedules.*.start_time'  => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i'],
            'schedules.*.end_time'    => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i', 'after:schedules.*.start_time'],
        ]);

        $schedulesData = $request->input('schedules');

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

        // Generate lại time slots cho 7 ngày tới sau khi admin cập nhật schedule
        $this->timeSlotService->clearAndRegenerate($barber->id);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', "Lịch làm việc của {$barber->user->name} đã được cập nhật.");
    }
}
