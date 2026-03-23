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
    /**
     * Đăng ký chờ slot cho barber vào ngày cụ thể.
     */
    public function register(User $user, int $barberId, string $desiredDate, ?string $desiredTime = null): Waitlist
    {
        // Kiểm tra đã đăng ký chưa (tránh trùng)
        $existing = Waitlist::where('user_id', $user->id)
            ->where('barber_id', $barberId)
            ->where('desired_date', $desiredDate)
            ->where('status', WaitlistStatus::Waiting)
            ->first();

        if ($existing) {
            return $existing; // Đã đăng ký rồi
        }

        return Waitlist::create([
            'user_id' => $user->id,
            'barber_id' => $barberId,
            'desired_date' => $desiredDate,
            'desired_time' => $desiredTime,
            'status' => WaitlistStatus::Waiting,
        ]);
    }

    /**
     * Khi booking bị hủy, thông báo cho người đang chờ.
     * Gọi method này từ Listener khi BookingCancelled.
     */
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
            // Đánh dấu đã thông báo
            $waiter->update([
                'status' => WaitlistStatus::Notified,
                'notified_at' => now(),
            ]);

            Log::channel('booking')->info('Waitlist notified', [
                'user_id' => $waiter->user_id,
                'barber_id' => $barberId,
                'date' => $date,
            ]);

            // TODO: Gửi notification thực tế (email/in-app) khi có notification system
        }
    }

    /**
     * Dọn waitlist quá hạn (ngày đã qua).
     */
    public function expireOld(): int
    {
        return Waitlist::where('status', WaitlistStatus::Waiting)
            ->where('desired_date', '<', Carbon::today())
            ->update(['status' => WaitlistStatus::Expired]);
    }
}
