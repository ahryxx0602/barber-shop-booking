@extends('layouts.tailadmin')

@section('title', 'Quản lý Mã giảm giá')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý Mã giảm giá</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Danh sách tất cả coupon</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
           class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600">
            + Thêm coupon
        </a>
    </div>

    <div class="w-full">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-md text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Mã</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Giá trị</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sử dụng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Hạn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-bold text-gray-900 dark:text-white">{{ $coupon->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $coupon->type->label() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                @if($coupon->type === \App\Enums\CouponType::Fixed)
                                    {{ number_format($coupon->value, 0, ',', '.') }}đ
                                @else
                                    {{ $coupon->value }}%
                                    @if($coupon->max_discount)
                                        <span class="text-xs text-gray-500">(tối đa {{ number_format($coupon->max_discount, 0, ',', '.') }}đ)</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                {{ $coupon->used_count }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $coupon->expiry_date ? $coupon->expiry_date->format('d/m/Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($coupon->isValid())
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Hoạt động</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Hết hiệu lực</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                   class="text-brand-600 hover:text-brand-900 dark:text-brand-400">Sửa</a>
                                <button type="button"
                                    @click="$dispatch('open-confirm-modal', {
                                        title: 'Xóa coupon',
                                        message: 'Bạn có chắc chắn muốn xóa mã {{ $coupon->code }}?',
                                        action: '{{ route('admin.coupons.destroy', $coupon) }}',
                                        method: 'DELETE',
                                        variant: 'danger',
                                    })"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400">Xóa</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Chưa có coupon nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($coupons->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
