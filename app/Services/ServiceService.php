<?php

namespace App\Services;

use App\DTOs\Admin\CreateServiceData;
use App\DTOs\Admin\UpdateServiceData;
use App\Models\Service;
use App\Repositories\Contracts\Admin\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    public function __construct(
        private CacheService $cacheService,
        private ServiceRepositoryInterface $serviceRepo,
    ) {}

    // ──────────────────── CRUD (Admin) ────────────────────

    public function create(CreateServiceData $data, ?UploadedFile $image = null): Service
    {
        $serviceData = $data->toArray();

        if ($image) {
            $serviceData['image'] = $image->store('services', 'public');
        }

        $service = $this->serviceRepo->create($serviceData);

        $this->cacheService->clearServiceCache();

        return $service;
    }

    public function update(Service $service, UpdateServiceData $data, ?UploadedFile $image = null): Service
    {
        $serviceData = $data->toArray();

        if ($image) {
            $this->deleteImage($service);
            $serviceData['image'] = $image->store('services', 'public');
        }

        $result = $this->serviceRepo->update($service, $serviceData);

        $this->cacheService->clearServiceCache();

        return $result;
    }

    public function delete(Service $service): void
    {
        $this->deleteImage($service);
        $this->serviceRepo->delete($service);

        $this->cacheService->clearServiceCache();
    }

    // ──────────────────── Queries (Client) ────────────────────

    public function getActiveServices(): Collection
    {
        return Service::where('is_active', true)->get();
    }

    // ──────────────────── Private ────────────────────

    protected function deleteImage(Service $service): void
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
    }
}
