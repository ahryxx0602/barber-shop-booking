<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    public function __construct(private CacheService $cacheService) {}
    public function create(array $data, ?UploadedFile $image = null): Service
    {
        if ($image) {
            $data['image'] = $image->store('services', 'public');
        }

        $service = Service::create($data);

        $this->cacheService->clearServiceCache();

        return $service;
    }

    public function update(Service $service, array $data, ?UploadedFile $image = null): Service
    {
        if ($image) {
            $this->deleteImage($service);
            $data['image'] = $image->store('services', 'public');
        }

        $service->update($data);

        $this->cacheService->clearServiceCache();

        return $service->fresh();
    }

    public function delete(Service $service): void
    {
        $this->deleteImage($service);
        $service->delete();

        $this->cacheService->clearServiceCache();
    }

    protected function deleteImage(Service $service): void
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
    }
}
