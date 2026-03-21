<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barber\UpdateScheduleRequest;
use App\Services\ScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleService $scheduleService,
    ) {}

    public function edit(): View
    {
        $barber = auth()->user()->barber;
        $scheduleData = $this->scheduleService->getScheduleData($barber);

        return view('barber.schedule.edit', [
            'days'     => $scheduleData['days'],
            'daysJson' => $scheduleData['daysJson'],
        ]);
    }

    public function update(UpdateScheduleRequest $request): RedirectResponse
    {
        $barber = auth()->user()->barber;

        $this->scheduleService->updateSchedule($barber, $request->validated()['schedules']);

        return redirect()
            ->route('barber.schedule.edit')
            ->with('success', 'Lịch làm việc đã được cập nhật thành công!');
    }
}
