@extends('layouts.tailadmin')

@section('title', 'Quản lý Chi nhánh')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý Chi nhánh</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Danh sách tất cả chi nhánh</p>
        </div>
        <a href="{{ route('admin.branches.create') }}"
           class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600">
            + Thêm chi nhánh
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">Tổng chi nhánh</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">Đang hoạt động</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['active'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">Thợ đã gán</div>
            <div class="text-2xl font-bold text-brand-600 dark:text-brand-400 mt-1">{{ $stats['total_barbers'] }}</div>
        </div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tên chi nhánh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Địa chỉ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SĐT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Số thợ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($branches as $branch)
                        <tr>
                            <td class="px-6 py-4">
                                @if ($branch->image)
                                    <img src="{{ Storage::url($branch->image) }}"
                                         class="w-12 h-12 object-cover rounded-lg" alt="{{ $branch->name }}">
                                @else
                                    <div class="w-12 h-12 bg-brand-100 dark:bg-brand-900 rounded-lg flex items-center justify-center text-brand-700 dark:text-brand-300 font-semibold text-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->name }}</div>
                                @if ($branch->description)
                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $branch->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                {{ $branch->address }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                {{ $branch->phone ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-800 dark:bg-brand-900/30 dark:text-brand-400">
                                    {{ $branch->barbers_count }} thợ
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($branch->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                        Tạm đóng
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('admin.branches.edit', $branch) }}"
                                   class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300">Sửa</a>

                                <button
                                  type="button"
                                  @click="$dispatch('open-confirm-modal', {
                                    title: 'Xóa chi nhánh',
                                    message: 'Bạn có chắc chắn muốn xóa chi nhánh {{ addslashes(e($branch->name)) }}? Các thợ cắt thuộc chi nhánh này sẽ chuyển sang chưa gán.',
                                    confirmText: 'Xóa',
                                    cancelText: 'Hủy',
                                    action: '{{ route('admin.branches.destroy', $branch) }}',
                                    method: 'DELETE',
                                    variant: 'danger',
                                  })"
                                  class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                >Xóa</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Chưa có chi nhánh nào. <a href="{{ route('admin.branches.create') }}" class="text-brand-600 dark:text-brand-400">Thêm ngay</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($branches->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $branches->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
