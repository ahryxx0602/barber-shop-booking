<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => ['required', 'integer', 'exists:shipping_addresses,id'],
            'payment_method'      => ['required', 'in:cod,vnpay,momo'],
            'note'                => ['nullable', 'string', 'max:500'],
            'product_coupon_code' => ['nullable', 'string', 'max:50'],
            'shipping_coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                $validator->errors()->add('cart', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
            }
        });
    }
}
