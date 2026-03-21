<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBarberRequest;
use App\Http\Requests\Admin\UpdateBarberRequest;
use App\Models\Barber;
use App\Services\BarberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarberController extends Controller
{
    public function __construct(
        protected BarberService $barberService,
    ) {}

    public function index(): View
    {
        $barbers = Barber::with('user')->latest()->paginate(10);
        return view('admin.barbers.index', compact('barbers'));
    }

    public function create(): View
    {
        return view('admin.barbers.create');
    }

    public function store(StoreBarberRequest $request): RedirectResponse
    {
        $this->barberService->create(
            $request->validated(),
            $request->file('avatar'),
        );

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt đã được thêm thành công.');
    }

    public function edit(Barber $barber): View
    {
        $barber->load('user');
        return view('admin.barbers.edit', compact('barber'));
    }

    public function update(UpdateBarberRequest $request, Barber $barber): RedirectResponse
    {
        $this->barberService->update(
            $barber,
            $request->validated(),
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
