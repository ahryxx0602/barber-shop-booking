<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string', 'max:500'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Tên chi nhánh không được để trống.',
            'name.max'         => 'Tên chi nhánh không được vượt quá 255 ký tự.',
            'address.required' => 'Địa chỉ không được để trống.',
            'address.max'      => 'Địa chỉ không được vượt quá 500 ký tự.',
            'image.image'      => 'File phải là ảnh.',
            'image.max'        => 'Ảnh không được vượt quá 2MB.',
        ];
    }
}
