<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    public function create(array $data, ?UploadedFile $image = null): Service
    {
        if ($image) {
            $data['image'] = $image->store('services', 'public');
        }

        return Service::create($data);
    }

    public function update(Service $service, array $data, ?UploadedFile $image = null): Service
    {
        if ($image) {
            $this->deleteImage($service);
            $data['image'] = $image->store('services', 'public');
        }

        $service->update($data);

        return $service->fresh();
    }

    public function delete(Service $service): void
    {
        $this->deleteImage($service);
        $service->delete();
    }

    protected function deleteImage(Service $service): void
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
    }
}
