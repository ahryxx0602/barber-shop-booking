<?php

namespace App\Http\Controllers\Barber;

use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Models\BarberLeave;
use App\Services\BarberLeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function __construct(
        protected BarberLeaveService $leaveService,
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
    public function store(Request $request): RedirectResponse
    {
        $barber = $request->user()->barber;

        $validated = $request->validate([
            'leave_date'  => 'required|date|after:today',
            'type'        => 'required|in:full_day,partial',
            'start_time'  => 'nullable|required_if:type,partial|date_format:H:i',
            'end_time'    => 'nullable|required_if:type,partial|date_format:H:i|after:start_time',
            'reason'      => 'nullable|string|max:255',
        ], [
            'leave_date.required'  => 'Vui lòng chọn ngày nghỉ.',
            'leave_date.after'     => 'Chỉ có thể đăng ký nghỉ từ ngày mai trở đi.',
            'type.required'        => 'Vui lòng chọn loại nghỉ.',
            'start_time.required_if' => 'Vui lòng chọn giờ bắt đầu khi nghỉ một phần ngày.',
            'end_time.required_if'   => 'Vui lòng chọn giờ kết thúc khi nghỉ một phần ngày.',
            'end_time.after'         => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'reason.max'             => 'Lý do không quá 255 ký tự.',
        ]);

        // Kiểm tra đã đăng ký nghỉ ngày này chưa
        $exists = BarberLeave::where('barber_id', $barber->id)
            ->where('leave_date', $validated['leave_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['leave_date' => 'Bạn đã đăng ký nghỉ ngày này rồi.'])->withInput();
        }

        // Kiểm tra có booking active trong ngày đó không
        $activeStatuses = [
            \App\Enums\BookingStatus::Pending->value,
            \App\Enums\BookingStatus::Confirmed->value,
            \App\Enums\BookingStatus::InProgress->value,
        ];

        $bookingQuery = \App\Models\Booking::where('barber_id', $barber->id)
            ->where('booking_date', $validated['leave_date'])
            ->whereIn('status', $activeStatuses);

        // Nếu nghỉ partial, chỉ check booking trong khoảng giờ đó
        if ($validated['type'] === 'partial' && !empty($validated['start_time']) && !empty($validated['end_time'])) {
            $bookingQuery->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            });
        }

        if ($bookingQuery->exists()) {
            return back()->withErrors(['leave_date' => 'Ngày này đã có lịch hẹn, không thể đăng ký nghỉ. Vui lòng liên hệ Admin.'])->withInput();
        }

        $this->leaveService->store($barber->id, $validated);

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
