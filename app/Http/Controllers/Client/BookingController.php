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
        $barbers = Barber::with('user', 'branch')->where('is_active', true)->get();
        $branches = \App\Models\Branch::where('is_active', true)->orderBy('name')->get();

        return view('client.booking.create', compact('services', 'barbers', 'branches'));
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
                // Chỉ hiển thị slot cách ít nhất 1 tiếng so với giờ hiện tại
                $minTime = now()->addHour()->format('H:i:s');
                $query->where('start_time', '>=', $minTime);
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

    public function applyCoupon(Request $request, \App\Services\CouponService $couponService): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'total_price' => 'required|numeric|min:0',
        ]);

        try {
            $coupon = $couponService->validate($request->coupon_code, $request->total_price);
            $discountAmount = $couponService->calculateDiscount($coupon, $request->total_price);

            return response()->json([
                'valid' => true,
                'discount_amount' => $discountAmount,
                'message' => 'Áp dụng mã thành công!',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
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
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['coupon_code' => $e->getMessage()])->withInput();
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
