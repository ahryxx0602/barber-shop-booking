<?php

namespace App\Services\Traits;

use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * H2: Trait chung cho logic thanh toán VNPay + MoMo.
 *
 * Cả PaymentService (Booking) và OrderPaymentService (Order) đều dùng chung
 * cùng gateway logic. Trait này extract phần trùng lặp, mỗi service chỉ cần
 * implement các abstract methods để cung cấp config riêng.
 */
trait PaymentGatewayTrait
{
    // ========================================================================
    // VNPay Common
    // ========================================================================

    /**
     * Tạo URL thanh toán VNPay Sandbox.
     *
     * @param int   $paymentId    ID của payment record
     * @param float $amount       Số tiền thanh toán
     * @param string $orderCode   Mã đơn (booking_code / order_code)
     * @param string $returnRoute Route name cho VNPay return
     * @param string $txnPrefix   Prefix cho TxnRef (vd: '' hoặc 'ORD_')
     */
    protected function buildVNPayUrl(
        int $paymentId,
        float $amount,
        string $orderCode,
        string $returnRoute,
        string $txnPrefix,
        Request $request,
    ): string {
        $vnpUrl        = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnpTmnCode    = config('services.vnpay.tmn_code');
        $vnpHashSecret = config('services.vnpay.hash_secret');
        $vnpReturnUrl  = route($returnRoute);

        $tz = 'Asia/Ho_Chi_Minh';

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnpTmnCode,
            'vnp_Amount'     => (int) ($amount * 100),
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now()->setTimezone($tz)->format('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $request->ip(),
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => 'Thanh toan don hang BarberBook #' . $orderCode,
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $vnpReturnUrl,
            'vnp_TxnRef'     => $txnPrefix . $paymentId . '_' . time(),
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

    /**
     * Verify VNPay signature từ callback data.
     *
     * @return array ['valid' => bool, 'inputData' => array]
     */
    protected function verifyVNPaySignature(array $inputData): array
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

        return [
            'valid'     => hash_equals($secureHash, $vnpSecureHash),
            'inputData' => $inputData,
        ];
    }

    /**
     * Xử lý kết quả VNPay callback: cập nhật payment status.
     *
     * @param mixed  $payment       Payment/OrderPayment model
     * @param array  $inputData     Dữ liệu callback đã verify
     * @param string $relatedKey    Key của related model ('booking' hoặc 'order')
     * @param string $logChannel    Log channel name
     */
    protected function processVNPayResult($payment, array $inputData, string $relatedKey, string $logChannel = 'booking'): array
    {
        // Idempotency
        if ($payment->status === PaymentStatus::Paid) {
            return ['success' => true, 'message' => 'Giao dịch đã được xử lý thành công trước đó.', $relatedKey => $payment->{$relatedKey}];
        }
        if ($payment->status === PaymentStatus::Failed) {
            return ['success' => false, 'message' => 'Giao dịch đã bị từ chối trước đó.', $relatedKey => $payment->{$relatedKey}];
        }

        $responseCode = $inputData['vnp_ResponseCode'] ?? '99';

        if ($responseCode === '00') {
            $payment->update([
                'status'         => PaymentStatus::Paid,
                'transaction_id' => $inputData['vnp_TransactionNo'] ?? null,
                'paid_at'        => now(),
            ]);

            Log::channel($logChannel)->info('VNPay payment success', [
                'payment_id' => $payment->id,
                'amount'     => $payment->amount,
            ]);

            return ['success' => true, 'message' => 'Thanh toán VNPay thành công!', $relatedKey => $payment->{$relatedKey}];
        }

        $payment->update(['status' => PaymentStatus::Failed]);

        return ['success' => false, 'message' => 'Thanh toán VNPay thất bại (mã lỗi: ' . $responseCode . ').', $relatedKey => $payment->{$relatedKey}];
    }

    // ========================================================================
    // Momo Common
    // ========================================================================

    /**
     * Tạo URL thanh toán Momo Sandbox.
     */
    protected function buildMomoUrl(
        int $paymentId,
        float $amount,
        string $orderCode,
        string $returnRoute,
        string $orderIdPrefix,
    ): ?string {
        $partnerCode = config('services.momo.partner_code');
        $accessKey   = config('services.momo.access_key');
        $secretKey   = config('services.momo.secret_key');
        $endpoint    = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $returnUrl   = route($returnRoute);
        // Sandbox dùng chung returnUrl. Production cần tách IPN riêng (POST endpoint)
        $notifyUrl   = route($returnRoute);

        $orderId     = $orderIdPrefix . $paymentId . '_' . time();
        $requestId   = Str::uuid()->toString();
        $orderInfo   = 'Thanh toan BarberBook #' . $orderCode;
        $amountInt   = (int) $amount;
        $requestType = 'payWithMethod';
        $extraData   = base64_encode(json_encode(['paymentId' => $paymentId]));

        $rawSignature = "accessKey={$accessKey}&amount={$amountInt}&extraData={$extraData}"
            . "&ipnUrl={$notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}&redirectUrl={$returnUrl}"
            . "&requestId={$requestId}&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        $body = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'BarberBook',
            'storeId'     => 'BarberBookStore',
            'requestId'   => $requestId,
            'amount'      => $amountInt,
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

    /**
     * Verify Momo signature từ callback data.
     *
     * @return array ['valid' => bool, 'paymentId' => ?int]
     */
    protected function verifyMomoSignature(array $inputData): array
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

        $extraData  = $inputData['extraData'] ?? '';
        $decoded    = json_decode(base64_decode($extraData), true);
        $paymentId  = $decoded['paymentId'] ?? null;

        return [
            'valid'     => hash_equals($expectedSignature, $receivedSignature),
            'paymentId' => $paymentId,
        ];
    }

    /**
     * Xử lý kết quả Momo callback: cập nhật payment status.
     */
    protected function processMomoResult($payment, array $inputData, string $relatedKey, string $logChannel = 'booking'): array
    {
        // Idempotency
        if ($payment->status === PaymentStatus::Paid) {
            return ['success' => true, 'message' => 'Giao dịch đã được xử lý thành công trước đó.', $relatedKey => $payment->{$relatedKey}];
        }
        if ($payment->status === PaymentStatus::Failed) {
            return ['success' => false, 'message' => 'Giao dịch đã bị từ chối trước đó.', $relatedKey => $payment->{$relatedKey}];
        }

        $resultCode = (int) ($inputData['resultCode'] ?? -1);

        if ($resultCode === 0) {
            $payment->update([
                'status'         => PaymentStatus::Paid,
                'transaction_id' => $inputData['transId'] ?? $inputData['orderId'] ?? null,
                'paid_at'        => now(),
            ]);

            Log::channel($logChannel)->info('MoMo payment success', [
                'payment_id' => $payment->id,
                'amount'     => $payment->amount,
            ]);

            return ['success' => true, 'message' => 'Thanh toán MoMo thành công!', $relatedKey => $payment->{$relatedKey}];
        }

        $payment->update(['status' => PaymentStatus::Failed]);

        return ['success' => false, 'message' => 'Thanh toán MoMo thất bại (mã lỗi: ' . $resultCode . ').', $relatedKey => $payment->{$relatedKey}];
    }
}
