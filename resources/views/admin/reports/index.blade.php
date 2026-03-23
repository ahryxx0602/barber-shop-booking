@extends('layouts.tailadmin')

@section('title', 'Báo cáo tổng quan')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Báo cáo tổng quan</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Thống kê tháng {{ $overview['month'] }}</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Card: Tổng Booking --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10">
                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                @php $bookingChange = $overview['bookings']['change']; @endphp
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $bookingChange > 0 ? 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400' : ($bookingChange < 0 ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-gray-50 text-gray-500 dark:bg-gray-600/10 dark:text-gray-400') }}">
                    @if ($bookingChange > 0)
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3m0 0L3 6m3-3l3 3" />
                        </svg>
                        +{{ $bookingChange }}%
                    @elseif ($bookingChange < 0)
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 3v6m0 0l3-3m-3 3L3 6" />
                        </svg>
                        {{ $bookingChange }}%
                    @else
                        0%
                    @endif
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overview['bookings']['total']) }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tổng booking trong tháng</p>
        </div>

        {{-- Card: Doanh thu --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-green-50 dark:bg-green-500/10">
                    <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                @php $revenueChange = $overview['revenue']['change']; @endphp
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $revenueChange > 0 ? 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400' : ($revenueChange < 0 ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-gray-50 text-gray-500 dark:bg-gray-600/10 dark:text-gray-400') }}">
                    @if ($revenueChange > 0)
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3m0 0L3 6m3-3l3 3" />
                        </svg>
                        +{{ $revenueChange }}%
                    @elseif ($revenueChange < 0)
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 3v6m0 0l3-3m-3 3L3 6" />
                        </svg>
                        {{ $revenueChange }}%
                    @else
                        0%
                    @endif
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overview['revenue']['total'], 0, ',', '.') }} ₫</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Doanh thu dự kiến</p>
        </div>

        {{-- Card: Khách hàng mới --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-500/10">
                    <svg class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                @php $customerChange = $overview['newCustomers']['change']; @endphp
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $customerChange > 0 ? 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400' : ($customerChange < 0 ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-gray-50 text-gray-500 dark:bg-gray-600/10 dark:text-gray-400') }}">
                    @if ($customerChange > 0)
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3m0 0L3 6m3-3l3 3" />
                        </svg>
                        +{{ $customerChange }}%
                    @elseif ($customerChange < 0)
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 3v6m0 0l3-3m-3 3L3 6" />
                        </svg>
                        {{ $customerChange }}%
                    @else
                        0%
                    @endif
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overview['newCustomers']['total']) }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Khách hàng mới trong tháng</p>
        </div>

    </div>

    {{-- Ghi chú --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            <strong class="text-gray-700 dark:text-gray-300">Ghi chú:</strong>
            Doanh thu dự kiến được tính từ các booking có trạng thái
            <span class="font-medium text-blue-600 dark:text-blue-400">Đã xác nhận</span>,
            <span class="font-medium text-indigo-600 dark:text-indigo-400">Đang phục vụ</span>, và
            <span class="font-medium text-green-600 dark:text-green-400">Hoàn thành</span>.
            So sánh % là so với cùng kỳ tháng trước.
        </p>
    </div>
@endsection
