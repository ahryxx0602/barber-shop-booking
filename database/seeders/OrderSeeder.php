<?php

namespace Database\Seeders;

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', \App\Enums\UserRole::Customer)->get();
        if ($customers->isEmpty()) {
            return;
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            // Bias towards delivered and confirmed for realistic stats
            $statusOptions = [OrderStatus::Delivered, OrderStatus::Delivered, OrderStatus::Delivered, OrderStatus::Confirmed, OrderStatus::Shipping, OrderStatus::Pending, OrderStatus::Cancelled];
            $status = $statusOptions[array_rand($statusOptions)];
            
            $orderDate = now()->subDays(rand(0, 45))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // Create Shipping Address
            $address = ShippingAddress::create([
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone ?? '09' . rand(10000000, 99999999),
                'address' => rand(1, 999) . ' Đường ngẫu nhiên, Phường ' . rand(1, 15) . ', Quận ' . rand(1, 10) . ', TP.HCM',
                'is_default' => true,
            ]);

            // Create Order
            $order = Order::create([
                'order_code' => 'ORD' . str_pad($i + 1, 5, '0', STR_PAD_LEFT) . strtoupper(Str::random(3)),
                'customer_id' => $customer->id,
                'shipping_address_id' => $address->id,
                'status' => $status,
                'total_amount' => 0, // will be updated later
                'shipping_fee' => 30000,
                'notes' => rand(0, 1) ? 'Giao giờ hành chính' : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            if ($status === OrderStatus::Cancelled) {
                $order->update(['cancellation_reason' => 'Khách hàng đổi ý hoặc huỷ tự động']);
            }

            // Add Items
            $itemCount = rand(1, 4);
            $totalAmount = 0;
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
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

            $totalAmount += $order->shipping_fee;
            $order->update(['total_amount' => $totalAmount]);

            // Add Payment
            $paymentMethod = rand(0, 1) ? OrderPaymentMethod::COD : OrderPaymentMethod::BankTransfer;
            $paymentStatus = OrderPaymentStatus::Pending;
            
            if ($status === OrderStatus::Delivered) {
                $paymentStatus = OrderPaymentStatus::Paid;
            } else if ($paymentMethod === OrderPaymentMethod::BankTransfer && $status !== OrderStatus::Pending && $status !== OrderStatus::Cancelled) {
                $paymentStatus = OrderPaymentStatus::Paid;
            } else if ($status === OrderStatus::Cancelled) {
                $paymentStatus = OrderPaymentStatus::Failed;
            }

            OrderPayment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'amount' => $totalAmount,
                'transaction_id' => $paymentMethod === OrderPaymentMethod::BankTransfer ? 'TXN' . rand(100000, 999999) : null,
                'paid_at' => $paymentStatus === OrderPaymentStatus::Paid ? $orderDate->copy()->addHours(rand(1, 24)) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }
    }
}
