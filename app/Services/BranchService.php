<?php

namespace App\Services;

use App\DTOs\CreateBranchData;
use App\DTOs\UpdateBranchData;
use App\Models\Branch;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BranchService
{
    public function create(CreateBranchData $data, ?UploadedFile $image = null): Branch
    {
        $branchData = [
            'name'        => $data->name,
            'address'     => $data->address,
            'phone'       => $data->phone,
            'description' => $data->description,
            'is_active'   => $data->is_active,
        ];

        if ($image) {
            $branchData['image'] = $image->store('branches', 'public');
        }

        return Branch::create($branchData);
    }

    public function update(Branch $branch, UpdateBranchData $data, ?UploadedFile $image = null): Branch
    {
        $branchData = [
            'name'        => $data->name,
            'address'     => $data->address,
            'phone'       => $data->phone,
            'description' => $data->description,
            'is_active'   => $data->is_active,
        ];

        if ($image) {
            // Xóa ảnh cũ nếu có
            if ($branch->image) {
                Storage::disk('public')->delete($branch->image);
            }
            $branchData['image'] = $image->store('branches', 'public');
        }

        $branch->update($branchData);

        return $branch->fresh();
    }

    public function delete(Branch $branch): void
    {
        // Xóa ảnh nếu có
        if ($branch->image) {
            Storage::disk('public')->delete($branch->image);
        }

        // Gỡ barber khỏi chi nhánh trước khi xóa (set null)
        $branch->barbers()->update(['branch_id' => null]);

        $branch->delete();
    }
}
