<?php

namespace App\Http\Controllers\Barber;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
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

        $stats = [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', BookingStatus::Pending)->count(),
            'confirmed' => $bookings->where('status', BookingStatus::Confirmed)->count(),
            'in_progress' => $bookings->where('status', BookingStatus::InProgress)->count(),
            'completed' => $bookings->where('status', BookingStatus::Completed)->count(),
            'cancelled' => $bookings->where('status', BookingStatus::Cancelled)->count(),
            'revenue' => $bookings->whereIn('status', [BookingStatus::Confirmed, BookingStatus::InProgress, BookingStatus::Completed])->sum('total_price'),
        ];

        return view('barber.dashboard', compact('bookings', 'date', 'stats'));
    }
}
