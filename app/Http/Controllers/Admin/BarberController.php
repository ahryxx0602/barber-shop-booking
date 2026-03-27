<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\CreateBarberData;
use App\DTOs\Admin\UpdateBarberData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBarberRequest;
use App\Http\Requests\Admin\UpdateBarberRequest;
use App\Models\Barber;
use App\Repositories\Contracts\Admin\BarberRepositoryInterface;
use App\Repositories\Contracts\Admin\BranchRepositoryInterface;
use App\Services\BarberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarberController extends Controller
{
    public function __construct(
        protected BarberService $barberService,
        protected BarberRepositoryInterface $barberRepo,
        protected BranchRepositoryInterface $branchRepo,
    ) {}

    public function index(Request $request): View
    {
        $barbers = $this->barberRepo->paginateWithFilters(
            $request->only(['branch_id', 'search']),
            10
        );
        $branches = $this->branchRepo->getActiveBranches();

        return view('admin.barbers.index', compact('barbers', 'branches'));
    }

    public function create(): View
    {
        $branches = $this->branchRepo->getActiveBranches();
        return view('admin.barbers.create', compact('branches'));
    }

    public function store(StoreBarberRequest $request): RedirectResponse
    {
        $this->barberService->create(
            CreateBarberData::fromRequest($request),
            $request->file('avatar'),
        );

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt đã được thêm thành công.');
    }

    public function edit(Barber $barber): View
    {
        $barber->load('user');
        $branches = $this->branchRepo->getActiveBranches();
        return view('admin.barbers.edit', compact('barber', 'branches'));
    }

    public function update(UpdateBarberRequest $request, Barber $barber): RedirectResponse
    {
        $this->barberService->update(
            $barber,
            UpdateBarberData::fromRequest($request),
            $request->file('avatar'),
        );

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt đã được cập nhật.');
    }

    public function destroy(Barber $barber): RedirectResponse
    {
        $this->barberService->delete($barber);

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt đã được xóa.');
    }
}
