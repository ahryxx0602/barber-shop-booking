<?php

namespace App\Http\Controllers\Client;

use App\DTOs\CreateBookingData;
use App\Enums\TimeSlotStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreBookingRequest;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Services\BookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected BookingService $bookingService,
    ) {}

    public function create(): View
    {
        $services = Service::where('is_active', true)->get();
        $barbers = Barber::with('user')->where('is_active', true)->get();

        return view('client.booking.create', compact('services', 'barbers'));
    }

    public function getSlots(Request $request): JsonResponse
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = TimeSlot::where('barber_id', $request->barber_id)
            ->where('slot_date', $request->date)
            ->where('status', TimeSlotStatus::Available)
            ->when($request->date === now()->toDateString(), function ($query) {
                $query->where('start_time', '>', now()->format('H:i:s'));
            })
            ->orderBy('start_time')
            ->get()
            ->map(fn ($slot) => [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'label' => \Carbon\Carbon::parse($slot->start_time)->format('H:i'),
            ]);

        return response()->json($slots);
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $booking = $this->bookingService->create(
                CreateBookingData::fromRequest($request),
                $request->user()
            );

            // Redirect sang trang chọn phương thức thanh toán thay vì vào thẳng confirmation
            return redirect()->route('client.payment.show', $booking)
                ->with('success', 'Đặt lịch thành công! Vui lòng chọn phương thức thanh toán.');
        } catch (\App\Exceptions\SlotNotAvailableException $e) {
            return back()->withErrors(['time_slot_id' => $e->getMessage()])->withInput();
        }
    }

    public function confirmation(Booking $booking): View
    {
        $booking->load(['barber.user', 'services', 'timeSlot']);

        return view('client.booking.confirmation', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        $this->bookingService->cancel($booking, $request->input('cancel_reason'));

        return back()->with('success', 'Đã huỷ lịch hẹn thành công.');
    }
}
