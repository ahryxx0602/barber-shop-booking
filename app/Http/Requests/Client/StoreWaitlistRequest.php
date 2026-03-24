<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaitlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barber_id'    => 'required|exists:barbers,id',
            'desired_date' => 'required|date|after:today',
            'desired_time' => 'nullable|date_format:H:i',
        ];
    }
}
