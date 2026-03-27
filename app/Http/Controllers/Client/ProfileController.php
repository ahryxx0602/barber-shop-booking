<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Services\Client\LoyaltyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $bookings = $user->bookings()
            ->with(['barber.user', 'services', 'timeSlot', 'review'])
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->get();

        $upcomingBookings = $bookings->filter(fn ($b) => $b->booking_date >= now()->toDateString() && !in_array($b->status, [\App\Enums\BookingStatus::Cancelled, \App\Enums\BookingStatus::Completed]));
        $pastBookings = $bookings->filter(fn ($b) => $b->booking_date < now()->toDateString() || in_array($b->status, [\App\Enums\BookingStatus::Cancelled, \App\Enums\BookingStatus::Completed]));

        // Lấy lịch sử mua hàng e-commerce
        $orders = $user->orders()
            ->with(['items.product'])
            ->latest()
            ->get();

        return view('client.profile.show', compact('user', 'upcomingBookings', 'pastBookings', 'orders'));
    }

    public function edit(Request $request): View
    {
        return view('client.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        $user->fill(collect($validated)->except('avatar')->toArray());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('client.profile.show')->with('success', 'Cập nhật thông tin thành công!');
    }

    public function loyalty(Request $request, LoyaltyService $loyaltyService): View
    {
        $user = $request->user();
        $balance = $loyaltyService->getBalance($user);
        $history = $loyaltyService->getHistory($user);

        return view('client.profile.loyalty', compact('user', 'balance', 'history'));
    }

    public function favorites(Request $request): View
    {
        $user = $request->user();
        $favoriteBarbers = $user->favoriteBarbers()->with('user')->get();

        return view('client.profile.favorites', compact('user', 'favoriteBarbers'));
    }
}

