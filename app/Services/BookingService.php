<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\TimeSlotStatus;
use App\Enums\UserRole;
use App\Events\BookingCancelled;
use App\Events\BookingCompleted;
use App\Events\BookingConfirmed;
use App\Exceptions\SlotNotAvailableException;
use App\Models\Booking;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function create(array $data, ?User $customer = null): Booking
    {
        return DB::transaction(function () use ($data, $customer) {
            $slot = TimeSlot::lockForUpdate()->findOrFail($data['time_slot_id']);

            if ($slot->status !== TimeSlotStatus::Available) {
                throw new SlotNotAvailableException('Slot nay vua duoc dat, vui long chon lai.');
            }

            if (!$customer) {
                $customer = $this->findOrCreateGuest($data);
            }

            $services = Service::whereIn('id', $data['service_ids'])->get();
            $totalPrice = $services->sum('price');
            $totalDuration = $services->sum('duration_minutes');
            $endTime = Carbon::parse($slot->start_time)->addMinutes($totalDuration)->format('H:i:s');

            $booking = Booking::create([
                'booking_code' => $this->generateCode(),
                'customer_id' => $customer->id,
                'barber_id' => $data['barber_id'],
                'time_slot_id' => $slot->id,
                'booking_date' => $slot->slot_date,
                'start_time' => $slot->start_time,
                'end_time' => $endTime,
                'total_price' => $totalPrice,
                'note' => $data['note'] ?? null,
                'status' => BookingStatus::Pending,
            ]);

            foreach ($services as $service) {
                $booking->services()->attach($service->id, [
                    'price_snapshot' => $service->price,
                    'duration_snapshot' => $service->duration_minutes,
                ]);
            }

            $slot->update(['status' => TimeSlotStatus::Booked]);

            return $booking;
        });
    }

    protected function findOrCreateGuest(array $data): User
    {
        return User::firstOrCreate(
            ['email' => $data['guest_email']],
            [
                'name' => $data['guest_name'],
                'phone' => $data['guest_phone'],
                'password' => bcrypt(Str::random(32)),
                'role' => UserRole::Customer,
            ]
        );
    }

    public function confirm(Booking $booking): Booking
    {
        $booking->update(['status' => BookingStatus::Confirmed]);

        event(new BookingConfirmed($booking));

        return $booking;
    }

    public function reject(Booking $booking, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancel_reason' => $reason ?? 'Thu tu choi lich hen',
            ]);

            $this->reopenSlot($booking);

            event(new BookingCancelled($booking));

            return $booking;
        });
    }

    public function start(Booking $booking): Booking
    {
        $booking->update(['status' => BookingStatus::InProgress]);

        return $booking;
    }

    public function complete(Booking $booking): Booking
    {
        $booking->update(['status' => BookingStatus::Completed]);

        event(new BookingCompleted($booking));

        return $booking;
    }

    public function cancel(Booking $booking, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancel_reason' => $reason ?? 'Khach hang huy lich',
            ]);

            $this->reopenSlot($booking);

            event(new BookingCancelled($booking));

            return $booking;
        });
    }

    protected function reopenSlot(Booking $booking): void
    {
        if ($booking->time_slot_id) {
            TimeSlot::where('id', $booking->time_slot_id)->update(['status' => TimeSlotStatus::Available]);
        }
    }

    protected function generateCode(): string
    {
        return 'BB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
