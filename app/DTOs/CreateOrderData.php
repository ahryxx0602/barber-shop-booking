<?php

/**
 * DTO: CreateOrderData
 *
 * Đóng gói dữ liệu cần thiết để đặt đơn hàng mới.
 * Items là mảng ['product_id' => ..., 'quantity' => ...].
 *
 * Dùng bởi: Client\OrderController::placeOrder() → OrderService::create()
 */

namespace App\DTOs;

use App\Enums\OrderPaymentMethod;
use Illuminate\Http\Request;

readonly class CreateOrderData
{
    public function __construct(
        public int $customer_id,
        public int $shipping_address_id,
        /** @var array<int, array{product_id: int, quantity: int}> */
        public array $items,
        public OrderPaymentMethod $payment_method,
        public ?string $note = null,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(Request $request, int $customerId): self
    {
        $data = $request->validated();

        return new self(
            customer_id: $customerId,
            shipping_address_id: (int) $data['shipping_address_id'],
            items: $data['items'] ?? [],
            payment_method: OrderPaymentMethod::from($data['payment_method']),
            note: $data['note'] ?? null,
        );
    }

    /**
     * Tạo DTO từ array (dùng cho test/seed).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customer_id: (int) $data['customer_id'],
            shipping_address_id: (int) $data['shipping_address_id'],
            items: $data['items'] ?? [],
            payment_method: $data['payment_method'] instanceof OrderPaymentMethod
                ? $data['payment_method']
                : OrderPaymentMethod::from($data['payment_method']),
            note: $data['note'] ?? null,
        );
    }
}
