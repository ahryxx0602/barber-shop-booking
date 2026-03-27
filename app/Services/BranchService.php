<?php

namespace App\Services;

use App\DTOs\Admin\CreateBranchData;
use App\DTOs\Admin\UpdateBranchData;
use App\Models\Branch;
use App\Repositories\Contracts\Admin\BranchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BranchService
{
    public function __construct(
        private BranchRepositoryInterface $branchRepo,
    ) {}

    // ──────────────────── CRUD (Admin) ────────────────────

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

        return $this->branchRepo->create($branchData);
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
            if ($branch->image) {
                Storage::disk('public')->delete($branch->image);
            }
            $branchData['image'] = $image->store('branches', 'public');
        }

        return $this->branchRepo->update($branch, $branchData);
    }

    public function delete(Branch $branch): void
    {
        if ($branch->image) {
            Storage::disk('public')->delete($branch->image);
        }

        $branch->barbers()->update(['branch_id' => null]);

        $this->branchRepo->delete($branch);
    }

    // ──────────────────── Queries (Client) ────────────────────

    public function getActiveBranches(): Collection
    {
        return Branch::where('is_active', true)->orderBy('name')->get();
    }
}
