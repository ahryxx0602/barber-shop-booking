<?php

/**
 * DTO: ScheduleItemData
 *
 * Đại diện cho 1 ngày trong tuần của lịch làm việc.
 * Dùng làm DTO con bên trong UpdateScheduleData.
 * Mỗi item chứa day_of_week (0-6), is_working, start_time, end_time.
 *
 * Dùng bởi: UpdateScheduleData → ScheduleService::updateSchedule()
 */

namespace App\DTOs\Barber;

readonly class ScheduleItemData
{
    public function __construct(
        public int $day_of_week,
        public bool $is_working = false,
        public string $start_time = '08:00',
        public string $end_time = '18:00',
    ) {}

    /**
     * Tạo DTO từ mảng dữ liệu 1 ngày.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            day_of_week: (int) $data['day_of_week'],
            is_working: (bool) ($data['is_working'] ?? false),
            start_time: $data['start_time'] ?? '08:00',
            end_time: $data['end_time'] ?? '18:00',
        );
    }
}
