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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->time_slot_id) {
                $slot = \App\Models\TimeSlot::find($this->time_slot_id);
                if ($slot) {
                    $slotDateTime = \Carbon\Carbon::parse($slot->slot_date . ' ' . $slot->start_time);
                    if ($slotDateTime->isPast()) {
                        $validator->errors()->add('time_slot_id', 'Giờ hẹn này đã qua, vui lòng chọn giờ khác.');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'service_ids.required' => 'Vui lòng chọn ít nhất một dịch vụ.',
            'barber_id.required' => 'Vui lòng chọn thợ cắt.',
            'time_slot_id.required' => 'Vui lòng chọn giờ hẹn.',
            'guest_name.required' => 'Vui lòng nhập họ tên.',
            'guest_phone.required' => 'Vui lòng nhập số điện thoại.',
            'guest_email.required' => 'Vui lòng nhập email.',
            'guest_email.email' => 'Email không hợp lệ.',
        ];
    }
}
