<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')->id;

        return [
            'code'         => 'required|string|max:50|unique:coupons,code,' . $couponId,
            'type'         => 'required|in:fixed,percent',
            'applies_to'   => 'required|in:product,shipping,booking',
            'value'        => 'required|numeric|min:0',
            'min_amount'   => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'expiry_date'  => 'nullable|date',
            'usage_limit'  => 'nullable|integer|min:1',
            'is_active'    => 'boolean',
        ];
    }
}
