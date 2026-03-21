@extends('layouts.tailadmin')

@section('title', 'Sửa Thợ cắt')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sửa Thợ cắt: {{ $barber->user->name }}</h2>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">

                <form method="POST" action="{{ route('admin.barbers.update', $barber) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        {{-- Thông tin tài khoản --}}
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider">Thông tin tài khoản</h3>
                        </div>

                        {{-- Tên --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tên thợ cắt <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $barber->user->name) }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $barber->user->email) }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mật khẩu --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Mật khẩu mới <span class="text-gray-400 text-xs font-normal">(để trống nếu không đổi)</span>
                            </label>
                            <input type="password" name="password"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Số điện thoại --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số điện thoại</label>
                            <input type="text" name="phone" value="{{ old('phone', $barber->user->phone) }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                        </div>

                        {{-- Thông tin thợ --}}
                        <div class="pt-2 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider">Thông tin nghề nghiệp</h3>
                        </div>

                        {{-- Kinh nghiệm --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Số năm kinh nghiệm <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="experience_years" value="{{ old('experience_years', $barber->experience_years) }}" min="0" max="50"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('experience_years') border-red-500 @enderror">
                            @error('experience_years')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bio --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giới thiệu</label>
                            <textarea name="bio" rows="3"
                                      class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">{{ old('bio', $barber->bio) }}</textarea>
                        </div>

                        {{-- Avatar hiện tại --}}
                        @if ($barber->user->avatar)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ảnh hiện tại</label>
                                <img src="{{ Storage::url($barber->user->avatar) }}"
                                     class="w-20 h-20 object-cover rounded-full" alt="{{ $barber->user->name }}">
                            </div>
                        @endif

                        {{-- Upload ảnh mới --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $barber->user->avatar ? 'Thay ảnh mới (để trống nếu không đổi)' : 'Ảnh đại diện' }}
                            </label>
                            <input type="file" name="avatar" accept="image/*"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 @error('avatar') border-red-500 @enderror">
                            @error('avatar')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Trạng thái --}}
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $barber->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-brand-500">
                            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Kích hoạt thợ</label>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-md hover:bg-brand-600">
                            Cập nhật
                        </button>
                        <a href="{{ route('admin.barbers.index') }}"
                           class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Hủy
                        </a>
                    </div>
                </form>

        </div>
    </div>
@endsection
