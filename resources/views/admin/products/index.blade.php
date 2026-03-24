@extends('layouts.tailadmin')

@section('title', 'Quản lý Sản phẩm')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý Sản phẩm</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Module E-commerce — Sản phẩm chăm sóc tóc</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600">
            + Thêm sản phẩm
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tổng sản phẩm</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Đang bán</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $activeProducts }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Hết hàng</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $outOfStock }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3">
            <select name="category" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                <option value="">Tất cả danh mục</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->value }}" {{ ($filters['category'] ?? '') === $cat->value ? 'selected' : '' }}>
                        {{ $cat->label() }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên..."
                   class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 w-48">
            <button type="submit"
                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                Lọc
            </button>
            @if (!empty($filters['category']) || !empty($filters['search']))
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    ✕ Xóa bộ lọc
                </a>
            @endif
        </form>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ảnh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sản phẩm</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Giá</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tồn kho</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Danh mục</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}"
                                     class="w-12 h-12 object-cover rounded-md" alt="{{ $product->name }}">
                            @else
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center text-gray-400 text-xs">
                                    N/A
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        </td>
                        <td class="px-4 py-3">
                            @if ($product->stock_quantity <= 0)
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                    Hết hàng
                                </span>
                            @elseif ($product->stock_quantity <= 5)
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full">
                                    {{ $product->stock_quantity }}
                                </span>
                            @else
                                <span class="text-sm text-gray-900 dark:text-gray-300">{{ $product->stock_quantity }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                                {{ $product->category->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($product->is_active)
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                                    Đang bán
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                    Tạm ẩn
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm space-x-2 whitespace-nowrap">
                            {{-- Toggle --}}
                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="{{ $product->is_active ? 'Tạm ẩn' : 'Kích hoạt' }}"
                                        class="{{ $product->is_active ? 'text-yellow-600 hover:text-yellow-800 dark:text-yellow-400' : 'text-green-600 hover:text-green-800 dark:text-green-400' }}">
                                    @if ($product->is_active)
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>

                            {{-- Edit --}}
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300">Sửa</a>

                            {{-- Delete --}}
                            <button type="button"
                                @click="$dispatch('open-confirm-modal', {
                                    title: 'Xóa sản phẩm',
                                    message: 'Bạn có chắc chắn muốn xóa sản phẩm {{ addslashes(e($product->name)) }}? Hành động này không thể hoàn tác.',
                                    confirmText: 'Xóa',
                                    cancelText: 'Hủy',
                                    action: '{{ route('admin.products.destroy', $product) }}',
                                    method: 'DELETE',
                                    variant: 'danger',
                                })"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Xóa</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Chưa có sản phẩm nào. <a href="{{ route('admin.products.create') }}" class="text-brand-600 dark:text-brand-400">Thêm ngay</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
