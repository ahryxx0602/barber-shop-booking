<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    public function __construct(
        protected OrderPaymentService $orderPaymentService
    ) {}

    public function vnpayReturn(Request $request)
    {
        $result = $this->orderPaymentService->verifyVNPayCallback($request->all());

        if (!$result['order']) {
            return redirect()->route('client.shop.index')->with('error', $result['message']);
        }

        $order = $result['order'];

        if ($result['success']) {
            return redirect()->route('client.shop.order-success', $order->id)->with('success', $result['message']);
        }

        return redirect()->route('client.orders.show', $order->id)->with('error', $result['message']);
    }

    public function momoReturn(Request $request)
    {
        $result = $this->orderPaymentService->verifyMomoCallback($request->all());

        if (!$result['order']) {
            return redirect()->route('client.shop.index')->with('error', $result['message']);
        }

        $order = $result['order'];

        if ($result['success']) {
            return redirect()->route('client.shop.order-success', $order->id)->with('success', $result['message']);
        }

        return redirect()->route('client.orders.show', $order->id)->with('error', $result['message']);
    }
}
