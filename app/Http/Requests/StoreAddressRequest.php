<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'ward'           => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'is_default'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận.',
            'phone.required'          => 'Vui lòng nhập số điện thoại.',
            'address.required'        => 'Vui lòng nhập địa chỉ.',
            'ward.required'           => 'Vui lòng nhập phường/xã.',
            'district.required'       => 'Vui lòng nhập quận/huyện.',
            'city.required'           => 'Vui lòng nhập tỉnh/thành phố.',
        ];
    }
}
