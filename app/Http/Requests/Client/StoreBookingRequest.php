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
        $rules = [
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['exists:services,id'],
            'barber_id' => ['required', 'exists:barbers,id'],
            'time_slot_id' => ['required', 'exists:time_slots,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ];

        if (!$this->user()) {
            $rules['guest_name'] = ['required', 'string', 'max:255'];
            $rules['guest_phone'] = ['required', 'string', 'max:20'];
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'service_ids.required' => 'Vui long chon it nhat mot dich vu.',
            'barber_id.required' => 'Vui long chon tho cat.',
            'time_slot_id.required' => 'Vui long chon gio hen.',
            'guest_name.required' => 'Vui long nhap ho ten.',
            'guest_phone.required' => 'Vui long nhap so dien thoai.',
            'guest_email.required' => 'Vui long nhap email.',
            'guest_email.email' => 'Email khong hop le.',
        ];
    }
}
