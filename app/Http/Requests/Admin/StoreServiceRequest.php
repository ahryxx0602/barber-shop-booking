<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:100'],
            'description'      => ['nullable', 'string'],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
            'image'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'        => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Tên dịch vụ không được để trống.',
            'name.max'                  => 'Tên dịch vụ tối đa 100 ký tự.',
            'price.required'            => 'Giá không được để trống.',
            'price.numeric'             => 'Giá phải là số.',
            'price.min'                 => 'Giá không được âm.',
            'duration_minutes.required' => 'Thời gian thực hiện không được để trống.',
            'duration_minutes.integer'  => 'Thời gian phải là số nguyên.',
            'duration_minutes.min'      => 'Thời gian tối thiểu là 15 phút.',
            'duration_minutes.max'      => 'Thời gian tối đa là 300 phút.',
            'image.image'               => 'File phải là ảnh.',
            'image.mimes'               => 'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'image.max'                 => 'Ảnh tối đa 2MB.',
        ];
    }
}
