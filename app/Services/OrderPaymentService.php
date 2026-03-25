<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderPaymentService
{
    // ========================================================================
    // VNPay Sandbox
    // ========================================================================

    public function createVNPayUrl(OrderPayment $payment, Request $request): string
    {
        $vnpUrl      = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnpTmnCode  = config('services.vnpay.tmn_code');
        $vnpHashSecret = config('services.vnpay.hash_secret');
        $vnpReturnUrl  = route('client.order-payment.vnpay.return');

        $tz = 'Asia/Ho_Chi_Minh';
        
        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnpTmnCode,
            'vnp_Amount'     => (int) ($payment->amount * 100),
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now()->setTimezone($tz)->format('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $request->ip(),
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => 'Thanh toan don hang BarberBook #' . $payment->order->order_code,
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $vnpReturnUrl,
            'vnp_TxnRef'     => 'ORD_' . $payment->id . '_' . time(),
            'vnp_ExpireDate' => now()->setTimezone($tz)->addMinutes(15)->format('YmdHis'),
        ];

        ksort($inputData);
        $hashData = '';
        $query    = '';
        $i        = 0;
        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
                $query    .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $query    .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);
        return $vnpUrl . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;
    }

    public function verifyVNPayCallback(array $inputData): array
    {
        $vnpHashSecret = config('services.vnpay.hash_secret');

        $vnpSecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);

        if ($secureHash !== $vnpSecureHash) {
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ.', 'order' => null];
        }

        $txnRef = $inputData['vnp_TxnRef'] ?? '';
        $parts = explode('_', $txnRef);
        $paymentId = $parts[1] ?? null;

        $payment = OrderPayment::find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'order' => null];
        }

        if ($payment->status === PaymentStatus::Paid) {
            return ['success' => true, 'message' => 'Giao dịch đã được xử lý thành công trước đó.', 'order' => $payment->order];
        }
        if ($payment->status === PaymentStatus::Failed) {
            return ['success' => false, 'message' => 'Giao dịch đã bị từ chối trước đó.', 'order' => $payment->order];
        }

        $responseCode = $inputData['vnp_ResponseCode'] ?? '99';

        if ($responseCode === '00') {
            $payment->update([
                'status'         => PaymentStatus::Paid,
                'transaction_id' => $inputData['vnp_TransactionNo'] ?? null,
                'paid_at'        => now(),
            ]);
            // If payment successful, we might want to update Order status if needed, 
            // but for now, we just return success like Booking
            // In OrderService, we might need to handle Payment successful state later, but wait, OrderStatus is separate.
            return ['success' => true, 'message' => 'Thanh toán VNPay thành công!', 'order' => $payment->order];
        }

        $payment->update(['status' => PaymentStatus::Failed]);
        return ['success' => false, 'message' => 'Thanh toán VNPay thất bại (mã lỗi: ' . $responseCode . ').', 'order' => $payment->order];
    }

    // ========================================================================
    // Momo Sandbox
    // ========================================================================

    public function createMomoUrl(OrderPayment $payment): ?string
    {
        $partnerCode = config('services.momo.partner_code');
        $accessKey   = config('services.momo.access_key');
        $secretKey   = config('services.momo.secret_key');
        $endpoint    = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $returnUrl   = route('client.order-payment.momo.return');
        $notifyUrl   = route('client.order-payment.momo.return'); 

        $orderId    = 'ORD_MOMO_' . $payment->id . '_' . time();
        $requestId  = Str::uuid()->toString();
        $orderInfo  = 'Thanh toan BarberBook #' . $payment->order->order_code;
        $amount     = (int) $payment->amount;
        $requestType = 'payWithMethod';
        $extraData   = base64_encode(json_encode(['paymentId' => $payment->id]));

        $rawSignature = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}"
            . "&ipnUrl={$notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}&redirectUrl={$returnUrl}"
            . "&requestId={$requestId}&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        $body = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'BarberBook',
            'storeId'     => 'BarberBookStore',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        try {
            $response = Http::timeout(10)->post($endpoint, $body);
            return $response->json()['payUrl'] ?? null;
        } catch (\Exception $e) {
            Log::error('MoMo API error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function verifyMomoCallback(array $inputData): array
    {
        $secretKey = config('services.momo.secret_key');
        $accessKey = config('services.momo.access_key');

        $receivedSignature = $inputData['signature'] ?? '';
        $rawSignature = "accessKey={$accessKey}"
            . "&amount=" . ($inputData['amount'] ?? '')
            . "&extraData=" . ($inputData['extraData'] ?? '')
            . "&message=" . ($inputData['message'] ?? '')
            . "&orderId=" . ($inputData['orderId'] ?? '')
            . "&orderInfo=" . ($inputData['orderInfo'] ?? '')
            . "&orderType=" . ($inputData['orderType'] ?? '')
            . "&partnerCode=" . ($inputData['partnerCode'] ?? '')
            . "&payType=" . ($inputData['payType'] ?? '')
            . "&requestId=" . ($inputData['requestId'] ?? '')
            . "&responseTime=" . ($inputData['responseTime'] ?? '')
            . "&resultCode=" . ($inputData['resultCode'] ?? '')
            . "&transId=" . ($inputData['transId'] ?? '');

        $expectedSignature = hash_hmac('sha256', $rawSignature, $secretKey);

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            return ['success' => false, 'message' => 'Chữ ký MoMo không hợp lệ.', 'order' => null];
        }

        $extraData  = $inputData['extraData'] ?? '';
        $decoded    = json_decode(base64_decode($extraData), true);
        $paymentId  = $decoded['paymentId'] ?? null;

        $payment = OrderPayment::find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'order' => null];
        }

        if ($payment->status === PaymentStatus::Paid) {
            return ['success' => true, 'message' => 'Giao dịch đã được xử lý thành công trước đó.', 'order' => $payment->order];
        }
        if ($payment->status === PaymentStatus::Failed) {
            return ['success' => false, 'message' => 'Giao dịch đã bị từ chối trước đó.', 'order' => $payment->order];
        }

        $resultCode = (int) ($inputData['resultCode'] ?? -1);

        if ($resultCode === 0) {
            $payment->update([
                'status'         => PaymentStatus::Paid,
                'transaction_id' => $inputData['transId'] ?? $inputData['orderId'] ?? null,
                'paid_at'        => now(),
            ]);
            return ['success' => true, 'message' => 'Thanh toán MoMo thành công!', 'order' => $payment->order];
        }

        $payment->update(['status' => PaymentStatus::Failed]);
        return ['success' => false, 'message' => 'Thanh toán MoMo thất bại (mã lỗi: ' . $resultCode . ').', 'order' => $payment->order];
    }
}
