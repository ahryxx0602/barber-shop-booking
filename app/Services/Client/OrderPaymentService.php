<?php

namespace App\Services\Client;

use App\Enums\PaymentStatus;
use App\Models\OrderPayment;
use App\Services\Traits\PaymentGatewayTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Service xử lý thanh toán cho Order.
 * H2: Dùng PaymentGatewayTrait để loại bỏ code trùng lặp với PaymentService.
 */
class OrderPaymentService
{
    use PaymentGatewayTrait;

    // ========================================================================
    // VNPay
    // ========================================================================

    public function createVNPayUrl(OrderPayment $payment, Request $request): string
    {
        return $this->buildVNPayUrl(
            paymentId: $payment->id,
            amount: $payment->amount,
            orderCode: $payment->order->order_code,
            returnRoute: 'client.order-payment.vnpay.return',
            txnPrefix: 'ORD_',
            request: $request,
        );
    }

    public function verifyVNPayCallback(array $inputData): array
    {
        $result = $this->verifyVNPaySignature($inputData);

        if (!$result['valid']) {
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ.', 'order' => null];
        }

        // TxnRef format: ORD_{paymentId}_{timestamp}
        $txnRef = $result['inputData']['vnp_TxnRef'] ?? '';
        $parts = explode('_', $txnRef);
        $paymentId = $parts[1] ?? null;

        $payment = OrderPayment::find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'order' => null];
        }

        return $this->processVNPayResult($payment, $result['inputData'], 'order');
    }

    // ========================================================================
    // Momo
    // ========================================================================

    public function createMomoUrl(OrderPayment $payment): ?string
    {
        return $this->buildMomoUrl(
            paymentId: $payment->id,
            amount: $payment->amount,
            orderCode: $payment->order->order_code,
            returnRoute: 'client.order-payment.momo.return',
            orderIdPrefix: 'ORD_MOMO_',
        );
    }

    public function verifyMomoCallback(array $inputData): array
    {
        $result = $this->verifyMomoSignature($inputData);

        if (!$result['valid']) {
            return ['success' => false, 'message' => 'Chữ ký MoMo không hợp lệ.', 'order' => null];
        }

        $payment = OrderPayment::find($result['paymentId']);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'order' => null];
        }

        return $this->processMomoResult($payment, $inputData, 'order');
    }
}
