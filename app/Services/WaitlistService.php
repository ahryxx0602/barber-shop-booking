<?php

namespace App\Services;

use App\Enums\WaitlistStatus;
use App\Models\Barber;
use App\Models\User;
use App\Models\Waitlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WaitlistService
{
    public function register(User $user, int $barberId, string $desiredDate, ?string $desiredTime = null): Waitlist
    {
        $existing = Waitlist::where('user_id', $user->id)
            ->where('barber_id', $barberId)
            ->where('desired_date', $desiredDate)
            ->where('status', WaitlistStatus::Waiting)
            ->first();

        if ($existing) {
            return $existing;
        }

        return Waitlist::create([
            'user_id' => $user->id,
            'barber_id' => $barberId,
            'desired_date' => $desiredDate,
            'desired_time' => $desiredTime,
            'status' => WaitlistStatus::Waiting,
        ]);
    }

    public function notifyWaiters(int $barberId, string $date, ?string $time = null): void
    {
        $query = Waitlist::where('barber_id', $barberId)
            ->where('desired_date', $date)
            ->where('status', WaitlistStatus::Waiting);

        if ($time) {
            $query->where(function ($q) use ($time) {
                $q->where('desired_time', $time)
                    ->orWhereNull('desired_time');
            });
        }

        $waiters = $query->with('user')->get();

        foreach ($waiters as $waiter) {
            $waiter->update([
                'status' => WaitlistStatus::Notified,
                'notified_at' => now(),
            ]);

            Log::channel('booking')->info('Waitlist notified', [
                'user_id' => $waiter->user_id,
                'barber_id' => $barberId,
                'date' => $date,
            ]);
        }
    }

    public function expireOld(): int
    {
        return Waitlist::where('status', WaitlistStatus::Waiting)
            ->where('desired_date', '<', Carbon::today())
            ->update(['status' => WaitlistStatus::Expired]);
    }
}
