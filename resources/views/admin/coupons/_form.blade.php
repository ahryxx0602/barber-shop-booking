<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Mã coupon --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mã coupon</label>
        <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}"
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm uppercase"
               placeholder="VD: GIAM20K" required>
        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Loại --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loại giảm giá</label>
        <select name="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            @foreach($types as $type)
                <option value="{{ $type->value }}" {{ old('type', $coupon->type->value ?? '') === $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Áp dụng cho --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Áp dụng cho</label>
        <select name="applies_to" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            @foreach($appliesToOptions as $option)
                <option value="{{ $option->value }}" {{ old('applies_to', $coupon->applies_to->value ?? 'product') === $option->value ? 'selected' : '' }}>
                    {{ $option->label() }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Giá trị --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giá trị (VND hoặc %)</label>
        <input type="number" name="value" value="{{ old('value', $coupon->value ?? '') }}" step="0.01" min="0"
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" required>
        @error('value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Đơn tối thiểu --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Đơn tối thiểu (VND)</label>
        <input type="number" name="min_amount" value="{{ old('min_amount', $coupon->min_amount ?? 0) }}" step="1000" min="0"
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
    </div>

    {{-- Giảm tối đa --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giảm tối đa (VND) <span class="text-xs text-gray-400">— chỉ cho loại %</span></label>
        <input type="number" name="max_discount" value="{{ old('max_discount', $coupon->max_discount ?? '') }}" step="1000" min="0"
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
    </div>

    {{-- Hạn sử dụng --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày hết hạn</label>
        <input type="date" name="expiry_date" value="{{ old('expiry_date', isset($coupon) && $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d') : '') }}"
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
    </div>

    {{-- Giới hạn lượt --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giới hạn lượt dùng</label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" min="1"
               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
               placeholder="Để trống = không giới hạn">
    </div>

    {{-- Trạng thái --}}
    <div class="flex items-center gap-2 pt-6">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-brand-600 dark:border-gray-600 dark:bg-gray-700">
        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Kích hoạt coupon</label>
    </div>
</div>
