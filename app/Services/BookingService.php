<?php

namespace App\Services;

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
    public function create(array $data, User $customer): Booking
    {
        return DB::transaction(function () use ($data, $customer) {
            $slot = TimeSlot::lockForUpdate()->findOrFail($data['time_slot_id']);

            if ($slot->status !== 'available') {
                throw new SlotNotAvailableException('Slot nay vua duoc dat, vui long chon lai.');
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
                'status' => 'pending',
            ]);

            foreach ($services as $service) {
                $booking->services()->attach($service->id, [
                    'price_snapshot' => $service->price,
                    'duration_snapshot' => $service->duration_minutes,
                ]);
            }

            $slot->update(['status' => 'booked']);

            return $booking;
        });
    }

    protected function generateCode(): string
    {
        return 'BB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
