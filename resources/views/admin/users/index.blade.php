@extends('layouts.tailadmin')
@use('App\Enums\UserRole')

@section('title', 'Quản lý Người dùng')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý Người dùng</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Danh sách tất cả tài khoản trong hệ thống</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tổng cộng</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Khách hàng</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Thợ cắt</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $totalBarbers }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quản trị</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $totalAdmins }}</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Lọc theo role --}}
            <select name="role" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
                <option value="">Tất cả vai trò</option>
                @foreach (UserRole::cases() as $role)
                    <option value="{{ $role->value }}" {{ request('role') === $role->value ? 'selected' : '' }}>
                        {{ $role->label() }}
                    </option>
                @endforeach
            </select>

            {{-- Tìm kiếm --}}
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm theo tên, email hoặc SĐT..."
                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-md hover:bg-brand-600">
                    Lọc
                </button>
                @if (request('role') || request('search'))
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                        Xoá bộ lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Flash message --}}
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

    {{-- Users Table --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Người dùng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SĐT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vai trò</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ngày tạo</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($users as $user)
                    <tr>
                        {{-- Avatar + Tên --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if ($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}"
                                         class="w-9 h-9 object-cover rounded-full" alt="{{ $user->name }}">
                                @else
                                    <div class="w-9 h-9 bg-brand-100 dark:bg-brand-900 rounded-full flex items-center justify-center text-brand-700 dark:text-brand-300 font-semibold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $user->email }}</td>

                        {{-- SĐT --}}
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $user->phone ?? '—' }}</td>

                        {{-- Role badge --}}
                        <td class="px-6 py-4">
                            @php
                                $roleBadge = match ($user->role) {
                                    UserRole::Admin    => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                    UserRole::Barber   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                    UserRole::Customer => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $roleBadge }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>

                        {{-- Trạng thái --}}
                        <td class="px-6 py-4">
                            @if ($user->is_active)
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                                    Hoạt động
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                    Vô hiệu
                                </span>
                            @endif
                        </td>

                        {{-- Ngày tạo --}}
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">Xem</a>
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300">Sửa</a>
                            <form method="POST" action="{{ route('admin.users.toggleActive', $user) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                        class="{{ $user->id === auth()->id() ? 'text-gray-400 cursor-not-allowed' : ($user->is_active ? 'text-red-600 hover:text-red-900 dark:text-red-400' : 'text-green-600 hover:text-green-900 dark:text-green-400') }}">
                                    {{ $user->is_active ? 'Khoá' : 'Mở khoá' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Không tìm thấy tài khoản nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
