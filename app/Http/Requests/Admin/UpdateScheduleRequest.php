<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedules'               => ['required', 'array', 'size:7'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'schedules.*.is_working'  => ['sometimes', 'boolean'],
            'schedules.*.start_time'  => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i'],
            'schedules.*.end_time'    => ['required_if:schedules.*.is_working,1', 'nullable', 'date_format:H:i', 'after:schedules.*.start_time'],
        ];
    }
}
