<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\UpdateScheduleData;
use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Services\ScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleService $scheduleService,
    ) {}

    public function index(): View
    {
        $barbersData = $this->scheduleService->getAllBarbersScheduleData();
        return view('admin.schedules.index', compact('barbersData'));
    }

    public function edit(Barber $barber): View
    {
        $barber->load('user', 'workingSchedules');
        $scheduleData = $this->scheduleService->getScheduleData($barber);

        return view('admin.schedules.edit', [
            'barber'   => $barber,
            'days'     => $scheduleData['days'],
            'daysJson' => $scheduleData['daysJson'],
        ]);
    }

    public function update(Request $request, Barber $barber): RedirectResponse
    {
        // Tối ưu N+1 Query (Issue #4): Eager load 'user' để dùng cho flash message bên dưới
        $barber->load('user');

        $request->validate([
            'schedules'               => ['required', 'array', 'size:7'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'schedules.*.is_working'  => ['sometimes', 'boolean'],
            'schedules.*.start_time'  => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i'],
            'schedules.*.end_time'    => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i', 'after:schedules.*.start_time'],
        ]);

        $this->scheduleService->updateSchedule(
            $barber,
            UpdateScheduleData::fromArray($request->input('schedules'))
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', "Lịch làm việc của {$barber->user->name} đã được cập nhật.");
    }
}
