<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBarberRequest;
use App\Http\Requests\Admin\UpdateBarberRequest;
use App\Models\Barber;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BarberController extends Controller
{
    public function index(): View
    {
        $barbers = Barber::with('user')->latest()->paginate(10);
        return view('admin.barbers.index', compact('barbers'));
    }

    public function create(): View
    {
        return view('admin.barbers.create');
    }

    public function store(StoreBarberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            // 1. Tạo User với role = barber
            $userData = [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'barber',
                'phone'    => $data['phone'] ?? null,
            ];

            if ($request->hasFile('avatar')) {
                $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create($userData);

            // 2. Tạo Barber liên kết
            Barber::create([
                'user_id'          => $user->id,
                'bio'              => $data['bio'] ?? null,
                'experience_years' => $data['experience_years'],
                'is_active'        => $data['is_active'] ?? true,
            ]);
        });

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt đã được thêm thành công.');
    }

    public function edit(Barber $barber): View
    {
        $barber->load('user');
        return view('admin.barbers.edit', compact('barber'));
    }

    public function update(UpdateBarberRequest $request, Barber $barber): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $barber) {
            // 1. Cập nhật User
            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            if ($request->hasFile('avatar')) {
                if ($barber->user->avatar) {
                    Storage::disk('public')->delete($barber->user->avatar);
                }
                $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $barber->user->update($userData);

            // 2. Cập nhật Barber
            $barber->update([
                'bio'              => $data['bio'] ?? null,
                'experience_years' => $data['experience_years'],
                'is_active'        => $data['is_active'] ?? false,
            ]);
        });

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt đã được cập nhật.');
    }

    public function destroy(Barber $barber): RedirectResponse
    {
        DB::transaction(function () use ($barber) {
            if ($barber->user->avatar) {
                Storage::disk('public')->delete($barber->user->avatar);
            }

            // Xóa barber record trước, rồi xóa user
            // Hoặc nếu có cascade, chỉ cần xóa user
            $barber->user->delete();
        });

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt đã được xóa.');
    }
}
