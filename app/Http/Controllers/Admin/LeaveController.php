<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Models\BarberLeave;
use App\Services\Barber\BarberLeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function __construct(
        protected BarberLeaveService $leaveService,
    ) {}

    /**
     * Danh sách tất cả đơn xin nghỉ — lọc theo trạng thái.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status');

        $leaves = $this->leaveService->getAllLeaves($statusFilter);
        $pendingCount = $this->leaveService->pendingCount();

        return view('admin.leaves.index', compact('leaves', 'pendingCount', 'statusFilter'));
    }

    /**
     * Admin duyệt đơn nghỉ.
     */
    public function approve(Request $request, BarberLeave $leave): RedirectResponse
    {
        if ($leave->status !== LeaveStatus::Pending) {
            return back()->withErrors(['leave' => 'Đơn nghỉ này đã được xử lý.']);
        }

        $this->leaveService->approve(
            $leave,
            $request->user()->id,
            $request->input('admin_note'),
        );

        return back()->with('success', 'Đã duyệt đơn xin nghỉ của ' . $leave->barber->user->name . '.');
    }

    /**
     * Admin từ chối đơn nghỉ.
     */
    public function reject(Request $request, BarberLeave $leave): RedirectResponse
    {
        if ($leave->status !== LeaveStatus::Pending) {
            return back()->withErrors(['leave' => 'Đơn nghỉ này đã được xử lý.']);
        }

        $request->validate([
            'admin_note' => 'nullable|string|max:255',
        ]);

        $this->leaveService->reject(
            $leave,
            $request->user()->id,
            $request->input('admin_note'),
        );

        return back()->with('success', 'Đã từ chối đơn xin nghỉ.');
    }
}
