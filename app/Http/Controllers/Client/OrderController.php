<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\DTOs\CreateOrderData;
use App\Enums\CouponAppliesTo;
use App\Http\Requests\Client\PlaceOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\CouponService;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected CouponService $couponService,
        protected OrderPaymentService $orderPaymentService,
    ) {}

    /**
     * Đặt hàng.
     */
    public function placeOrder(PlaceOrderRequest $request)
    {
        $cart = session()->get('cart', []);
        
        $items = [];
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active && $item['quantity'] > 0) {
                $items[] = [
                    'product_id' => $productId,
                    'quantity'   => $item['quantity'],
                ];
            }
        }

        if (empty($items)) {
            return back()->with('error', 'Không có sản phẩm hợp lệ trong giỏ hàng.');
        }

        $address = auth()->user()->shippingAddresses()->findOrFail($request->shipping_address_id);

        try {
            $orderData = CreateOrderData::fromArray([
                'customer_id'        => auth()->id(),
                'shipping_address_id' => $address->id,
                'items'              => $items,
                'payment_method'     => $request->payment_method,
                'note'               => $request->note,
            ]);

            $result = $this->orderService->create($orderData);
            $order = $result['order'];

            if ($request->filled('product_coupon_code')) {
                try {
                    $coupon = $this->couponService->validate(
                        $request->product_coupon_code,
                        (float) $order->subtotal,
                        CouponAppliesTo::Product
                    );
                    $discount = $this->couponService->calculateDiscount($coupon, (float) $order->subtotal);
                    $order->update([
                        'product_coupon_code' => $coupon->code,
                        'product_discount' => $discount,
                        'total_price' => max(0, $order->total_price - $discount),
                    ]);
                    $this->couponService->markUsed($coupon);
                } catch (\InvalidArgumentException $e) { }
            }

            if ($request->filled('shipping_coupon_code')) {
                try {
                    $coupon = $this->couponService->validate(
                        $request->shipping_coupon_code,
                        (float) $order->shipping_fee,
                        CouponAppliesTo::Shipping
                    );
                    $discount = $this->couponService->calculateDiscount($coupon, (float) $order->shipping_fee);
                    $order->update([
                        'shipping_coupon_code' => $coupon->code,
                        'shipping_discount' => $discount,
                        'total_price' => max(0, $order->total_price - $discount),
                    ]);
                    $this->couponService->markUsed($coupon);
                } catch (\InvalidArgumentException $e) { }
            }

            session()->forget('cart');

            // Handle Payments
            if ($request->payment_method === 'vnpay') {
                $url = $this->orderPaymentService->createVNPayUrl($order->payment, $request);
                return redirect()->away($url);
            } elseif ($request->payment_method === 'momo') {
                $url = $this->orderPaymentService->createMomoUrl($order->payment);
                if ($url) {
                    return redirect()->away($url);
                }
                return redirect()->route('client.shop.order-success', $order->id)->with('error', 'Lỗi khởi tạo MoMo. Vui lòng thử lại sau.');
            }

            // COD
            return redirect()->route('client.shop.order-success', $order->id);
            
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Trang đặt hàng thành công.
     */
    public function orderSuccess(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'payment', 'shippingAddress']);

        return view('client.shop.order-success', compact('order'));
    }

    /**
     * Danh sách đơn hàng của user.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng.
     */
    public function show(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'payment', 'shippingAddress']);

        return view('client.orders.show', compact('order'));
    }

    /**
     * Hủy đơn hàng (chỉ khi pending).
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        try {
            $this->orderService->cancel($order, $request->cancel_reason ?? 'Khách hàng hủy đơn');
            return back()->with('success', 'Đã hủy đơn hàng thành công.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
