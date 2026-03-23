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
        // Stats cards — tổng quan nhanh
        $totalUsers     = User::count();
        $totalBarbers   = User::where('role', UserRole::Barber)->count();
        $totalCustomers = User::where('role', UserRole::Customer)->count();

        // Booking stats
        $todayBookings    = Booking::whereDate('booking_date', today())->count();
        $pendingBookings  = Booking::where('status', BookingStatus::Pending)->count();

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