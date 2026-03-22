<?php

namespace App\Http\Controllers\Barber;

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
            'pending' => $bookings->where('status', 'pending')->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'in_progress' => $bookings->where('status', 'in_progress')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'revenue' => $bookings->whereIn('status', ['confirmed', 'in_progress', 'completed'])->sum('total_price'),
        ];

        return view('barber.dashboard', compact('bookings', 'date', 'stats'));
    }
}
