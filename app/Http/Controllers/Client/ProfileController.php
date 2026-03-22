<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $bookings = $user->bookings()
            ->with(['barber.user', 'services', 'timeSlot'])
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
        ]);

        $user = $request->user();
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('client.profile.show')->with('success', 'Cap nhat thong tin thanh cong!');
    }
}
