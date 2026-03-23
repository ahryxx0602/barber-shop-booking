<?php

/**
 * DTO: UpdateScheduleData
 *
 * Đóng gói dữ liệu cập nhật lịch làm việc cho barber.
 * Chứa mảng ScheduleItemData[] (7 ngày trong tuần).
 * Thay thế array $schedulesData trong ScheduleService::updateSchedule().
 *
 * Dùng bởi: Barber\ScheduleController + Admin\ScheduleController → ScheduleService::updateSchedule()
 */

namespace App\DTOs;

readonly class UpdateScheduleData
{
    /**
     * @param ScheduleItemData[] $schedules
     */
    public function __construct(
        public array $schedules,
    ) {}

    /**
     * Tạo DTO từ mảng schedules (đã validated).
     */
    public static function fromArray(array $schedulesArray): self
    {
        $items = array_map(
            fn (array $item) => ScheduleItemData::fromArray($item),
            $schedulesArray
        );

        return new self(schedules: $items);
    }
}
