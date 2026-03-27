<?php

namespace App\Http\Controllers\Barber;

use App\DTOs\Barber\CreateLeaveData;
use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Barber\StoreLeaveRequest;
use App\Models\BarberLeave;
use App\Repositories\Contracts\Barber\BarberLeaveRepositoryInterface;
use App\Repositories\Contracts\Barber\BookingRepositoryInterface;
use App\Services\Barber\BarberLeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function __construct(
        protected BarberLeaveService $leaveService,
        protected BarberLeaveRepositoryInterface $leaveRepo,
        protected BookingRepositoryInterface $bookingRepo,
    ) {}

    /**
     * Danh sách đơn nghỉ của barber hiện tại.
     */
    public function index(Request $request): View
    {
        $barber = $request->user()->barber;

        // Đơn nghỉ sắp tới (từ hôm nay)
        $leaves = $this->leaveService->getLeaves(
            $barber->id,
            now()->toDateString(),
        );

        // Lịch sử (quá khứ)
        $pastLeaves = $this->leaveService->getLeaves(
            $barber->id,
            null,
            now()->subDay()->toDateString(),
        );

        return view('barber.leaves.index', compact('leaves', 'pastLeaves'));
    }

    /**
     * Gửi đơn xin nghỉ.
     */
    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $barber = $request->user()->barber;
        $validated = $request->validated();

        // Kiểm tra đã đăng ký nghỉ ngày này chưa
        if ($this->leaveRepo->existsForBarberOnDate($barber->id, $validated['leave_date'])) {
            return back()->withErrors(['leave_date' => 'Bạn đã đăng ký nghỉ ngày này rồi.'])->withInput();
        }

        // Kiểm tra có booking active trong ngày đó không
        $startTime = ($validated['type'] === 'partial' && !empty($validated['start_time']) && !empty($validated['end_time']))
            ? $validated['start_time']
            : null;
        $endTime = ($validated['type'] === 'partial' && !empty($validated['start_time']) && !empty($validated['end_time']))
            ? $validated['end_time']
            : null;

        if ($this->bookingRepo->hasActiveBookingsOnDate($barber->id, $validated['leave_date'], $startTime, $endTime)) {
            return back()->withErrors(['leave_date' => 'Ngày này đã có lịch hẹn, không thể đăng ký nghỉ. Vui lòng liên hệ Admin.'])->withInput();
        }

        $this->leaveService->store($barber->id, CreateLeaveData::fromRequest($request));

        return back()->with('success', 'Đã gửi đơn xin nghỉ. Vui lòng chờ Admin duyệt.');
    }

    /**
     * Huỷ đơn nghỉ.
     */
    public function destroy(Request $request, BarberLeave $leave): RedirectResponse
    {
        $barber = $request->user()->barber;

        // Chỉ cho phép huỷ đơn của chính mình
        if ($leave->barber_id !== $barber->id) {
            abort(403);
        }

        // Không cho huỷ đơn đã bị từ chối (đã xử lý xong)
        if ($leave->status === LeaveStatus::Rejected) {
            return back()->withErrors(['leave' => 'Đơn nghỉ đã bị từ chối, không thể huỷ.']);
        }

        // Không cho huỷ ngày nghỉ đã qua
        if ($leave->leave_date->lt(now()->startOfDay())) {
            return back()->withErrors(['leave' => 'Không thể huỷ ngày nghỉ đã qua.']);
        }

        $this->leaveService->cancel($leave);

        return back()->with('success', 'Đã huỷ đơn xin nghỉ.');
    }
}
