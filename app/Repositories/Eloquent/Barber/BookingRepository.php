<?php

namespace App\Repositories\Eloquent\Barber;

use App\Enums\BookingStatus;
use App\Enums\TimeSlotStatus;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Repositories\Contracts\Barber\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    public function getByBarberAndWeek(int $barberId, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('barber_id', $barberId)
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->with(['customer', 'services'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();
    }

    public function getByBarberAndDateRange(int $barberId, ?string $start = null, ?string $end = null): Collection
    {
        $query = $this->model
            ->where('barber_id', $barberId)
            ->with(['customer', 'services']);

        if ($start) {
            $query->where('booking_date', '>=', $start);
        }
        if ($end) {
            $query->where('booking_date', '<=', $end);
        }

        return $query->orderBy('booking_date')->orderBy('start_time')->get();
    }

    public function findSlotForUpdate(int $slotId): TimeSlot
    {
        return TimeSlot::lockForUpdate()->findOrFail($slotId);
    }

    public function updateSlotStatus(int $slotId, TimeSlotStatus $status): void
    {
        TimeSlot::where('id', $slotId)->update(['status' => $status]);
    }

    public function findAvailableRecurringSlot(int $barberId, string $date, string $startTime): ?TimeSlot
    {
        return TimeSlot::where('barber_id', $barberId)
            ->where('slot_date', $date)
            ->where('start_time', $startTime)
            ->where('status', TimeSlotStatus::Available)
            ->first();
    }

    public function getAvailableSlots(int $barberId, string $date, bool $filterPast = false): SupportCollection
    {
        $query = TimeSlot::where('barber_id', $barberId)
            ->where('slot_date', $date)
            ->where('status', TimeSlotStatus::Available);

        if ($filterPast) {
            // Chỉ hiển thị slot cách ít nhất 1 tiếng so với giờ hiện tại
            $minTime = now()->addHour()->format('H:i:s');
            $query->where('start_time', '>=', $minTime);
        }

        return $query->orderBy('start_time')
            ->get()
            ->map(fn ($slot) => [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'label' => \Carbon\Carbon::parse($slot->start_time)->format('H:i'),
            ]);
    }

    public function hasActiveBookingsOnDate(int $barberId, string $date, ?string $startTime = null, ?string $endTime = null): bool
    {
        $activeStatuses = [
            BookingStatus::Pending->value,
            BookingStatus::Confirmed->value,
            BookingStatus::InProgress->value,
        ];

        $query = $this->model
            ->where('barber_id', $barberId)
            ->where('booking_date', $date)
            ->whereIn('status', $activeStatuses);

        // Nếu nghỉ partial, chỉ check booking trong khoảng giờ đó
        if ($startTime && $endTime) {
            $query->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });
        }

        return $query->exists();
    }
}
