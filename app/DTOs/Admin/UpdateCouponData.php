<?php

/**
 * DTO: UpdateCouponData
 *
 * Đóng gói dữ liệu cần thiết để cập nhật mã giảm giá.
 *
 * Dùng bởi: Admin\CouponController::update() → CouponService::updateCoupon()
 */

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\UpdateCouponRequest;

readonly class UpdateCouponData
{
    public function __construct(
        public string $code,
        public string $type,
        public string $applies_to,
        public float $value,
        public ?float $min_amount = null,
        public ?float $max_discount = null,
        public ?string $expiry_date = null,
        public ?int $usage_limit = null,
        public bool $is_active = true,
    ) {}

    /**
     * Tạo DTO từ validated request.
     */
    public static function fromRequest(UpdateCouponRequest $request): self
    {
        $data = $request->validated();

        return new self(
            code: strtoupper(trim($data['code'])),
            type: $data['type'],
            applies_to: $data['applies_to'],
            value: (float) $data['value'],
            min_amount: isset($data['min_amount']) ? (float) $data['min_amount'] : null,
            max_discount: isset($data['max_discount']) ? (float) $data['max_discount'] : null,
            expiry_date: $data['expiry_date'] ?? null,
            usage_limit: isset($data['usage_limit']) ? (int) $data['usage_limit'] : null,
            is_active: $request->has('is_active'),
        );
    }

    /**
     * Chuyển DTO thành array để lưu vào DB.
     */
    public function toArray(): array
    {
        return [
            'code'         => $this->code,
            'type'         => $this->type,
            'applies_to'   => $this->applies_to,
            'value'        => $this->value,
            'min_amount'   => $this->min_amount,
            'max_discount' => $this->max_discount,
            'expiry_date'  => $this->expiry_date,
            'usage_limit'  => $this->usage_limit,
            'is_active'    => $this->is_active,
        ];
    }
}
