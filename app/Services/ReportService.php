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
     * Lấy doanh thu theo ngày.
     * - Không truyền $month/$year → 30 ngày gần nhất.
     * - Truyền $month + $year → các ngày trong tháng đó.
     */
    public function getDailyRevenue(?int $month = null, ?int $year = null): array
    {
        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
        }

        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        // Query doanh thu group by ngày
        $revenues = Booking::whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->selectRaw('DATE(booking_date) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Fill ngày trống bằng 0
        $labels = [];
        $data = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $labels[] = $current->format('d/m');
            $data[] = (float) ($revenues[$dateKey] ?? 0);
            $current->addDay();
        }

        return compact('labels', 'data');
    }

    /**
     * Lấy doanh thu theo từng tháng trong năm.
     * Trả về ['labels' => ['T1', 'T2', ...], 'data' => [...]]
     */
    public function getMonthlyRevenue(int $year): array
    {
        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        $revenues = Booking::whereIn('status', $revenueStatuses)
            ->whereYear('booking_date', $year)
            ->selectRaw('MONTH(booking_date) as month, SUM(total_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = 'T' . $m;
            $data[] = (float) ($revenues[$m] ?? 0);
        }

        return compact('labels', 'data');
    }

    /**
     * Lấy danh sách các năm có booking để hiển thị dropdown.
     */
    public function getAvailableYears(): array
    {
        $years = Booking::selectRaw('DISTINCT YEAR(booking_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Đảm bảo luôn có năm hiện tại
        $currentYear = (int) now()->format('Y');
        if (!in_array($currentYear, $years)) {
            array_unshift($years, $currentYear);
        }

        return $years;
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
