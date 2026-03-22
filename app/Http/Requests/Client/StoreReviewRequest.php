<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'exists:bookings,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Thieu thong tin booking.',
            'booking_id.exists' => 'Booking khong ton tai.',
            'rating.required' => 'Vui long chon so sao danh gia.',
            'rating.min' => 'Danh gia toi thieu la 1 sao.',
            'rating.max' => 'Danh gia toi da la 5 sao.',
            'comment.max' => 'Nhan xet khong duoc qua 1000 ky tu.',
        ];
    }
}
