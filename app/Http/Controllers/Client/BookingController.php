<?php

namespace App\Http\Controllers\Client;

use App\DTOs\Barber\CreateBookingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreBookingRequest;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use App\Repositories\Contracts\Barber\BookingRepositoryInterface;
use App\Services\BarberService;
use App\Services\Booking\BookingService;
use App\Services\BranchService;
use App\Services\CouponService;
use App\Services\ServiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Client\ApplyCouponRequest;
use App\Http\Requests\Client\GetSlotsRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepositoryInterface $bookingRepo,
        protected ServiceService $serviceService,
        protected BarberService $barberService,
        protected BranchService $brancheService,
        protected CouponService $couponService
    ) {
    }

    public function create(): View
    {
        $services = $this->serviceService->getActiveServices();
        $barbers = $this->barberService->getAllBarbers();
        $branches = $this->brancheService->getActiveBranches();

        return view('client.booking.create', compact('services', 'barbers', 'branches'));
    }

    public function getSlots(GetSlotsRequest $request): JsonResponse
    {
        $isToday = $request->date === now()->toDateString();

        $slots = $this->bookingRepo->getAvailableSlots(
            $request->barber_id,
            $request->date,
            $isToday
        );

        return response()->json($slots);
    }

    public function applyCoupon(ApplyCouponRequest $request): JsonResponse
    {
        try {
            $coupon = $this->couponService->validate($request->coupon_code, $request->total_price);
            $discountAmount = $this->couponService->calculateDiscount($coupon, $request->total_price);

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
