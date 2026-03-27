<?php

/**
 * DTO: CreateLeaveData
 *
 * Đóng gói dữ liệu cần thiết để tạo đơn xin nghỉ.
 * Thay thế array $data trong BarberLeaveService::store().
 *
 * Dùng bởi: Barber\LeaveController::store() → BarberLeaveService::store()
 */

namespace App\DTOs\Barber;

use App\Http\Requests\Barber\StoreLeaveRequest;

readonly class CreateLeaveData
{
    public function __construct(
        public string $leave_date,
        public string $type = 'full_day',
        public ?string $start_time = null,
        public ?string $end_time = null,
        public ?string $reason = null,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(StoreLeaveRequest $request): self
    {
        $data = $request->validated();

        return new self(
            leave_date: $data['leave_date'],
            type: $data['type'] ?? 'full_day',
            start_time: $data['start_time'] ?? null,
            end_time: $data['end_time'] ?? null,
            reason: $data['reason'] ?? null,
        );
    }

    /**
     * Chuyển DTO thành array để lưu vào DB.
     */
    public function toArray(): array
    {
        return [
            'leave_date' => $this->leave_date,
            'type'       => $this->type,
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
            'reason'     => $this->reason,
        ];
    }
}
