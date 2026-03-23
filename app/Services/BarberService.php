<?php

namespace App\Services;

use App\DTOs\CreateBarberData;
use App\DTOs\UpdateBarberData;
use App\Models\Barber;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class BarberService
{
    public function __construct(private CacheService $cacheService) {}
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

            $user = User::create($userData);

            return Barber::create([
                'user_id'          => $user->id,
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

            $barber->user->update($userData);

            $barber->update([
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
