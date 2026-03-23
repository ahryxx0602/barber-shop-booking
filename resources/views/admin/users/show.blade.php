@extends('layouts.tailadmin')
@use('App\Enums\UserRole')
@use('App\Enums\BookingStatus')

@section('title', 'Chi tiết: ' . $user->name)

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Chi tiết tài khoản</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <a href="{{ route('admin.users.index') }}" class="text-brand-600 dark:text-brand-400 hover:underline">Người dùng</a>
                / {{ $user->name }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}"
               class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600">
                Sửa thông tin
            </a>
            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggleActive', $user) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium rounded-lg border {{ $user->is_active
                                ? 'border-red-300 text-red-700 hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:bg-red-900/20'
                                : 'border-green-300 text-green-700 hover:bg-green-50 dark:border-green-700 dark:text-green-400 dark:hover:bg-green-900/20' }}">
                        {{ $user->is_active ? 'Vô hiệu hoá' : 'Kích hoạt' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

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

    {{-- User Info Card --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Thông tin chính --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col items-center text-center">
                    @if ($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}"
                             class="w-20 h-20 object-cover rounded-full border-2 border-brand-300 dark:border-brand-600 mb-3"
                             alt="{{ $user->name }}">
                    @else
                        <div class="w-20 h-20 bg-brand-100 dark:bg-brand-900 rounded-full flex items-center justify-center text-brand-700 dark:text-brand-300 font-bold text-2xl mb-3">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $user->name }}</h3>

                    @php
                        $roleBadge = match ($user->role) {
                            UserRole::Admin    => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                            UserRole::Barber   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                            UserRole::Customer => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                        };
                    @endphp
                    <span class="mt-1 px-3 py-1 text-xs font-semibold rounded-full {{ $roleBadge }}">
                        {{ $user->role->label() }}
                    </span>

                    @if (!$user->is_active)
                        <span class="mt-2 px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                            Vô hiệu hoá
                        </span>
                    @endif
                </div>

                <hr class="my-4 border-gray-200 dark:border-gray-700">

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $user->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Số điện thoại</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $user->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Ngày tạo</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if ($user->email_verified_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Xác thực email</dt>
                            <dd class="text-green-600 dark:text-green-400 font-medium">{{ $user->email_verified_at->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                </dl>

                {{-- Thông tin thợ cắt nếu role = barber --}}
                @if ($user->role === UserRole::Barber && $user->barber)
                    <hr class="my-4 border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Thông tin thợ cắt</h4>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Kinh nghiệm</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $user->barber->experience_years }} năm</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Đánh giá</dt>
                            <dd class="flex items-center gap-1 text-gray-900 dark:text-white font-medium">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                {{ number_format($user->barber->rating, 1) }}
                            </dd>
                        </div>
                        @if ($user->barber->bio)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 mb-1">Giới thiệu</dt>
                                <dd class="text-gray-900 dark:text-gray-300 text-sm">{{ $user->barber->bio }}</dd>
                            </div>
                        @endif
                    </dl>
                @endif
            </div>
        </div>

        {{-- Right: Lịch sử booking (customer) --}}
        <div class="lg:col-span-2">
            @if ($user->role === UserRole::Customer)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Lịch sử đặt lịch</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">10 booking gần nhất</p>
                    </div>

                    @if ($user->bookings && $user->bookings->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Mã</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Thợ cắt</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dịch vụ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ngày</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Giá</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($user->bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-300 font-mono">#{{ $booking->id }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $booking->barber?->user?->name ?? '—' }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-300">
                                            {{ $booking->services->pluck('name')->join(', ') }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                            <span class="text-xs">{{ $booking->start_time }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-300 font-medium">
                                            {{ number_format($booking->total_price, 0, ',', '.') }}₫
                                        </td>
                                        <td class="px-6 py-3">
                                            @php
                                                $statusClass = match ($booking->status) {
                                                    BookingStatus::Pending     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                    BookingStatus::Confirmed   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                    BookingStatus::InProgress  => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                                    BookingStatus::Completed   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                    BookingStatus::Cancelled   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                    default                    => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                {{ $booking->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Chưa có lịch đặt nào.
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if ($user->role === UserRole::Barber)
                            Tài khoản thợ cắt — quản lý chi tiết tại <a href="{{ route('admin.barbers.index') }}" class="text-brand-600 dark:text-brand-400 hover:underline">mục Thợ cắt</a>.
                        @else
                            Tài khoản quản trị viên.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
