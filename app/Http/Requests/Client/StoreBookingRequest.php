<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['exists:services,id'],
            'barber_id' => ['required', 'exists:barbers,id'],
            'time_slot_id' => ['required', 'exists:time_slots,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_ids.required' => 'Vui long chon it nhat mot dich vu.',
            'barber_id.required' => 'Vui long chon tho cat.',
            'time_slot_id.required' => 'Vui long chon gio hen.',
        ];
    }
}
