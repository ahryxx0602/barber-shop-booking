<?php

namespace App\Http\Requests\Barber;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_date' => 'required|date|after:today',
            'type'       => 'required|in:full_day,partial',
            'start_time' => 'nullable|required_if:type,partial|date_format:H:i',
            'end_time'   => 'nullable|required_if:type,partial|date_format:H:i|after:start_time',
            'reason'     => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'leave_date.required'    => 'Vui lòng chọn ngày nghỉ.',
            'leave_date.after'       => 'Chỉ có thể đăng ký nghỉ từ ngày mai trở đi.',
            'type.required'          => 'Vui lòng chọn loại nghỉ.',
            'start_time.required_if' => 'Vui lòng chọn giờ bắt đầu khi nghỉ một phần ngày.',
            'end_time.required_if'   => 'Vui lòng chọn giờ kết thúc khi nghỉ một phần ngày.',
            'end_time.after'         => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'reason.max'             => 'Lý do không quá 255 ký tự.',
        ];
    }
}
