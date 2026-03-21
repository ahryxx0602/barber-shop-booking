@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quản lý Dịch vụ</h2>
        <a href="{{ route('admin.services.create') }}"
           class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
            + Thêm dịch vụ
        </a>
    </div>
@endsection

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ảnh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên dịch vụ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($services as $service)
                            <tr>
                                <td class="px-6 py-4">
                                    @if ($service->image)
                                        <img src="{{ Storage::url($service->image) }}"
                                             class="w-12 h-12 object-cover rounded-md" alt="{{ $service->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center text-gray-400 text-xs">
                                            N/A
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                    @if ($service->description)
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ $service->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ number_format($service->price, 0, ',', '.') }}đ
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
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
                                       class="text-indigo-600 hover:text-indigo-900">Sửa</a>

                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                          class="inline"
                                          onsubmit="return confirm('Xác nhận xóa dịch vụ này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Chưa có dịch vụ nào. <a href="{{ route('admin.services.create') }}" class="text-indigo-600">Thêm ngay</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($services->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $services->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
