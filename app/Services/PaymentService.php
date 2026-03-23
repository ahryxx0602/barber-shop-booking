<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Tạo bản ghi Payment mới với status Pending.
     */
    public function createPendingPayment(Booking $booking, PaymentMethod $method): Payment
    {
        return Payment::create([
            'booking_id'     => $booking->id,
            'amount'         => $booking->total_price,
            'method'         => $method,
            'status'         => PaymentStatus::Pending,
            'transaction_id' => null,
            'paid_at'        => null,
        ]);
    }

    // ========================================================================
    // VNPay Sandbox
    // ========================================================================

    /**
     * Tạo URL thanh toán VNPay Sandbox.
     * Tham khảo tài liệu: https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
     */
    public function createVNPayUrl(Payment $payment, Request $request): string
    {
        $vnpUrl      = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnpTmnCode  = config('services.vnpay.tmn_code');
        $vnpHashSecret = config('services.vnpay.hash_secret');
        $vnpReturnUrl  = route('client.payment.vnpay.return');

        // VNPay bắt buộc múi giờ GMT+7 (Asia/Ho_Chi_Minh)
        $tz = 'Asia/Ho_Chi_Minh';
        
        // Dữ liệu gửi sang VNPay
        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnpTmnCode,
            'vnp_Amount'     => (int) ($payment->amount * 100), // VNPay yêu cầu nhân 100
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => now()->setTimezone($tz)->format('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $request->ip(),
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => 'Thanh toan don hang BarberBook #' . $payment->booking->booking_code,
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $vnpReturnUrl,
            'vnp_TxnRef'     => $payment->id . '_' . time(), // Mã duy nhất mỗi giao dịch
            'vnp_ExpireDate' => now()->setTimezone($tz)->addMinutes(15)->format('YmdHis'),
        ];

        // Sắp xếp theo thứ tự alphabet key
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

        // Tạo chữ ký HMAC SHA512 theo yêu cầu VNPay
        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);

        return $vnpUrl . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;
    }

    /**
     * Xác thực callback từ VNPay, cập nhật trạng thái Payment.
     * @return array ['success' => bool, 'message' => string, 'booking' => ?Booking]
     */
    public function verifyVNPayCallback(array $inputData): array
    {
        $vnpHashSecret = config('services.vnpay.hash_secret');

        // Lấy chữ ký VNPay gửi về
        $vnpSecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        // Sắp xếp lại và sinh chữ ký để so sánh
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
            return ['success' => false, 'message' => 'Chữ ký không hợp lệ.', 'booking' => null];
        }

        // Tìm Payment từ vnp_TxnRef (format: paymentId_timestamp)
        $txnRef = $inputData['vnp_TxnRef'] ?? '';
        $paymentId = explode('_', $txnRef)[0] ?? null;

        $payment = Payment::find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'booking' => null];
        }

        $responseCode = $inputData['vnp_ResponseCode'] ?? '99';

        if ($responseCode === '00') {
            // Giao dịch thành công
            $payment->update([
                'status'         => PaymentStatus::Paid,
                'transaction_id' => $inputData['vnp_TransactionNo'] ?? null,
                'paid_at'        => now(),
            ]);
            return ['success' => true, 'message' => 'Thanh toán VNPay thành công!', 'booking' => $payment->booking];
        }

        return ['success' => false, 'message' => 'Thanh toán VNPay thất bại (mã lỗi: ' . $responseCode . ').', 'booking' => $payment->booking];
    }

    // ========================================================================
    // Momo Sandbox
    // ========================================================================

    /**
     * Tạo URL thanh toán Momo Sandbox.
     * Tham khảo tài liệu: https://developers.momo.vn/v3/docs/payment/api/wallet/onetime
     */
    public function createMomoUrl(Payment $payment): ?string
    {
        $partnerCode = config('services.momo.partner_code');
        $accessKey   = config('services.momo.access_key');
        $secretKey   = config('services.momo.secret_key');
        $endpoint    = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $returnUrl   = route('client.payment.momo.return');
        $notifyUrl   = route('client.payment.momo.return'); // IPN URL (trong sandbox dùng chung)

        $orderId    = 'BARBER_' . $payment->id . '_' . time();
        $requestId  = Str::uuid()->toString();
        $orderInfo  = 'Thanh toan BarberBook #' . $payment->booking->booking_code;
        $amount     = (int) $payment->amount;
        $requestType = 'payWithMethod';
        $extraData   = base64_encode(json_encode(['paymentId' => $payment->id]));

        // Tạo chữ ký HMAC SHA256 theo quy chuẩn Momo
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

        // Gọi API Momo
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        // Trả về URL thanh toán Momo (payUrl)
        return $result['payUrl'] ?? null;
    }

    /**
     * Xác thực callback từ Momo.
     * @return array ['success' => bool, 'message' => string, 'booking' => ?Booking]
     */
    public function verifyMomoCallback(array $inputData): array
    {
        $secretKey = config('services.momo.secret_key');
        $accessKey = config('services.momo.access_key');

        // Lấy extraData để tìm paymentId
        $extraData  = $inputData['extraData'] ?? '';
        $decoded    = json_decode(base64_decode($extraData), true);
        $paymentId  = $decoded['paymentId'] ?? null;

        $payment = Payment::find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Không tìm thấy giao dịch.', 'booking' => null];
        }

        $resultCode = (int) ($inputData['resultCode'] ?? -1);

        if ($resultCode === 0) {
            // Giao dịch thành công
            $payment->update([
                'status'         => PaymentStatus::Paid,
                'transaction_id' => $inputData['transId'] ?? $inputData['orderId'] ?? null,
                'paid_at'        => now(),
            ]);
            return ['success' => true, 'message' => 'Thanh toán MoMo thành công!', 'booking' => $payment->booking];
        }

        return ['success' => false, 'message' => 'Thanh toán MoMo thất bại (mã lỗi: ' . $resultCode . ').', 'booking' => $payment->booking];
    }
}
