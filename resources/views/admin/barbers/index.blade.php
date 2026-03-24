@extends('layouts.tailadmin')

@section('title', 'Quản lý Thợ cắt')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý Thợ cắt</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Danh sách tất cả thợ cắt tóc</p>
        </div>
        <a href="{{ route('admin.barbers.create') }}"
           class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600">
            + Thêm thợ cắt
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tên thợ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SĐT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kinh nghiệm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Chi nhánh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Đánh giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($barbers as $barber)
                            <tr>
                                <td class="px-6 py-4">
                                    @if ($barber->user->avatar)
                                        <img src="{{ Storage::url($barber->user->avatar) }}"
                                             class="w-10 h-10 object-cover rounded-full" alt="{{ $barber->user->name }}">
                                    @else
                                        <div class="w-10 h-10 bg-brand-100 dark:bg-brand-900 rounded-full flex items-center justify-center text-brand-700 dark:text-brand-300 font-semibold text-sm">
                                            {{ strtoupper(substr($barber->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $barber->user->name }}</div>
                                    @if ($barber->bio)
                                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $barber->bio }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $barber->user->email }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $barber->user->phone ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $barber->experience_years }} năm
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($barber->branch)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $barber->branch->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 text-xs">Chưa gán</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 text-sm">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-gray-900 dark:text-gray-300">{{ number_format($barber->rating, 1) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($barber->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                            Đang hoạt động
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                            Tạm nghỉ
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    <a href="{{ route('admin.barbers.edit', $barber) }}"
                                       class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300">Sửa</a>

                                    <button
                                      type="button"
                                      @click="$dispatch('open-confirm-modal', {
                                        title: 'Xóa thợ cắt',
                                        message: 'Bạn có chắc chắn muốn xóa thợ cắt {{ addslashes(e($barber->user->name)) }}? Tài khoản user của thợ cũng sẽ bị xóa. Hành động này không thể hoàn tác.',
                                        confirmText: 'Xóa',
                                        cancelText: 'Hủy',
                                        action: '{{ route('admin.barbers.destroy', $barber) }}',
                                        method: 'DELETE',
                                        variant: 'danger',
                                      })"
                                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >Xóa</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Chưa có thợ cắt nào. <a href="{{ route('admin.barbers.create') }}" class="text-brand-600 dark:text-brand-400">Thêm ngay</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($barbers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $barbers->links() }}
                    </div>
                @endif
            </div>

    </div>
@endsection
