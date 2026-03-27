<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\CreateServiceData;
use App\DTOs\Admin\UpdateServiceData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use App\Repositories\Contracts\Admin\ServiceRepositoryInterface;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService,
        protected ServiceRepositoryInterface $serviceRepo,
    ) {}

    public function index(): View
    {
        $services = $this->serviceRepo->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $this->serviceService->create(
            CreateServiceData::fromRequest($request),
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
            UpdateServiceData::fromRequest($request),
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
