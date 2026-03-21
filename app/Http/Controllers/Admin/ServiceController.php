<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService,
    ) {}

    public function index(): View
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $this->serviceService->create(
            $request->validated(),
            $request->file('image'),
        );

        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được thêm thành công.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $this->serviceService->update(
            $service,
            $request->validated(),
            $request->file('image'),
        );

        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được cập nhật.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->serviceService->delete($service);

        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được xóa.');
    }
}
