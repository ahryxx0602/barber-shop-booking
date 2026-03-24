<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    /**
     * Trang lịch sử hoa hồng cá nhân của barber.
     */
    public function index(Request $request): View
    {
        $barber = $request->user()->barber;

        // Bộ lọc thời gian
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfMonth();

        // Thống kê tổng quan
        $stats = Commission::where('barber_id', $barber->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(SUM(commission_amount), 0) as total_commission,
                COALESCE(SUM(booking_amount), 0) as total_booking_amount,
                COUNT(*) as total_records,
                COALESCE(AVG(commission_rate), 0) as avg_rate
            ')
            ->first();

        // Lịch sử chi tiết (phân trang)
        $history = Commission::where('barber_id', $barber->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['booking:id,booking_code,total_price,booking_date', 'booking.customer:id,name', 'booking.services:id,name'])
            ->orderByDesc('created_at')
            ->paginate(15);

        // Dữ liệu biểu đồ 6 tháng gần nhất
        $chartData = [];
        $chartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->format('m/Y');
            $monthTotal = Commission::where('barber_id', $barber->id)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('commission_amount');
            $chartData[] = (float) $monthTotal;
        }

        return view('barber.commissions.index', compact(
            'barber', 'stats', 'history', 'startDate', 'endDate',
            'chartLabels', 'chartData'
        ));
    }
}
