<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Services\LoyaltyService;
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

        $upcomingBookings = $bookings->filter(fn ($b) => $b->booking_date >= now()->toDateString() && !in_array($b->status, [BookingStatus::Cancelled, BookingStatus::Completed]));
        $pastBookings = $bookings->filter(fn ($b) => $b->booking_date < now()->toDateString() || in_array($b->status, [BookingStatus::Cancelled, BookingStatus::Completed]));

        return view('client.profile.show', compact('user', 'upcomingBookings', 'pastBookings'));
    }

    public function edit(Request $request): View
    {
        return view('client.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

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
}

