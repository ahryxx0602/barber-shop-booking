<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBarberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('barber')->user_id;

        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password'         => ['nullable', 'string', 'min:8'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'avatar'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bio'              => ['nullable', 'string', 'max:1000'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:50'],
            'is_active'        => ['nullable', 'boolean'],
            'branch_id'        => ['nullable', 'exists:branches,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Tên thợ không được để trống.',
            'email.required'            => 'Email không được để trống.',
            'email.unique'              => 'Email này đã được sử dụng.',
            'password.min'              => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'experience_years.required' => 'Số năm kinh nghiệm không được để trống.',
            'avatar.image'              => 'File phải là ảnh.',
            'avatar.max'                => 'Ảnh không được vượt quá 2MB.',
        ];
    }
}
