<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\DTOs\CreateOrderData;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\ProductCategory;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected ShippingService $shippingService,
    ) {}

    /**
     * Trang cửa hàng — danh sách sản phẩm active.
     */
    public function index(Request $request)
    {
        $query = Product::active();

        // Filter theo category
        if ($request->filled('category')) {
            $category = ProductCategory::tryFrom($request->category);
            if ($category) {
                $query->byCategory($category);
            }
        }

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = ProductCategory::cases();

        return view('client.shop.index', compact('products', 'categories'));
    }

    /**
     * Chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        // Chỉ hiện SP active
        if (!$product->is_active) {
            abort(404);
        }

        // Sản phẩm liên quan cùng category (trừ SP hiện tại)
        $relatedProducts = Product::active()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('client.shop.show', compact('product', 'relatedProducts'));
    }

    /**
     * Trang giỏ hàng.
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active) {
                // Điều chỉnh SL nếu vượt stock
                $quantity = min($item['quantity'], $product->stock_quantity);
                if ($quantity <= 0) continue;

                $cartItems[] = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'total'    => $product->price * $quantity,
                ];
                $subtotal += $product->price * $quantity;
            }
        }

        return view('client.shop.cart', compact('cartItems', 'subtotal'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (AJAX).
     */
    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'    => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không còn bán.'], 422);
        }

        $quantity = $request->quantity ?? 1;
        $cart = session()->get('cart', []);

        // Nếu đã có trong giỏ → tăng SL
        $currentQty = $cart[$product->id]['quantity'] ?? 0;
        $newQty = $currentQty + $quantity;

        // Kiểm tra stock
        if ($newQty > $product->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity} trong kho.",
            ], 422);
        }

        $cart[$product->id] = [
            'quantity' => $newQty,
        ];

        session()->put('cart', $cart);

        return response()->json([
            'success'    => true,
            'message'    => "Đã thêm \"{$product->name}\" vào giỏ hàng.",
            'cart_count' => $this->getCartCount(),
        ]);
    }

    /**
     * Cập nhật số lượng trong giỏ (AJAX).
     */
    public function updateCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'    => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        if (!isset($cart[$product->id])) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không có trong giỏ.'], 422);
        }

        // Kiểm tra stock
        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity} trong kho.",
            ], 422);
        }

        $cart[$product->id]['quantity'] = $request->quantity;
        session()->put('cart', $cart);

        $itemTotal = $product->price * $request->quantity;

        return response()->json([
            'success'    => true,
            'item_total' => $itemTotal,
            'subtotal'   => $this->calculateSubtotal(),
            'cart_count' => $this->getCartCount(),
        ]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ (AJAX).
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);

        return response()->json([
            'success'    => true,
            'message'    => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'subtotal'   => $this->calculateSubtotal(),
            'cart_count' => $this->getCartCount(),
        ]);
    }

    /**
     * Trang checkout (cần đăng nhập).
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.shop.index')
                ->with('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        // Load cart items
        $cartItems = [];
        $subtotal = 0;
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active) {
                $quantity = min($item['quantity'], $product->stock_quantity);
                if ($quantity <= 0) continue;
                $cartItems[] = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'total'    => $product->price * $quantity,
                ];
                $subtotal += $product->price * $quantity;
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('client.shop.index')
                ->with('error', 'Không có sản phẩm hợp lệ trong giỏ hàng.');
        }

        $taxAmount = round($subtotal * 0.10, 2);
        $addresses = auth()->user()->shippingAddresses()->orderByDesc('is_default')->get();
        $paymentMethods = OrderPaymentMethod::cases();

        return view('client.shop.checkout', compact(
            'cartItems', 'subtotal', 'taxAmount', 'addresses', 'paymentMethods'
        ));
    }

    /**
     * AJAX: Tính phí vận chuyển theo địa chỉ.
     */
    public function getShippingFee(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address_id' => 'required|integer|exists:shipping_addresses,id',
        ]);

        $address = auth()->user()->shippingAddresses()->findOrFail($request->shipping_address_id);

        if (!$address->latitude || !$address->longitude) {
            // Fallback phí mặc định khi không có tọa độ
            $fallbackFee = $this->shippingService->feeFromDistance(10.0);
            return response()->json([
                'fee'         => $fallbackFee['fee'],
                'distance_km' => 10.0,
                'is_free'     => false,
                'note'        => 'Phí ước tính (chưa có tọa độ)',
            ]);
        }

        $subtotal = $this->calculateSubtotal();
        $result = $this->shippingService->calculateFee(
            (float) $address->latitude,
            (float) $address->longitude,
            $subtotal
        );

        return response()->json($result);
    }

    /**
     * Đặt hàng.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|integer|exists:shipping_addresses,id',
            'payment_method'      => 'required|in:cod,vnpay,momo',
            'note'                => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Giỏ hàng trống.');
        }

        // Xây dựng items array từ session cart
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

        // Kiểm tra address thuộc user
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

            // Xóa giỏ hàng sau khi đặt thành công
            session()->forget('cart');

            // Phase 6 sẽ xử lý redirect cho VNPay/MoMo
            // Hiện tại tất cả → trang thành công
            return redirect()->route('client.shop.order-success', $result['order']->id);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Trang đặt hàng thành công.
     */
    public function orderSuccess(Order $order)
    {
        // Chỉ cho phép xem đơn của mình
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'payment', 'shippingAddress']);

        return view('client.shop.order-success', compact('order'));
    }

    /**
     * Danh sách đơn hàng của user.
     */
    public function orders()
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
    public function orderShow(Order $order)
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
    public function cancelOrder(Request $request, Order $order)
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

    /**
     * Đếm tổng item trong giỏ.
     */
    protected function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Tính subtotal từ session cart.
     */
    protected function calculateSubtotal(): float
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active) {
                $subtotal += $product->price * min($item['quantity'], $product->stock_quantity);
            }
        }

        return $subtotal;
    }
}
