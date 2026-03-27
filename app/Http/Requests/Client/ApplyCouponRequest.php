<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coupon_code' => ['required', 'string'],
            'total_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.required' => 'Vui lòng nhập mã giảm giá.',
            'total_price.required' => 'Thiếu thông tin tổng giá.',
        ];
    }
}
