<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $selectedBranchId = $request->input('branch_id');

        // Lấy barbers, filter theo branch nếu có
        $barbersQuery = Barber::with('user', 'branch')->where('is_active', true)->orderBy('id');
        if ($selectedBranchId) {
            $barbersQuery->where('branch_id', $selectedBranchId);
        }
        $barbers = $barbersQuery->get();

        $selectedBarberId = $request->input('barber_id', $barbers->first()?->id);

        $weekStart = $request->input('week')
            ? Carbon::parse($request->input('week'))->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $bookings = collect();
        $selectedBarber = null;

        if ($selectedBarberId) {
            $selectedBarber = $barbers->firstWhere('id', $selectedBarberId);

            $bookings = Booking::where('barber_id', $selectedBarberId)
                ->whereBetween('booking_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->with(['customer', 'services'])
                ->orderBy('booking_date')
                ->orderBy('start_time')
                ->get();
        }

        $days = [];
        for ($d = $weekStart->copy(); $d->lte($weekEnd); $d->addDay()) {
            $dateStr = $d->toDateString();
            $days[$dateStr] = [
                'label' => $d->locale('vi')->isoFormat('ddd, DD/MM'),
                'bookings' => $bookings->filter(fn ($b) => $b->booking_date->format('Y-m-d') === $dateStr)->values(),
                'isToday' => $d->isToday(),
            ];
        }

        $stats = [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', BookingStatus::Pending)->count(),
            'completed' => $bookings->where('status', BookingStatus::Completed)->count(),
            'revenue' => $bookings->whereIn('status', [BookingStatus::Confirmed, BookingStatus::InProgress, BookingStatus::Completed])->sum('total_price'),
        ];

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        // Nhóm barbers theo chi nhánh để hiển thị
        $barbersByBranch = $barbers->groupBy(fn ($b) => $b->branch?->name ?? 'Chưa gán');

        return view('admin.bookings.index', compact(
            'barbers', 'barbersByBranch', 'branches', 'selectedBranchId',
            'selectedBarberId', 'selectedBarber',
            'days', 'weekStart', 'weekEnd', 'stats', 'prevWeek', 'nextWeek'
        ));
    }
}
