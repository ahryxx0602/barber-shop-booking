<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class GetSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barber_id' => ['required', 'exists:barbers,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'barber_id.required' => 'Vui lòng chọn thợ cắt.',
            'barber_id.exists' => 'Thợ cắt không tồn tại.',
            'date.required' => 'Vui lòng chọn ngày.',
            'date.after_or_equal' => 'Ngày phải từ hôm nay trở đi.',
        ];
    }
}
