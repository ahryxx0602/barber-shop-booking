<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateBranchData;
use App\DTOs\UpdateBranchData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranchRequest;
use App\Http\Requests\Admin\UpdateBranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService,
    ) {}

    public function index(): View
    {
        $branches = Branch::withCount('barbers')->latest()->paginate(10);

        $stats = [
            'total'         => Branch::count(),
            'active'        => Branch::where('is_active', true)->count(),
            'total_barbers' => \App\Models\Barber::whereNotNull('branch_id')->count(),
        ];

        return view('admin.branches.index', compact('branches', 'stats'));
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
