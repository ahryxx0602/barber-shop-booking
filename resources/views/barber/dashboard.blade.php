@extends('layouts.tailbarber')

@section('page', 'barberDashboard')
@section('title', 'Dashboard Thợ')

@section('content')
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Xin chào, <strong>{{ auth()->user()->name }}</strong>! Tổng quan hoạt động của bạn.</p>
        </div>
        {{-- Date Picker --}}
        <form method="GET" action="{{ route('barber.dashboard') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                onchange="this.form.submit()">
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ========== THỐNG KÊ CÁ NHÂN (THÁNG) ========== --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        {{-- Doanh thu tháng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Doanh thu tháng</span>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                    {{ $personalStats['revenue_change'] >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                    {{ $personalStats['revenue_change'] >= 0 ? '+' : '' }}{{ $personalStats['revenue_change'] }}%
                </span>
            </div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($personalStats['monthly_revenue'], 0, ',', '.') }}đ</p>
        </div>

        {{-- Booking tháng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking tháng</span>
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $personalStats['monthly_bookings'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Hoàn thành: {{ $personalStats['completed_count'] }}</p>
        </div>

        {{-- Tỷ lệ hoàn thành --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tỷ lệ HT</span>
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $personalStats['completion_rate'] }}%</p>
            {{-- Mini progress bar --}}
            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min($personalStats['completion_rate'], 100) }}%"></div>
            </div>
        </div>

        {{-- Rating --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Đánh giá</span>
                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($personalStats['rating'], 1) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $personalStats['total_reviews'] }} đánh giá</p>
        </div>

        {{-- Hoa hồng tháng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-emerald-200 dark:border-emerald-800 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Hoa hồng tháng</span>
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($personalStats['monthly_commission'], 0, ',', '.') }}đ</p>
            <p class="text-xs text-gray-400 mt-1">Tỷ lệ: {{ $personalStats['commission_rate'] }}% · {{ $personalStats['commission_count'] }} booking</p>
        </div>
    </div>

    {{-- ========== BIỂU ĐỒ DOANH THU 7 NGÀY + BOOKING SẮP TỚI ========== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Biểu đồ 7 ngày --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Doanh thu 7 ngày gần nhất</h3>
            <div class="h-48">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Sidebar: Booking sắp tới + Ngày nghỉ --}}
        <div class="space-y-4">
            {{-- Booking sắp tới --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Sắp tới hôm nay
                </h3>
                @if($upcomingBookings->isEmpty())
                    <p class="text-xs text-gray-400 dark:text-gray-500">Không có lịch hẹn sắp tới.</p>
                @else
                    <div class="space-y-2">
                        @foreach($upcomingBookings as $upcoming)
                            <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ \Carbon\Carbon::parse($upcoming->start_time)->format('H:i') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $upcoming->customer->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $upcoming->services->pluck('name')->join(', ') }}</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                    {{ $upcoming->status === \App\Enums\BookingStatus::Pending ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                    {{ $upcoming->status->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Ngày nghỉ sắp tới --}}
            @if($upcomingLeaves->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-amber-200 dark:border-amber-800 p-4">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        Ngày nghỉ sắp tới
                    </h3>
                    <div class="space-y-2">
                        @foreach($upcomingLeaves as $leave)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-xs font-bold text-amber-600 dark:text-amber-400">
                                    {{ $leave->leave_date->format('d') }}
                                </span>
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $leave->leave_date->locale('vi')->isoFormat('ddd, DD/MM') }}</p>
                                    <p class="text-xs text-gray-400">{{ $leave->type === 'full_day' ? 'Cả ngày' : \Carbon\Carbon::parse($leave->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($leave->end_time)->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ========== LỌC LỊCH HẸN THEO NGÀY (cũ) ========== --}}
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Lịch hẹn ngày {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
    </div>

    {{-- Stats Cards ngày --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tổng lịch hẹn</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-yellow-200 dark:border-yellow-800 p-4">
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Chờ xác nhận</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-blue-200 dark:border-blue-800 p-4">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['in_progress'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Đang thực hiện</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-200 dark:border-green-800 p-4">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Doanh thu ngày</p>
        </div>
    </div>

    {{-- Booking List --}}
    @if($bookings->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Không có lịch hẹn nào trong ngày này.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                @include('barber.partials.booking-card', ['booking' => $booking])
            @endforeach
        </div>
    @endif
@endsection

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const isDark = document.documentElement.classList.contains('dark');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($personalStats['chart_labels']),
                datasets: [{
                    label: 'Doanh thu',
                    data: @json($personalStats['chart_data']),
                    borderColor: '#3b82f6',
                    backgroundColor: (context) => {
                        const chart = context.chart;
                        const {ctx: c, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                let val = ctx.parsed.y;
                                if (val >= 1e6) return (val / 1e6).toFixed(1) + ' tr';
                                if (val >= 1e3) return (val / 1e3).toFixed(0) + 'k';
                                return val.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: isDark ? '#9ca3af' : '#6b7280',
                            font: { size: 11 }
                        }
                    },
                    y: {
                        grid: {
                            color: isDark ? 'rgba(75,85,99,0.3)' : 'rgba(229,231,235,0.8)',
                        },
                        ticks: {
                            color: isDark ? '#9ca3af' : '#6b7280',
                            font: { size: 11 },
                            callback: function(val) {
                                if (val >= 1e6) return (val / 1e6).toFixed(0) + 'tr';
                                if (val >= 1e3) return (val / 1e3).toFixed(0) + 'k';
                                return val;
                            }
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    });
</script>
@endpush
