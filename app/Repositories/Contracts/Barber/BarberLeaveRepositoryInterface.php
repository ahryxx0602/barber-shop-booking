<?php

namespace App\Repositories\Contracts\Barber;

use App\Models\BarberLeave;
use Illuminate\Support\Collection;

interface BarberLeaveRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Lấy danh sách ngày nghỉ của barber trong khoảng thời gian.
     */
    public function getByBarber(int $barberId, ?string $from = null, ?string $to = null): Collection;

    /**
     * Lấy tất cả đơn nghỉ với eager load (cho Admin).
     */
    public function getAllWithRelations(?string $status = null): Collection;

    /**
     * Đếm số đơn chờ duyệt.
     */
    public function pendingCount(): int;

    /**
     * Kiểm tra barber đã đăng ký nghỉ ngày này chưa.
     */
    public function existsForBarberOnDate(int $barberId, string $date): bool;

    /**
     * Block time slots cho ngày nghỉ đã duyệt.
     */
    public function blockTimeSlots(BarberLeave $leave): void;

    /**
     * Khôi phục time slots khi huỷ ngày nghỉ đã duyệt.
     */
    public function unblockTimeSlots(BarberLeave $leave): void;
}
