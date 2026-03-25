<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\DTOs\CreateOrderData;
use App\Enums\CouponAppliesTo;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\ProductCategory;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\CouponService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected ShippingService $shippingService,
        protected CouponService $couponService,
    ) {}

    /**
     * Trang mã giảm giá — hiển thị coupon active cho client.
     */
    public function coupons()
    {
        $coupons = \App\Models\Coupon::where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        return view('client.shop.coupons', compact('coupons'));
    }

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
     * Trang giỏ hàng — xử lý edge cases: SP hết hàng, bị disable, SL vượt stock.
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $warnings = [];
        $subtotal = 0;
        $cartUpdated = false;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            // Edge case 1: SP đã bị xóa khỏi DB
            if (!$product) {
                $warnings[] = "Một sản phẩm không còn tồn tại và đã được xóa khỏi giỏ.";
                unset($cart[$productId]);
                $cartUpdated = true;
                continue;
            }

            // Edge case 2: SP bị disable (admin tắt bán)
            if (!$product->is_active) {
                $warnings[] = "Sản phẩm \"{$product->name}\" hiện không còn bán và đã được xóa khỏi giỏ.";
                unset($cart[$productId]);
                $cartUpdated = true;
                continue;
            }

            // Edge case 3: SP hết hàng (stock = 0)
            if ($product->stock_quantity <= 0) {
                $warnings[] = "Sản phẩm \"{$product->name}\" đã hết hàng và được xóa khỏi giỏ.";
                unset($cart[$productId]);
                $cartUpdated = true;
                continue;
            }

            // Edge case 4: SL trong giỏ vượt stock hiện tại → tự giảm
            $quantity = $item['quantity'];
            if ($quantity > $product->stock_quantity) {
                $warnings[] = "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock_quantity}, đã điều chỉnh số lượng.";
                $quantity = $product->stock_quantity;
                $cart[$productId]['quantity'] = $quantity;
                $cartUpdated = true;
            }

            $cartItems[] = [
                'product'  => $product,
                'quantity' => $quantity,
                'total'    => $product->price * $quantity,
            ];
            $subtotal += $product->price * $quantity;
        }

        // Cập nhật session nếu có thay đổi
        if ($cartUpdated) {
            session()->put('cart', $cart);
        }

        return view('client.shop.cart', compact('cartItems', 'subtotal', 'warnings'));
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

        // Edge case: SP bị disable
        if (!$product->is_active) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm hiện không còn bán.'], 422);
        }

        // Edge case: SP hết hàng
        if ($product->stock_quantity <= 0) {
            return response()->json(['success' => false, 'message' => "Sản phẩm \"{$product->name}\" đã hết hàng."], 422);
        }

        $quantity = $request->quantity ?? 1;
        $cart = session()->get('cart', []);

        // Nếu đã có trong giỏ → tăng SL
        $currentQty = $cart[$product->id]['quantity'] ?? 0;
        $newQty = $currentQty + $quantity;

        // Edge case: SL vượt stock
        if ($newQty > $product->stock_quantity) {
            $remaining = $product->stock_quantity - $currentQty;
            if ($remaining <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Bạn đã có {$currentQty} sản phẩm \"{$product->name}\" trong giỏ (tối đa {$product->stock_quantity}).",
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => "Chỉ có thể thêm tối đa {$remaining} sản phẩm \"{$product->name}\" nữa.",
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

        // Edge case: SP bị disable hoặc hết hàng → xóa khỏi giỏ
        if (!$product->is_active || $product->stock_quantity <= 0) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
            return response()->json([
                'success' => false,
                'message' => "Sản phẩm \"{$product->name}\" hiện không có sẵn và đã được xóa khỏi giỏ.",
                'removed' => true,
            ], 422);
        }

        // Edge case: SL vượt stock
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
            // Thử geocode bằng Nominatim (OpenStreetMap) — miễn phí
            $fullAddr = "{$address->address}, {$address->ward}, {$address->district}, {$address->city}, Vietnam";
            try {
                $geoRes = \Illuminate\Support\Facades\Http::timeout(5)
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'format' => 'json',
                        'q' => $fullAddr,
                        'limit' => 1,
                        'countrycodes' => 'vn',
                    ]);
                $geoData = $geoRes->json();
                if (!empty($geoData[0]['lat']) && !empty($geoData[0]['lon'])) {
                    $address->update([
                        'latitude'  => (float) $geoData[0]['lat'],
                        'longitude' => (float) $geoData[0]['lon'],
                    ]);
                    $address->refresh();
                }
            } catch (\Exception $e) {
                // Geocode fail → dùng fallback
            }

            // Vẫn không có tọa độ → fallback
            if (!$address->latitude || !$address->longitude) {
                $fallbackFee = $this->shippingService->feeFromDistance(10.0);
                return response()->json([
                    'fee'         => $fallbackFee['fee'],
                    'distance_km' => 10.0,
                    'is_free'     => $fallbackFee['is_free'],
                    'note'        => 'Phí ước tính (chưa xác định tọa độ)',
                ]);
            }
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
            $order = $result['order'];

            // Áp dụng coupon nếu có
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
                } catch (\InvalidArgumentException $e) {
                    // Coupon không hợp lệ → bỏ qua, không block đơn hàng
                }
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
                } catch (\InvalidArgumentException $e) {
                    // Coupon không hợp lệ → bỏ qua
                }
            }

            // Xóa giỏ hàng sau khi đặt thành công
            session()->forget('cart');

            return redirect()->route('client.shop.order-success', $order->id);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * API validate mã giảm giá (AJAX).
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code'       => 'required|string|max:50',
            'applies_to' => 'required|in:product,shipping',
        ]);

        $appliesTo = CouponAppliesTo::from($request->applies_to);

        // Tính subtotal hoặc shipping fee tùy loại
        if ($appliesTo === CouponAppliesTo::Product) {
            $amount = $this->calculateSubtotal();
        } else {
            $amount = (float) $request->input('shipping_fee', 0);
        }

        try {
            $coupon = $this->couponService->validate($request->code, $amount, $appliesTo);
            $discount = $this->couponService->calculateDiscount($coupon, $amount);

            return response()->json([
                'success'  => true,
                'code'     => $coupon->code,
                'type'     => $coupon->type->value,
                'value'    => $coupon->value,
                'discount' => $discount,
                'message'  => 'Áp dụng mã thành công! Giảm ' . number_format($discount, 0, ',', '.') . 'đ',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
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
