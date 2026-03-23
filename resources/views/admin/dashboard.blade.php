@extends('layouts.tailadmin')
@use('App\Enums\BookingStatus')

@section('title', 'Dashboard')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Xin chào, <strong>{{ auth()->user()->name }}</strong>! Tổng quan hệ thống Classic Cut.
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Tổng doanh thu tháng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Doanh thu tháng</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($overview['revenue']['total'], 0, ',', '.') }}₫</p>
                    @if ($overview['revenue']['change'] != 0)
                        <span class="text-xs {{ $overview['revenue']['change'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $overview['revenue']['change'] > 0 ? '+' : '' }}{{ $overview['revenue']['change'] }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Booking hôm nay --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking hôm nay</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $todayBookings }}</p>
                </div>
            </div>
        </div>

        {{-- Chờ xác nhận --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Chờ xác nhận</p>
                    <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingBookings }}</p>
                </div>
            </div>
        </div>

        {{-- Tổng khách hàng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Khách hàng</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>

        {{-- Thợ cắt --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Thợ cắt</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $totalBarbers }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid 2 cột: Biểu đồ + Top thợ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Biểu đồ doanh thu 7 ngày --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">Doanh thu 7 ngày gần nhất</h3>
                <a href="{{ route('admin.reports.index') }}"
                   class="text-sm text-brand-600 dark:text-brand-400 hover:underline">Xem báo cáo →</a>
            </div>
            <div style="position: relative; height: 200px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Top thợ cắt --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">Top thợ cắt tháng này</h3>
            </div>

            @if (count($topBarbers) > 0)
                <div class="space-y-4">
                    @foreach ($topBarbers as $index => $barber)
                        <div class="flex items-center gap-3">
                            {{-- Rank --}}
                            @php
                                $rankColors = ['bg-yellow-400 text-yellow-900', 'bg-gray-300 text-gray-700', 'bg-amber-600 text-white'];
                            @endphp
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $rankColors[$index] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $index + 1 }}
                            </div>

                            {{-- Avatar --}}
                            @if ($barber['avatar'])
                                <img src="{{ Storage::url($barber['avatar']) }}" class="w-9 h-9 rounded-full object-cover" alt="{{ $barber['name'] }}">
                            @else
                                <div class="w-9 h-9 bg-brand-100 dark:bg-brand-900 rounded-full flex items-center justify-center text-brand-700 dark:text-brand-300 font-semibold text-sm">
                                    {{ strtoupper(substr($barber['name'], 0, 1)) }}
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $barber['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $barber['bookings'] }} booking · ⭐ {{ number_format($barber['rating'], 1) }}</p>
                            </div>

                            {{-- Revenue --}}
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400 whitespace-nowrap">
                                {{ number_format($barber['revenue'], 0, ',', '.') }}₫
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Chưa có dữ liệu tháng này.</p>
            @endif
        </div>
    </div>

    {{-- Booking gần nhất --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Booking gần nhất</h3>
            <a href="{{ route('admin.bookings.index') }}"
               class="text-sm text-brand-600 dark:text-brand-400 hover:underline">Xem tất cả →</a>
        </div>

        @if ($recentBookings->count() > 0)
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Thợ cắt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dịch vụ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ngày</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Giá</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($recentBookings as $booking)
                        <tr>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    @if ($booking->customer?->avatar)
                                        <img src="{{ Storage::url($booking->customer->avatar) }}" class="w-7 h-7 rounded-full object-cover" alt="">
                                    @else
                                        <div class="w-7 h-7 bg-brand-100 dark:bg-brand-900 rounded-full flex items-center justify-center text-brand-700 dark:text-brand-300 font-semibold text-xs">
                                            {{ strtoupper(substr($booking->customer?->name ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-sm text-gray-900 dark:text-gray-300">{{ $booking->customer?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $booking->barber?->user?->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $booking->services->pluck('name')->join(', ') ?: '—' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m') }}
                                <span class="text-xs">{{ $booking->start_time }}</span>
                            </td>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
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
                Chưa có booking nào.
            </div>
        @endif
    </div>

    {{-- Quick Links --}}
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.bookings.index') }}"
           class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:border-brand-300 dark:hover:border-brand-600 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Quản lý Booking</span>
            </div>
        </a>

        <a href="{{ route('admin.barbers.index') }}"
           class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:border-brand-300 dark:hover:border-brand-600 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Quản lý Thợ cắt</span>
            </div>
        </a>

        <a href="{{ route('admin.services.index') }}"
           class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:border-brand-300 dark:hover:border-brand-600 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Quản lý Dịch vụ</span>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:border-brand-300 dark:hover:border-brand-600 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Quản lý Người dùng</span>
            </div>
        </a>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.15)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.01)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($revenueChart['labels']),
                    datasets: [{
                        label: 'Doanh thu',
                        data: @json($revenueChart['data']),
                        borderColor: '#4F46E5',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4F46E5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + '₫'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => v >= 1e6 ? (v / 1e6).toFixed(1) + 'tr' : v >= 1e3 ? (v / 1e3).toFixed(0) + 'k' : v
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endpush