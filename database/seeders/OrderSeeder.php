<?php

namespace Database\Seeders;

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        if ($customers->isEmpty()) return;

        $products = Product::all();
        if ($products->isEmpty()) return;

        $now = Carbon::now();

        // 1. PAST ORDERS (150 orders in last 60 days) - Status: Delivered
        for ($i = 0; $i < 150; $i++) {
            $customer = $customers->random();
            $orderDate = $now->copy()->subDays(rand(1, 60))->setHour(rand(7, 22))->setMinute(rand(0, 59));

            // Create Shipping Address
            $address = ShippingAddress::firstOrCreate(
                ['user_id' => $customer->id, 'is_default' => true],
                [
                    'recipient_name' => $customer->name,
                    'phone' => $customer->phone ?? '09' . rand(10000000, 99999999),
                    'address' => rand(1, 999) . ' Đường Nguyễn Trãi',
                    'ward' => 'Phường ' . rand(1, 15),
                    'district' => 'Quận ' . rand(1, 10),
                    'city' => 'TP.HCM',
                ]
            );

            // Create Order
            $shippingFee = rand(2, 5) * 10000;
            $order = Order::create([
                'order_code' => 'ORD' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'shipping_address_id' => $address->id,
                'status' => OrderStatus::Delivered,
                'subtotal' => 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'shipping_distance_km' => rand(1, 15),
                'total_amount' => 0,
                'shipping_fee' => $shippingFee,
                'note' => rand(0, 1) ? 'Giao giờ hành chính' : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate->copy()->addDays(rand(1, 3)),
            ]);

            // Add Items
            $itemCount = rand(1, 3);
            $totalAmount = 0;
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $subtotal,
                ]);
            }

            $order->update([
                'subtotal' => $totalAmount,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'shipping_distance_km' => rand(1, 15),
                'total_amount' => $totalAmount + $shippingFee
            ]);

            // Add Payment
            $paymentMethod = collect([OrderPaymentMethod::Cod, OrderPaymentMethod::VNPay, OrderPaymentMethod::Momo])->random();
            OrderPayment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'status' => PaymentStatus::Paid,
                'amount' => $order->total_amount,
                'transaction_id' => $paymentMethod !== OrderPaymentMethod::Cod ? 'TXN' . rand(100000, 999999) : null,
                'paid_at' => $orderDate->copy()->addHours(rand(1, 24)),
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }

        // 2. FUTURE/CURRENT ORDERS (20 orders) - Status: Pending/Shipping
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $orderDate = $now->copy()->subHours(rand(1, 72))->setMinute(rand(0, 59));
            $status = rand(0, 1) ? OrderStatus::Pending : OrderStatus::Shipping;

            $address = ShippingAddress::firstOrCreate(
                ['user_id' => $customer->id, 'is_default' => true],
                [
                    'recipient_name' => $customer->name,
                    'phone' => $customer->phone ?? '09' . rand(10000000, 99999999),
                    'address' => rand(1, 999) . ' Đường Lê Lợi',
                    'ward' => 'Phường Bến Nghé',
                    'district' => 'Quận 1',
                    'city' => 'TP.HCM',
                ]
            );

            $shippingFee = rand(2, 5) * 10000;
            $order = Order::create([
                'order_code' => 'ORD' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'shipping_address_id' => $address->id,
                'status' => $status,
                'subtotal' => 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'shipping_distance_km' => rand(1, 15),
                'total_amount' => 0,
                'shipping_fee' => $shippingFee,
                'note' => rand(0, 3) === 0 ? 'Gọi trước khi giao' : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            $itemCount = rand(1, 3);
            $totalAmount = 0;
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $subtotal,
                ]);
            }

            $order->update([
                'subtotal' => $totalAmount,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'shipping_distance_km' => rand(1, 15),
                'total_amount' => $totalAmount + $shippingFee
            ]);

            $paymentMethod = collect([OrderPaymentMethod::Cod, OrderPaymentMethod::VNPay, OrderPaymentMethod::Momo])->random();
            $paymentStatus = $paymentMethod !== OrderPaymentMethod::Cod && rand(0, 1) ? PaymentStatus::Paid : PaymentStatus::Pending;

            OrderPayment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'status' => $paymentStatus,
                'amount' => $order->total_amount,
                'transaction_id' => $paymentStatus === PaymentStatus::Paid ? 'TXN' . rand(100000, 999999) : null,
                'paid_at' => $paymentStatus === PaymentStatus::Paid ? $orderDate->copy()->addMinutes(rand(10, 60)) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }
    }
}
