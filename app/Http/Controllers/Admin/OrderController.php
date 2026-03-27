<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\Client\OrderRepositoryInterface;
use App\Services\Shop\OrderService;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected OrderRepositoryInterface $orderRepo,
    ) {}

    /**
     * Danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $orders = $this->orderRepo->paginateWithFilters([
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ]);

        $stats = $this->orderRepo->getStats();

        $totalOrders = $stats->total_orders;
        $pendingOrders = $stats->pending_orders;
        $shippingOrders = $stats->shipping_orders;
        $thisMonthRevenue = $stats->this_month_revenue;

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
            'status' => ['required', 'string', \Illuminate\Validation\Rule::in(array_column(OrderStatus::cases(), 'value'))],
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
