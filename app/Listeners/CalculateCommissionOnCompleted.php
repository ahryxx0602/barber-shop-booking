<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Services\Admin\CommissionService;

class CalculateCommissionOnCompleted
{
    public function __construct(
        private CommissionService $commissionService,
    ) {}

    /**
     * Tự động tính hoa hồng khi booking hoàn thành.
     */
    public function handle(BookingCompleted $event): void
    {
        $this->commissionService->calculateForBooking($event->booking);
    }
}
