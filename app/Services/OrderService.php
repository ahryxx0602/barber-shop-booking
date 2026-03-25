<?php

namespace App\Services;

use App\DTOs\CreateOrderData;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OrderStatusUpdatedMail;

class OrderService
{
    public function __construct(
        protected ShippingService $shippingService,
        protected ProductService $productService,
    ) {}

    /**
     * Tạo đơn hàng mới trong transaction.
     * 1. Validate stock
     * 2. Tính subtotal, tax, shipping fee
     * 3. Tạo Order + OrderItems + OrderPayment
     * 4. Giảm stock
     *
     * @return array{order: Order, redirect_url: ?string}
     */
    public function create(CreateOrderData $data): array
    {
        return DB::transaction(function () use ($data) {
            // 1. Load shipping address để lấy tọa độ
            $shippingAddress = ShippingAddress::findOrFail($data->shipping_address_id);

            // 2. Load & validate stock cho mỗi item, tính subtotal
            $subtotal = 0;
            $itemsData = [];

            foreach ($data->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \InvalidArgumentException(
                        "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity} trong kho."
                    );
                }

                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $item['quantity'];
                $subtotal += $totalPrice;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            // 3. Tính thuế VAT 10%
            $taxRate = 10.00;
            $taxAmount = round($subtotal * $taxRate / 100, 2);

            // 4. Tính phí vận chuyển (nếu có tọa độ)
            $shippingFee = 0;
            $shippingDistanceKm = null;

            if ($shippingAddress->latitude && $shippingAddress->longitude) {
                $shippingResult = $this->shippingService->calculateFee(
                    (float) $shippingAddress->latitude,
                    (float) $shippingAddress->longitude,
                    $subtotal
                );
                $shippingFee = $shippingResult['fee'];
                $shippingDistanceKm = $shippingResult['distance_km'];
            }

            // 5. Tính tổng
            $totalAmount = $subtotal + $taxAmount + $shippingFee;

            // 6. Tạo Order
            $order = Order::create([
                'order_code'           => $this->generateCode(),
                'customer_id'          => $data->customer_id,
                'shipping_address_id'  => $data->shipping_address_id,
                'subtotal'             => $subtotal,
                'tax_rate'             => $taxRate,
                'tax_amount'           => $taxAmount,
                'shipping_fee'         => $shippingFee,
                'shipping_distance_km' => $shippingDistanceKm,
                'total_amount'         => $totalAmount,
                'status'               => OrderStatus::Pending,
                'note'                 => $data->note,
            ]);

            // 7. Tạo OrderItems
            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // 8. Giảm stock từng sản phẩm
            foreach ($data->items as $item) {
                $this->productService->decreaseStock($item['product_id'], $item['quantity']);
            }

            // 9. Tạo OrderPayment (pending)
            OrderPayment::create([
                'order_id' => $order->id,
                'amount'   => $totalAmount,
                'method'   => $data->payment_method,
                'status'   => PaymentStatus::Pending,
            ]);

            Log::channel('booking')->info('Order created', [
                'order_code'  => $order->order_code,
                'customer_id' => $data->customer_id,
                'total'       => $totalAmount,
                'method'      => $data->payment_method->value,
            ]);

            Mail::to($order->customer->email)->queue(new OrderStatusUpdatedMail($order));

            // 10. Nếu COD → trả order, nếu online → trả redirect URL (xử lý ở Phase 6)
            $redirectUrl = null;
            if ($data->payment_method !== OrderPaymentMethod::Cod) {
                // Redirect URL sẽ được xử lý bởi OrderPaymentService ở Phase 6
                $redirectUrl = null;
            }

            return [
                'order'        => $order->load(['items.product', 'payment', 'shippingAddress']),
                'redirect_url' => $redirectUrl,
            ];
        });
    }

