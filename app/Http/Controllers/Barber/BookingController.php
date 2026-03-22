<?php

namespace App\Http\Controllers\Barber;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected BookingService $bookingService,
    ) {}

    public function index(Request $request): View
    {
        $barber = $request->user()->barber;

        $weekStart = $request->input('week')
            ? Carbon::parse($request->input('week'))->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $bookings = Booking::where('barber_id', $barber->id)
            ->whereBetween('booking_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->with(['customer', 'services'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();

        $days = [];
        for ($d = $weekStart->copy(); $d->lte($weekEnd); $d->addDay()) {
            $dateStr = $d->toDateString();
            $days[$dateStr] = [
                'label' => $d->locale('vi')->isoFormat('ddd, DD/MM'),
                'bookings' => $bookings->where('booking_date', $dateStr)->values(),
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

        return view('barber.bookings.index', compact('days', 'weekStart', 'weekEnd', 'stats', 'prevWeek', 'nextWeek'));
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        $this->authorize('confirm', $booking);

        $this->bookingService->confirm($booking);

        return back()->with('success', 'Da xac nhan lich hen.');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('reject', $booking);

        $this->bookingService->reject($booking, $request->input('cancel_reason'));

        return back()->with('success', 'Da tu choi lich hen.');
    }

    public function start(Booking $booking): RedirectResponse
    {
        $this->authorize('start', $booking);

        $this->bookingService->start($booking);

        return back()->with('success', 'Da bat dau phuc vu.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $this->authorize('complete', $booking);

        $this->bookingService->complete($booking);

        return back()->with('success', 'Da hoan thanh lich hen.');
    }
}
