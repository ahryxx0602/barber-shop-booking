<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\UpdateScheduleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateScheduleRequest;
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

    public function update(UpdateScheduleRequest $request, Barber $barber): RedirectResponse
    {
        $barber->load('user');

        $this->scheduleService->updateSchedule(
            $barber,
            UpdateScheduleData::fromArray($request->input('schedules'))
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', "Lịch làm việc của {$barber->user->name} đã được cập nhật.");
    }
}
