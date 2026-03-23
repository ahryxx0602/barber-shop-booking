<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class ReportService
{
    /**
     * Lấy thống kê tổng quan theo tháng.
     * Trả về tổng booking, doanh thu, khách mới + % so sánh tháng trước.
     */
    public function getMonthlyOverview(?Carbon $date = null): array
    {
        $date = $date ?? now();

        $currentMonthStart = $date->copy()->startOfMonth();
        $currentMonthEnd = $date->copy()->endOfMonth();
        $prevMonthStart = $date->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $date->copy()->subMonth()->endOfMonth();

        // Tổng booking
        $currentBookings = Booking::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $prevBookings = Booking::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();

        // Doanh thu (chỉ tính booking confirmed, in_progress, completed)
        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        $currentRevenue = Booking::whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_price');

        $prevRevenue = Booking::whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [$prevMonthStart, $prevMonthEnd])
            ->sum('total_price');

        // Khách hàng mới
        $currentNewCustomers = User::where('role', UserRole::Customer)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $prevNewCustomers = User::where('role', UserRole::Customer)
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->count();

        return [
            'month' => $date->format('m/Y'),
            'bookings' => [
                'total' => $currentBookings,
                'change' => $this->calculateChange($currentBookings, $prevBookings),
            ],
            'revenue' => [
                'total' => $currentRevenue,
                'change' => $this->calculateChange($currentRevenue, $prevRevenue),
            ],
            'newCustomers' => [
                'total' => $currentNewCustomers,
                'change' => $this->calculateChange($currentNewCustomers, $prevNewCustomers),
            ],
        ];
    }

    /**
     * Tính % thay đổi so với tháng trước.
     */
    private function calculateChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }
}
