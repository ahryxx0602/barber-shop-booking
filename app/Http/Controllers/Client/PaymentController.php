<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {
    }

    /**
     * Hiển thị trang chọn phương thức thanh toán.
     */
    public function show(Booking $booking): View
    {
        // C3: Kiểm tra quyền sở hữu booking
        if (auth()->check() && $booking->customer_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem thanh toán này.');
        }

        // Chặn truy cập nếu đã thanh toán
        if ($booking->payment && $booking->payment->status === PaymentStatus::Paid) {
            return view('client.booking.confirmation', [
                'booking' => $booking->load(['barber.user', 'services', 'timeSlot', 'payment']),
            ]);
        }

        $booking->load(['barber.user', 'services', 'timeSlot']);

        return view('client.payment.show', compact('booking'));
    }

    /**
     * Xử lý chọn phương thức thanh toán:
     * - Tiền mặt → redirect thẳng sang trang confirmation
     * - VNPay → redirect sang cổng sandbox VNPay
     * - Momo → gọi API Momo lấy payUrl rồi redirect
     */
    public function process(Request $request, Booking $booking): RedirectResponse
    {
        // C3: Kiểm tra quyền sở hữu booking
        if (auth()->check() && $booking->customer_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền thanh toán booking này.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:cash,vnpay,momo'],
        ]);

        $method = PaymentMethod::from($request->payment_method);

        // Chặn thanh toán lại nếu đã hoàn tất
        if ($booking->payment && $booking->payment->status === PaymentStatus::Paid) {
            return redirect()
                ->route('client.booking.confirmation', $booking)
                ->with('error', 'Booking này đã được thanh toán thành công!');
        }

        // Tạo bản ghi Payment (hoặc cập nhật phục hồi trạng thái Pending nếu đã tồn tại)
        $payment = $this->paymentService->createPendingPayment($booking, $method);

        // Phân luồng theo phương thức
        return match ($method) {
            PaymentMethod::Cash => $this->handleCash($booking),
            PaymentMethod::VNPay => $this->handleVNPay($payment, $request),
            PaymentMethod::Momo => $this->handleMomo($payment),
        };
    }

    /**
     * Tiền mặt: đánh dấu pending, chuyển sang confirmation.
     */
    private function handleCash(Booking $booking): RedirectResponse
    {
        return redirect()
            ->route('client.booking.confirmation', $booking)
            ->with('success', 'Đặt lịch thành công! Bạn sẽ thanh toán tại quán.');
    }

    /**
     * VNPay: tạo URL sandbox rồi redirect.
     */
    private function handleVNPay(Payment $payment, Request $request): RedirectResponse
    {
        $url = $this->paymentService->createVNPayUrl($payment, $request);
        return redirect()->away($url);
    }

    /**
     * Momo: gọi API lấy payUrl rồi redirect.
     */
    private function handleMomo(Payment $payment): RedirectResponse
    {
        $url = $this->paymentService->createMomoUrl($payment);

        if (!$url) {
            return redirect()
                ->route('client.payment.show', $payment->booking)
                ->with('error', 'Không thể kết nối đến cổng MoMo. Vui lòng thử lại.');
        }

        return redirect()->away($url);
    }

    /**
     * VNPay redirect về đây sau khi thanh toán xong.
     */
    public function vnpayReturn(Request $request): View|RedirectResponse
    {
        $result = $this->paymentService->verifyVNPayCallback($request->all());

        if ($result['success'] && $result['booking']) {
            $booking = $result['booking']->load(['barber.user', 'services', 'timeSlot', 'payment']);
            return view('client.booking.confirmation', compact('booking'))
                ->with('success', $result['message']);
        }

        // Thất bại: quay về trang payment
        if ($result['booking']) {
            return redirect()
                ->route('client.payment.show', $result['booking'])
                ->with('error', $result['message']);
        }

        return redirect()->route('home')->with('error', $result['message']);
    }

    /**
     * VNPay IPN — Server-to-server callback.
     * VNPay gọi POST đến endpoint này để thông báo kết quả giao dịch.
     * Đảm bảo payment luôn được cập nhật ngay cả khi user đóng browser.
     */
    public function vnpayIPN(Request $request)
    {
        $result = $this->paymentService->verifyVNPayCallback($request->all());

        // VNPay yêu cầu trả về JSON với RspCode
        if ($result['success']) {
            return response()->json([
                'RspCode' => '00',
                'Message' => 'Confirm Success',
            ]);
        }

        return response()->json([
            'RspCode' => '99',
            'Message' => $result['message'],
        ]);
    }

    /**
     * Momo redirect về đây sau khi thanh toán xong.
     */
    public function momoReturn(Request $request): View|RedirectResponse
    {
        $result = $this->paymentService->verifyMomoCallback($request->all());

        if ($result['success'] && $result['booking']) {
            $booking = $result['booking']->load(['barber.user', 'services', 'timeSlot', 'payment']);
            return view('client.booking.confirmation', compact('booking'))
                ->with('success', $result['message']);
        }

        if ($result['booking']) {
            return redirect()
                ->route('client.payment.show', $result['booking'])
                ->with('error', $result['message']);
        }

        return redirect()->route('home')->with('error', $result['message']);
    }
}
