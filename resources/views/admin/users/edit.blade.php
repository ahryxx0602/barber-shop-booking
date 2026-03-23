@extends('layouts.tailadmin')
@use('App\Enums\UserRole')

@section('title', 'Sửa tài khoản: ' . $user->name)

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sửa tài khoản: {{ $user->name }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            <a href="{{ route('admin.users.index') }}" class="text-brand-600 dark:text-brand-400 hover:underline">Người dùng</a>
            / Sửa
        </p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    {{-- Thông tin cơ bản --}}
                    <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider">Thông tin cơ bản</h3>
                    </div>

                    {{-- Tên --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Họ tên <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
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
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Số điện thoại --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Vai trò --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Vai trò <span class="text-red-500">*</span>
                        </label>
                        <select name="role"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('role') border-red-500 @enderror">
                            @foreach (UserRole::cases() as $role)
                                <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Trạng thái --}}
                    @if ($user->id !== auth()->id())
                        <div class="pt-2 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider">Trạng thái</h3>
                        </div>

                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('admin.users.toggleActive', $user) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium rounded-lg border {{ $user->is_active
                                            ? 'border-red-300 text-red-700 hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:bg-red-900/20'
                                            : 'border-green-300 text-green-700 hover:bg-green-50 dark:border-green-700 dark:text-green-400 dark:hover:bg-green-900/20' }}">
                                    {{ $user->is_active ? 'Vô hiệu hoá tài khoản' : 'Kích hoạt tài khoản' }}
                                </button>
                            </form>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Hiện tại:
                                @if ($user->is_active)
                                    <span class="text-green-600 dark:text-green-400 font-medium">Đang hoạt động</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400 font-medium">Đã vô hiệu hoá</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-md hover:bg-brand-600">
                        Cập nhật
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
