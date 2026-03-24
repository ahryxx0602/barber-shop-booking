<?php

namespace App\Http\Controllers\Barber;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BarberLeave;
use App\Models\Commission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $barber = $request->user()->barber;
        $date = $request->input('date', now()->toDateString());

        $bookings = Booking::where('barber_id', $barber->id)
            ->where('booking_date', $date)
            ->with(['customer', 'services'])
            ->orderBy('start_time')
            ->get();

        // Thống kê ngày hiện tại
        $stats = [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', BookingStatus::Pending)->count(),
            'confirmed' => $bookings->where('status', BookingStatus::Confirmed)->count(),
            'in_progress' => $bookings->where('status', BookingStatus::InProgress)->count(),
            'completed' => $bookings->where('status', BookingStatus::Completed)->count(),
            'cancelled' => $bookings->where('status', BookingStatus::Cancelled)->count(),
            'revenue' => $bookings->whereIn('status', [BookingStatus::Confirmed, BookingStatus::InProgress, BookingStatus::Completed])->sum('total_price'),
        ];

        // ====== THỐNG KÊ CÁ NHÂN (12.4) ======
        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        // Thống kê tháng hiện tại
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $monthlyStats = Booking::where('barber_id', $barber->id)
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->selectRaw("
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status IN ('confirmed','in_progress','completed') THEN total_price ELSE 0 END) as total_revenue,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count
            ")
            ->first();

        // Tháng trước (so sánh)
        $prevMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $prevMonthEnd = now()->subMonth()->endOfMonth()->toDateString();

        $prevMonthRevenue = Booking::where('barber_id', $barber->id)
            ->whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [$prevMonthStart, $prevMonthEnd])
            ->sum('total_price');

        $revenueChange = $prevMonthRevenue > 0
            ? round(($monthlyStats->total_revenue - $prevMonthRevenue) / $prevMonthRevenue * 100, 1)
            : ($monthlyStats->total_revenue > 0 ? 100 : 0);

        // Rating hiện tại
        $barberRating = $barber->rating ?? 0;
        $totalReviews = $barber->reviews()->count();

        // Tỷ lệ hoàn thành (tháng này)
        $completionRate = $monthlyStats->total_bookings > 0
            ? round(($monthlyStats->completed_count / ($monthlyStats->total_bookings - $monthlyStats->cancelled_count)) * 100, 1)
            : 0;

        // Doanh thu 7 ngày gần nhất (cho biểu đồ mini)
        $last7Days = Booking::where('barber_id', $barber->id)
            ->whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [now()->subDays(6)->toDateString(), now()->toDateString()])
            ->selectRaw('DATE(booking_date) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartData[] = (float) ($last7Days[$d] ?? 0);
        }

        // Booking sắp tới (hôm nay, chưa hoàn thành)
        $upcomingBookings = Booking::where('barber_id', $barber->id)
            ->where('booking_date', now()->toDateString())
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Pending])
            ->with(['customer', 'services'])
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Ngày nghỉ sắp tới
        $upcomingLeaves = BarberLeave::where('barber_id', $barber->id)
            ->where('leave_date', '>=', now()->toDateString())
            ->orderBy('leave_date')
            ->limit(3)
            ->get();

        // Hoa hồng tháng hiện tại
        $commissionStats = Commission::where('barber_id', $barber->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('
                COALESCE(SUM(commission_amount), 0) as total_commission,
                COUNT(*) as commission_count
            ')
            ->first();

        $personalStats = [
            'monthly_revenue'      => (float) $monthlyStats->total_revenue,
            'revenue_change'       => $revenueChange,
            'monthly_bookings'     => (int) $monthlyStats->total_bookings,
            'completed_count'      => (int) $monthlyStats->completed_count,
            'completion_rate'      => $completionRate,
            'rating'               => $barberRating,
            'total_reviews'        => $totalReviews,
            'chart_labels'         => $chartLabels,
            'chart_data'           => $chartData,
            'commission_rate'      => (float) $barber->commission_rate,
            'monthly_commission'   => (float) $commissionStats->total_commission,
            'commission_count'     => (int) $commissionStats->commission_count,
        ];

        return view('barber.dashboard', compact(
            'bookings', 'date', 'stats', 'personalStats',
            'upcomingBookings', 'upcomingLeaves'
        ));
    }
}
