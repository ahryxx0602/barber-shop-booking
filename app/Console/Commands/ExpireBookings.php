<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Enums\TimeSlotStatus;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Tự động huỷ booking ở trạng thái Pending quá 30 phút.
 * Giải phóng lại time slot để người khác có thể đặt.
 */
class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire {--minutes=30 : Số phút tối đa cho booking pending}';

    protected $description = 'Tự động huỷ booking pending quá thời hạn cho phép';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');

        $expiredBookings = Booking::where('status', BookingStatus::Pending)
            ->where('created_at', '<=', now()->subMinutes($minutes))
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info("✓ Không có booking nào cần huỷ.");
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'status'        => BookingStatus::Cancelled,
                    'cancelled_at'  => now(),
                    'cancel_reason' => 'Tự động huỷ — quá thời hạn chờ xác nhận',
                ]);

                // Giải phóng time slot
                if ($booking->time_slot_id) {
                    $booking->timeSlot()->update([
                        'status' => TimeSlotStatus::Available,
                    ]);
                }
            });

            $count++;
            $this->line("  Huỷ: {$booking->booking_code}");
        }

        Log::channel('booking')->info("Auto-expired {$count} pending bookings", [
            'booking_codes' => $expiredBookings->pluck('booking_code')->toArray(),
        ]);

        $this->info("✓ Đã tự động huỷ {$count} booking quá hạn.");

        return Command::SUCCESS;
    }
}
