<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\CreateBranchData;
use App\DTOs\Admin\UpdateBranchData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranchRequest;
use App\Http\Requests\Admin\UpdateBranchRequest;
use App\Models\Branch;
use App\Repositories\Contracts\Admin\BranchRepositoryInterface;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService,
        protected BranchRepositoryInterface $branchRepo,
    ) {}

    public function index(Request $request): View
    {
        // Xử lý tháng filter (fallback về tháng hiện tại nếu rỗng/không hợp lệ)
        $monthInput = $request->input('month');
        try {
            $selectedMonth = $monthInput ? $monthInput : now()->format('Y-m');
            $filterDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $selectedMonth = now()->format('Y-m');
            $filterDate = now();
        }

        $branches = $this->branchRepo->paginateWithBarberCount(10);

        // Revenue query — giữ nguyên (reporting logic, không phải CRUD)
        $branchIds = $branches->pluck('id');
        $revenues = \DB::table('bookings')
            ->join('barbers', 'bookings.barber_id', '=', 'barbers.id')
            ->whereIn('barbers.branch_id', $branchIds)
            ->whereIn('bookings.status', ['completed', 'confirmed', 'in_progress'])
            ->whereMonth('bookings.booking_date', $filterDate->month)
            ->whereYear('bookings.booking_date', $filterDate->year)
            ->groupBy('barbers.branch_id')
            ->select('barbers.branch_id', \DB::raw('SUM(bookings.total_price) as revenue'))
            ->pluck('revenue', 'branch_id');

        $totalRevenue = $revenues->sum();

        $stats = [
            'total'         => Branch::count(),
            'active'        => Branch::where('is_active', true)->count(),
            'total_barbers' => \App\Models\Barber::whereNotNull('branch_id')->count(),
            'total_revenue' => $totalRevenue,
        ];

        return view('admin.branches.index', compact('branches', 'stats', 'revenues', 'selectedMonth'));
    }

    public function create(): View
    {
        return view('admin.branches.create');
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $this->branchService->create(
            CreateBranchData::fromRequest($request),
            $request->file('image'),
        );

        return redirect()->route('admin.branches.index')
            ->with('success', 'Chi nhánh đã được thêm thành công.');
    }

    public function edit(Branch $branch): View
    {
        $branch->load('barbers.user');
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->branchService->update(
            $branch,
            UpdateBranchData::fromRequest($request),
            $request->file('image'),
        );

        return redirect()->route('admin.branches.index')
            ->with('success', 'Chi nhánh đã được cập nhật.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->branchService->delete($branch);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Chi nhánh đã được xóa.');
    }
}
