<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function index(): View
    {
        // Tối ưu N+1 Query (Issue #7): Gom nhiều truy vấn COUNT() riêng lẻ thành 2 câu query dùng selectRaw()
        // 1. Thống kê User
        $userStats = User::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as barbers,
            SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as customers
        ", [UserRole::Barber->value, UserRole::Customer->value])->first();

        $totalUsers     = $userStats->total;
        $totalBarbers   = $userStats->barbers;
        $totalCustomers = $userStats->customers;

        // 2. Thống kê Booking 
        $bookingStats = Booking::selectRaw("
            SUM(CASE WHEN DATE(booking_date) = ? THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending
        ", [today()->toDateString(), BookingStatus::Pending->value])->first();

        $todayBookings   = (int) $bookingStats->today;
        $pendingBookings = (int) $bookingStats->pending;

        // Doanh thu tháng
        $overview = $this->reportService->getMonthlyOverview();

        // Biểu đồ doanh thu 7 ngày gần nhất
        $revenueChart = $this->reportService->getDailyRevenue();
        // Chỉ lấy 7 ngày gần nhất
        $revenueChart['labels'] = array_slice($revenueChart['labels'], -7);
        $revenueChart['data']   = array_slice($revenueChart['data'], -7);

        // Bookings gần nhất
        $recentBookings = Booking::with(['customer', 'barber.user', 'services'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // Top thợ cắt tháng này
        $topBarbers = $this->reportService->getTopBarbers(3);

        return view('admin.dashboard', compact(
            'totalUsers', 'totalBarbers', 'totalCustomers',
            'todayBookings', 'pendingBookings',
            'overview', 'revenueChart', 'recentBookings', 'topBarbers'
        ));
    }
}