    /**
     * Admin xác nhận đơn hàng: Pending → Confirmed.
     */
    public function confirm(Order $order): Order
    {
        if (!$order->status->canTransitionTo(OrderStatus::Confirmed)) {
            throw new \InvalidArgumentException(
                'Không thể xác nhận đơn hàng ở trạng thái: ' . $order->status->label()
            );
        }

        $order->update(['status' => OrderStatus::Confirmed]);

        Log::channel('booking')->info('Order confirmed', [
            'order_code' => $order->order_code,
        ]);

        Mail::to($order->customer->email)->queue(new OrderStatusUpdatedMail($order));

        return $order;
    }

    /**
     * Chuyển đơn sang trạng thái giao hàng: Confirmed → Shipping.
     */
    public function ship(Order $order): Order
    {
        if (!$order->status->canTransitionTo(OrderStatus::Shipping)) {
            throw new \InvalidArgumentException(
                'Không thể giao hàng đơn ở trạng thái: ' . $order->status->label()
            );
        }

        $order->update(['status' => OrderStatus::Shipping]);

        Log::channel('booking')->info('Order shipping', [
            'order_code' => $order->order_code,
        ]);

        Mail::to($order->customer->email)->queue(new OrderStatusUpdatedMail($order));

        return $order;
    }

    /**
     * Hoàn thành giao hàng: Shipping → Delivered.
     * Nếu COD → tự động đánh dấu payment là Paid.
     */
    public function deliver(Order $order): Order
    {
        if (!$order->status->canTransitionTo(OrderStatus::Delivered)) {
            throw new \InvalidArgumentException(
                'Không thể hoàn thành giao hàng đơn ở trạng thái: ' . $order->status->label()
            );
        }

        $order->update(['status' => OrderStatus::Delivered]);

        // Nếu COD → auto mark payment as paid
        $payment = $order->payment;
        if ($payment && $payment->method === OrderPaymentMethod::Cod && $payment->status === PaymentStatus::Pending) {
            $payment->update([
                'status'  => PaymentStatus::Paid,
                'paid_at' => now(),
            ]);
        }

        Log::channel('booking')->info('Order delivered', [
            'order_code' => $order->order_code,
        ]);

        Mail::to($order->customer->email)->queue(new OrderStatusUpdatedMail($order));

        return $order;
    }

    /**
     * Hủy đơn hàng: hoàn stock, nếu đã thanh toán online → cần refund (flag).
     */
    public function cancel(Order $order, ?string $reason = null): Order
    {
        if (!$order->status->canTransitionTo(OrderStatus::Cancelled)) {
            throw new \InvalidArgumentException(
                'Không thể hủy đơn hàng ở trạng thái: ' . $order->status->label()
            );
        }

        return DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status'        => OrderStatus::Cancelled,
                'cancelled_at'  => now(),
                'cancel_reason' => $reason ?? 'Đơn hàng bị hủy',
            ]);

            // Hoàn stock từng sản phẩm
            $order->loadMissing('items');
            foreach ($order->items as $item) {
                $this->productService->increaseStock($item->product_id, $item->quantity);
            }

            // Nếu đã thanh toán online → đánh dấu cần hoàn tiền
            $payment = $order->payment;
            if ($payment && $payment->status === PaymentStatus::Paid && $payment->method !== OrderPaymentMethod::Cod) {
                // TODO: Implement refund logic khi có API hoàn tiền
                Log::channel('booking')->warning('Order cancelled — cần hoàn tiền online', [
                    'order_code'     => $order->order_code,
                    'payment_method' => $payment->method->value,
                    'amount'         => $payment->amount,
                ]);
            }

            Log::channel('booking')->info('Order cancelled', [
                'order_code' => $order->order_code,
                'reason'     => $reason,
            ]);

            Mail::to($order->customer->email)->queue(new OrderStatusUpdatedMail($order));

            return $order;
        });
    }

    /**
     * Tạo mã đơn hàng unique: ORD-YYYYMMDD-XXXX.
     */
    protected function generateCode(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
