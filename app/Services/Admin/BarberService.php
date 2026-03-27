<?php

namespace App\Services\Admin;

use App\DTOs\Admin\CreateBarberData;
use App\DTOs\Admin\UpdateBarberData;
use App\Models\Barber;
use App\Models\User;
use App\Repositories\Contracts\Admin\BarberRepositoryInterface;
use App\Repositories\Contracts\Admin\UserRepositoryInterface;
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
            // Tối ưu N+1 Query: Eager load user để khi truy cập $barber->user ở dưới không bị query lại
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
            // Tối ưu N+1 Query: Eager load user để tránh N+1 khi truy cập $barber->user
            $barber->loadMissing('user');
            
            $this->deleteAvatar($barber->user);
            $barber->user->delete();
        });

        $this->cacheService->clearBarberCache();
    }

    protected function deleteAvatar(User $user): void
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
    }
}
