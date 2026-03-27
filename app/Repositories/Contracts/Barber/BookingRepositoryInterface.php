<?php

namespace App\Repositories\Contracts\Barber;

use App\Enums\TimeSlotStatus;
use App\Models\TimeSlot;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Lấy bookings theo barber và khoảng tuần, eager load customer + services.
     */
    public function getByBarberAndWeek(int $barberId, string $startDate, string $endDate): Collection;

    /**
     * Lấy bookings theo barber + date range (cho FullCalendar).
     */
    public function getByBarberAndDateRange(int $barberId, ?string $start = null, ?string $end = null): Collection;

    /**
     * Lock slot để tránh race condition khi đặt lịch.
     */
    public function findSlotForUpdate(int $slotId): TimeSlot;

    /**
     * Cập nhật trạng thái slot.
     */
    public function updateSlotStatus(int $slotId, TimeSlotStatus $status): void;

    /**
     * Tìm slot khả dụng cho booking recurring.
     */
    public function findAvailableRecurringSlot(int $barberId, string $date, string $startTime): ?TimeSlot;

    /**
     * Lấy danh sách slot khả dụng cho ngày + barber (Client booking).
     */
    public function getAvailableSlots(int $barberId, string $date, bool $filterPast = false): \Illuminate\Support\Collection;

    /**
     * Kiểm tra có booking active trong ngày cho barber không.
     */
    public function hasActiveBookingsOnDate(int $barberId, string $date, ?string $startTime = null, ?string $endTime = null): bool;
}
