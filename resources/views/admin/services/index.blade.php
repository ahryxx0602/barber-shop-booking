@extends('layouts.tailadmin')

@section('title', 'Quản lý Dịch vụ')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý Dịch vụ</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Danh sách tất cả dịch vụ</p>
        </div>
        <a href="{{ route('admin.services.create') }}"
           class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600">
            + Thêm dịch vụ
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ảnh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tên dịch vụ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Thời gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($services as $service)
                            <tr>
                                <td class="px-6 py-4">
                                    @if ($service->image)
                                        <img src="{{ Storage::url($service->image) }}"
                                             class="w-12 h-12 object-cover rounded-md" alt="{{ $service->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center text-gray-400 text-xs">
                                            N/A
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</div>
                                    @if ($service->description)
                                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $service->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                    {{ number_format($service->price, 0, ',', '.') }}đ
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $service->duration_minutes }} phút
                                </td>
                                <td class="px-6 py-4">
                                    @if ($service->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                            Đang hoạt động
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                            Tạm ngưng
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    <a href="{{ route('admin.services.edit', $service) }}"
                                       class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300">Sửa</a>

                                    <button
                                      type="button"
                                      @click="$dispatch('open-confirm-modal', {
                                        title: 'Xóa dịch vụ',
                                        message: 'Bạn có chắc chắn muốn xóa dịch vụ {{ addslashes(e($service->name)) }}? Hành động này không thể hoàn tác.',
                                        confirmText: 'Xóa',
                                        cancelText: 'Hủy',
                                        action: '{{ route('admin.services.destroy', $service) }}',
                                        method: 'DELETE',
                                        variant: 'danger',
                                      })"
                                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >Xóa</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Chưa có dịch vụ nào. <a href="{{ route('admin.services.create') }}" class="text-brand-600 dark:text-brand-400">Thêm ngay</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($services->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $services->links() }}
                    </div>
                @endif
            </div>

    </div>
@endsection
