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
    ) {
    }

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
                'bookings' => $bookings->filter(fn($b) => $b->booking_date->format('Y-m-d') === $dateStr)->values(),
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

        $this->bookingService->confirm($booking); // Policy check

        return back()->with('success', 'Đã xác nhận lịch hẹn.');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('reject', $booking); // Policy check

        $this->bookingService->reject($booking, $request->input('cancel_reason'));

        return back()->with('success', 'Đã từ chối lịch hẹn.');
    }

    public function start(Booking $booking): RedirectResponse
    {
        $this->authorize('start', $booking); // Policy check

        $this->bookingService->start($booking);

        return back()->with('success', 'Đã bắt đầu phục vụ.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $this->authorize('complete', $booking); // Policy check

        $this->bookingService->complete($booking);

        return back()->with('success', 'Đã hoàn thành lịch hẹn.');
    }

    /**
     * Trang lịch trực quan (Calendar View) cho thợ cắt
     */
    public function calendar(): View
    {
        return view('barber.bookings.calendar');
    }

    /**
     * API trả về danh sách events cho FullCalendar (JSON)
     * Params: start, end (ISO date strings từ FullCalendar)
     */
    public function events(Request $request): \Illuminate\Http\JsonResponse
    {
        $barber = $request->user()->barber;

        $query = Booking::where('barber_id', $barber->id)
            ->with(['customer', 'services']);

        // FullCalendar gửi start/end param dạng ISO string
        if ($request->filled('start')) {
            $query->where('booking_date', '>=', Carbon::parse($request->input('start'))->toDateString());
        }
        if ($request->filled('end')) {
            $query->where('booking_date', '<=', Carbon::parse($request->input('end'))->toDateString());
        }

        $bookings = $query->orderBy('booking_date')->orderBy('start_time')->get();

        // Map sang format FullCalendar events
        $events = $bookings->map(function (Booking $booking) {
            // Kết hợp booking_date + start_time / end_time thành datetime
            $dateStr = $booking->booking_date->format('Y-m-d');
            $start = Carbon::parse($dateStr . ' ' . $booking->start_time);
            $end = Carbon::parse($dateStr . ' ' . $booking->end_time);

            // Nếu end_time qua nửa đêm (end < start), giữ event trong cùng ngày
            if ($end->lte($start)) {
                $end = Carbon::parse($dateStr . ' 23:59:00');
            }

            // Gán màu theo trạng thái
            $colorMap = [
                BookingStatus::Pending->value     => ['bg' => '#f59e0b', 'border' => '#d97706'],
                BookingStatus::Confirmed->value   => ['bg' => '#3b82f6', 'border' => '#2563eb'],
                BookingStatus::InProgress->value  => ['bg' => '#8b5cf6', 'border' => '#7c3aed'],
                BookingStatus::Completed->value   => ['bg' => '#10b981', 'border' => '#059669'],
                BookingStatus::Cancelled->value   => ['bg' => '#ef4444', 'border' => '#dc2626'],
            ];

            $colors = $colorMap[$booking->status->value] ?? ['bg' => '#6b7280', 'border' => '#4b5563'];

            return [
                'id'              => $booking->id,
                'title'           => $booking->customer->name,
                'start'           => $start->format('Y-m-d\TH:i:s'),
                'end'             => $end->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $colors['bg'],
                'borderColor'     => $colors['border'],
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'booking_code' => $booking->booking_code,
                    'customer'     => $booking->customer->name,
                    'phone'        => $booking->customer->phone ?? '',
                    'services'     => $booking->services->pluck('name')->join(', '),
                    'status'       => $booking->status->value,
                    'status_label' => $booking->status->label(),
                    'total_price'  => number_format($booking->total_price, 0, ',', '.') . 'đ',
                    'note'         => $booking->note ?? '',
                    'start_time'   => Carbon::parse($booking->start_time)->format('H:i'),
                    'end_time'     => Carbon::parse($booking->end_time)->format('H:i'),
                ],
            ];
        });

        return response()->json($events);
    }
}
