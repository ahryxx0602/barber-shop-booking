<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function confirm(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === BookingStatus::Pending;
    }

    public function reject(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === BookingStatus::Pending;
    }

    public function start(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === BookingStatus::Confirmed;
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $user->barber && $user->barber->id === $booking->barber_id
            && $booking->status === BookingStatus::InProgress;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->id !== $booking->customer_id) {
            return false;
        }

        if (!in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) {
            return false;
        }

        $appointmentTime = \Carbon\Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);

        return now()->diffInMinutes($appointmentTime, false) >= 120;
    }
}
