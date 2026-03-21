@extends('layouts.tailadmin')

@section('title', 'Sửa Dịch vụ')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sửa Dịch vụ: {{ $service->name }}</h2>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">

                <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        {{-- Tên dịch vụ --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tên dịch vụ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $service->name) }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mô tả --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mô tả</label>
                            <textarea name="description" rows="3"
                                      class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $service->description) }}</textarea>
                        </div>

                        {{-- Giá và Thời gian --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Giá (VNĐ) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="price" value="{{ old('price', $service->price) }}" min="0" step="1000"
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror">
                                @error('price')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Thời gian (phút) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" min="15" max="300"
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('duration_minutes') border-red-500 @enderror">
                                @error('duration_minutes')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Ảnh hiện tại --}}
                        @if ($service->image)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ảnh hiện tại</label>
                                <img src="{{ Storage::url($service->image) }}"
                                     class="w-24 h-24 object-cover rounded-md" alt="{{ $service->name }}">
                            </div>
                        @endif

                        {{-- Upload ảnh mới --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $service->image ? 'Thay ảnh mới (để trống nếu không đổi)' : 'Ảnh dịch vụ' }}
                            </label>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 @error('image') border-red-500 @enderror">
                            @error('image')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Trạng thái --}}
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600">
                            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Kích hoạt dịch vụ</label>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Cập nhật
                        </button>
                        <a href="{{ route('admin.services.index') }}"
                           class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Hủy
                        </a>
                    </div>
                </form>

        </div>
    </div>
@endsection
