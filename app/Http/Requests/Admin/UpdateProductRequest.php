<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'price'          => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku'            => ['nullable', 'string', 'max:50', 'unique:products,sku,' . $this->route('product')?->id],
            'category'       => ['required', new Enum(ProductCategory::class)],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'      => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Tên sản phẩm không được để trống.',
            'name.max'                => 'Tên sản phẩm tối đa 255 ký tự.',
            'price.required'          => 'Giá không được để trống.',
            'price.numeric'           => 'Giá phải là số.',
            'price.min'               => 'Giá không được âm.',
            'stock_quantity.required' => 'Số lượng tồn kho không được để trống.',
            'stock_quantity.integer'  => 'Số lượng tồn kho phải là số nguyên.',
            'stock_quantity.min'      => 'Số lượng tồn kho không được âm.',
            'sku.unique'              => 'Mã SKU đã tồn tại.',
            'category.required'       => 'Danh mục không được để trống.',
            'image.image'             => 'File phải là ảnh.',
            'image.mimes'             => 'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'image.max'               => 'Ảnh tối đa 2MB.',
        ];
    }
}
