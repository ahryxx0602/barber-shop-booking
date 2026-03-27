<?php

namespace App\Services\Shop;

use App\DTOs\Client\CreateOrderData;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Repositories\Contracts\Client\OrderRepositoryInterface;
use App\Services\ProductService;
use App\Services\ShippingService;
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
        protected OrderRepositoryInterface $orderRepo,
    ) {}

    /**
     * @return array{order: Order, redirect_url: ?string}
     */
    public function create(CreateOrderData $data): array
    {
        return DB::transaction(function () use ($data) {
            $shippingAddress = ShippingAddress::findOrFail($data->shipping_address_id);

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

            $taxRate = 10.00;
            $taxAmount = round($subtotal * $taxRate / 100, 2);

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

            $totalAmount = $subtotal + $taxAmount + $shippingFee;

            $order = $this->orderRepo->create([
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

            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            foreach ($data->items as $item) {
                $this->productService->decreaseStock($item['product_id'], $item['quantity']);
            }

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

            $redirectUrl = null;
            if ($data->payment_method !== OrderPaymentMethod::Cod) {
                $redirectUrl = null;
            }

            return [
                'order'        => $order->load(['items.product', 'payment', 'shippingAddress']),
                'redirect_url' => $redirectUrl,
            ];
        });
    }

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

    public function deliver(Order $order): Order
    {
        if (!$order->status->canTransitionTo(OrderStatus::Delivered)) {
            throw new \InvalidArgumentException(
                'Không thể hoàn thành giao hàng đơn ở trạng thái: ' . $order->status->label()
            );
        }

        $order->update(['status' => OrderStatus::Delivered]);

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

            $order->loadMissing('items');
            foreach ($order->items as $item) {
                $this->productService->increaseStock($item->product_id, $item->quantity);
            }

            $payment = $order->payment;
            if ($payment && $payment->status === PaymentStatus::Paid && $payment->method !== OrderPaymentMethod::Cod) {
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

    protected function generateCode(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
