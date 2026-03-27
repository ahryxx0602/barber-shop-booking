<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\Traits\PaymentGatewayTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingPaymentService
{
    use PaymentGatewayTrait;

    public function createPendingPayment(Booking $booking, PaymentMethod $method): Payment
    {
        return Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount'         => $booking->total_price,
                'method'         => $method,
                'status'         => PaymentStatus::Pending,
                'transaction_id' => null,
                'paid_at'        => null,
            ]
        );
    }

    // ========================================================================
    // VNPay
    // ========================================================================

    public function createVNPayUrl(Payment $payment, Request $request): string
    {
        return $this->buildVNPayUrl(
            paymentId: $payment->id,
            amount: $payment->amount,
            orderCode: $payment->booking->booking_code,
            returnRoute: 'client.payment.vnpay.return',
            txnPrefix: '',
            request: $request,
        );
    }

    public function verifyVNPayCallback(array $inputData): array
    {
        $result = $this->verifyVNPaySignature($inputData);

        if (!$result['valid']) {
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ.', 'booking' => null];
        }

        $txnRef = $result['inputData']['vnp_TxnRef'] ?? '';
        $paymentId = explode('_', $txnRef)[0] ?? null;

        $payment = Payment::find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'booking' => null];
        }

        return $this->processVNPayResult($payment, $result['inputData'], 'booking');
    }

    // ========================================================================
    // Momo
    // ========================================================================

    public function createMomoUrl(Payment $payment): ?string
    {
        return $this->buildMomoUrl(
            paymentId: $payment->id,
            amount: $payment->amount,
            orderCode: $payment->booking->booking_code,
            returnRoute: 'client.payment.momo.return',
            orderIdPrefix: 'BARBER_',
        );
    }

    public function verifyMomoCallback(array $inputData): array
    {
        $result = $this->verifyMomoSignature($inputData);

        if (!$result['valid']) {
            Log::warning('MoMo callback signature mismatch', ['data' => $inputData]);
            return ['success' => false, 'message' => 'Chữ ký MoMo không hợp lệ.', 'booking' => null];
        }

        $payment = Payment::find($result['paymentId']);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'booking' => null];
        }

        return $this->processMomoResult($payment, $inputData, 'booking');
    }
}
