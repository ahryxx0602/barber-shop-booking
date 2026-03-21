<?php

namespace App\Http\Requests\Barber;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'barber';
    }

    public function rules(): array
    {
        return [
            'schedules'              => ['required', 'array', 'size:7'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'schedules.*.is_working' => ['sometimes', 'boolean'],
            'schedules.*.start_time' => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i'],
            'schedules.*.end_time'   => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i', 'after:schedules.*.start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedules.*.start_time.required_if' => 'Giờ bắt đầu là bắt buộc khi ngày làm việc được bật.',
            'schedules.*.end_time.required_if'   => 'Giờ kết thúc là bắt buộc khi ngày làm việc được bật.',
            'schedules.*.end_time.after'         => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'schedules.*.start_time.date_format'  => 'Giờ bắt đầu phải có định dạng HH:MM.',
            'schedules.*.end_time.date_format'    => 'Giờ kết thúc phải có định dạng HH:MM.',
        ];
    }
}
