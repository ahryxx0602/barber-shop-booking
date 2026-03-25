<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    /**
     * Danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Stats
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', OrderStatus::Pending)->count();
        $shippingOrders = Order::where('status', OrderStatus::Shipping)->count();
        
        // Doanh thu tháng này (Delivered)
        $thisMonthRevenue = Order::where('status', OrderStatus::Delivered)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return view('admin.orders.index', compact('orders', 'totalOrders', 'pendingOrders', 'shippingOrders', 'thisMonthRevenue'));
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'shippingAddress', 'items.product', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        try {
            switch ($request->status) {
                case OrderStatus::Confirmed->value:
                    $this->orderService->confirm($order);
                    $msg = 'Đã xác nhận đơn hàng thành công.';
                    break;
                case OrderStatus::Shipping->value:
                    $this->orderService->ship($order);
                    $msg = 'Đã chuyển đơn hàng sang trạng thái đang giao.';
                    break;
                case OrderStatus::Delivered->value:
                    $this->orderService->deliver($order);
                    $msg = 'Đã hoàn thành đơn hàng.';
                    break;
                case OrderStatus::Cancelled->value:
                    $this->orderService->cancel($order, $request->cancel_reason);
                    $msg = 'Đã hủy đơn hàng thành công.';
                    break;
                default:
                    return back()->with('error', 'Trạng thái không hợp lệ.');
            }

            return back()->with('success', $msg);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
