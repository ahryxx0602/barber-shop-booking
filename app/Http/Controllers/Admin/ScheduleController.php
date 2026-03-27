<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Barber\UpdateScheduleData;
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

    public function index(Request $request): View
    {
        $branchId = $request->input('branch_id') ? (int) $request->input('branch_id') : null;
        $barbersData = $this->scheduleService->getAllBarbersScheduleData($branchId);
        $branches = \App\Models\Branch::where('is_active', true)->orderBy('name')->get();

        return view('admin.schedules.index', compact('barbersData', 'branches', 'branchId'));
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
