<?php

namespace App\Services;

use App\DTOs\Admin\CreateBarberData;
use App\DTOs\Admin\UpdateBarberData;
use App\Models\Barber;
use App\Models\User;
use App\Repositories\Contracts\Admin\BarberRepositoryInterface;
use App\Repositories\Contracts\Admin\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class BarberService
{
    public function __construct(
        private CacheService $cacheService,
        private BarberRepositoryInterface $barberRepo,
        private UserRepositoryInterface $userRepo,
    ) {}

    // ──────────────────── CRUD (Admin) ────────────────────

    public function create(CreateBarberData $data, ?UploadedFile $avatar = null): Barber
    {
        $barber = DB::transaction(function () use ($data, $avatar) {
            $userData = [
                'name'     => $data->name,
                'email'    => $data->email,
                'password' => Hash::make($data->password),
                'role'     => 'barber',
                'phone'    => $data->phone,
            ];

            if ($avatar) {
                $userData['avatar'] = $avatar->store('avatars', 'public');
            }

            $user = $this->userRepo->create($userData);

            return $this->barberRepo->create([
                'user_id'          => $user->id,
                'branch_id'        => $data->branch_id,
                'bio'              => $data->bio,
                'experience_years' => $data->experience_years,
                'is_active'        => $data->is_active,
            ]);
        });

        $this->cacheService->clearBarberCache();

        return $barber;
    }

    public function update(Barber $barber, UpdateBarberData $data, ?UploadedFile $avatar = null): Barber
    {
        $result = DB::transaction(function () use ($barber, $data, $avatar) {
            $barber->loadMissing('user');

            $userData = [
                'name'  => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
            ];

            if (!empty($data->password)) {
                $userData['password'] = Hash::make($data->password);
            }

            if ($avatar) {
                $this->deleteAvatar($barber->user);
                $userData['avatar'] = $avatar->store('avatars', 'public');
            }

            $this->userRepo->update($barber->user, $userData);

            $this->barberRepo->update($barber, [
                'branch_id'        => $data->branch_id,
                'bio'              => $data->bio,
                'experience_years' => $data->experience_years,
                'is_active'        => $data->is_active,
            ]);

            return $barber->fresh('user');
        });

        $this->cacheService->clearBarberCache();

        return $result;
    }

    public function delete(Barber $barber): void
    {
        DB::transaction(function () use ($barber) {
            $barber->loadMissing('user');

            $this->deleteAvatar($barber->user);
            $barber->user->delete();
        });

        $this->cacheService->clearBarberCache();
    }

    // ──────────────────── Queries (Client) ────────────────────

    public function getActiveBarbersWithFilters(array $filters = []): Collection
    {
        return Barber::with('user', 'branch')
            ->withCount('reviews')
            ->where('is_active', true)
            ->when(isset($filters['search']) && $filters['search'], function ($query) use ($filters) {
                $query->whereHas('user', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%');
                });
            })
            ->when(isset($filters['branch_id']) && $filters['branch_id'], function ($query) use ($filters) {
                $query->where('branch_id', $filters['branch_id']);
            })
            ->get();
    }

    public function loadBarberDetails(Barber $barber): void
    {
        $barber->load(['user', 'reviews.customer', 'workingSchedules']);
    }

    public function getAllBarbers(): Collection
    {
        return Barber::with('user', 'branch')->where('is_active', true)->get();
    }

    // ──────────────────── Private ────────────────────

    protected function deleteAvatar(User $user): void
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
    }
}
