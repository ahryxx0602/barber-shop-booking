<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function confirm(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === 'pending';
    }

    public function reject(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === 'pending';
    }

    public function start(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === 'confirmed';
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === 'in_progress';
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->id !== $booking->customer_id) {
            return false;
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return false;
        }

        $appointmentTime = \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->start_time);

        return now()->diffInMinutes($appointmentTime, false) >= 120;
    }
}
