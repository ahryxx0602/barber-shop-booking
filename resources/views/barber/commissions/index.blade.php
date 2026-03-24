@extends('layouts.tailbarber')

@section('page', 'barberCommissions')
@section('title', 'Hoa hồng của tôi')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Hoa hồng của tôi</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Tỷ lệ hoa hồng hiện tại: <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $barber->commission_rate }}%</span>
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        {{-- Tổng hoa hồng --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-emerald-200 dark:border-emerald-800 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tổng hoa hồng</span>
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats->total_commission, 0, ',', '.') }}đ</p>
        </div>

        {{-- Doanh thu booking --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Doanh thu</span>
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($stats->total_booking_amount, 0, ',', '.') }}đ</p>
        </div>

        {{-- Số booking --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking tính HH</span>
                <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($stats->total_records) }}</p>
        </div>

        {{-- Tỷ lệ thực tế --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tỷ lệ TB</span>
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
            </div>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($stats->avg_rate, 1) }}%</p>
        </div>
    </div>

    {{-- Biểu đồ 6 tháng + Bộ lọc --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Biểu đồ --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Hoa hồng 6 tháng gần nhất</h3>
            <div class="h-48">
                <canvas id="commissionChart"></canvas>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Lọc theo thời gian</h3>
            <form method="GET" action="{{ route('barber.commissions.index') }}" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Từ ngày</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                        bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Đến ngày</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                        bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm py-2.5 px-4 rounded-lg transition-colors">
                    Áp dụng bộ lọc
                </button>
            </form>
        </div>
    </div>

    {{-- Bảng lịch sử chi tiết --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Chi tiết hoa hồng</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 font-medium">Thời gian</th>
                        <th class="pb-3 font-medium">Mã booking</th>
                        <th class="pb-3 font-medium">Khách hàng</th>
                        <th class="pb-3 font-medium">Dịch vụ</th>
                        <th class="pb-3 font-medium text-right">Giá trị</th>
                        <th class="pb-3 font-medium text-center">Tỷ lệ</th>
                        <th class="pb-3 font-medium text-right">Hoa hồng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($history as $commission)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 text-gray-500 dark:text-gray-400">
                                {{ $commission->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3">
                                <span class="font-mono text-xs text-blue-600 dark:text-blue-400">
                                    {{ $commission->booking->booking_code ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 text-gray-700 dark:text-gray-300">
                                {{ $commission->booking->customer->name ?? '-' }}
                            </td>
                            <td class="py-3 text-gray-500 dark:text-gray-400">
                                <span class="text-xs">
                                    {{ $commission->booking->services->pluck('name')->join(', ') }}
                                </span>
                            </td>
                            <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                {{ number_format($commission->booking_amount, 0, ',', '.') }}đ
                            </td>
                            <td class="py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    {{ $commission->commission_rate }}%
                                </span>
                            </td>
                            <td class="py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($commission->commission_amount, 0, ',', '.') }}đ
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                Chưa có hoa hồng nào trong khoảng thời gian này
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if ($history->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $history->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Ghi chú --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            <strong class="text-gray-700 dark:text-gray-300">Ghi chú:</strong>
            Hoa hồng được tự động tính khi lịch hẹn hoàn thành.
            Tỷ lệ hoa hồng do quản trị viên thiết lập (hiện tại: <strong class="text-emerald-600">{{ $barber->commission_rate }}%</strong>).
        </p>
    </div>
@endsection

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('commissionChart');
        if (!ctx) return;

        const isDark = document.documentElement.classList.contains('dark');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Hoa hồng',
                    data: @json($chartData),
                    backgroundColor: isDark ? 'rgba(52, 211, 153, 0.6)' : 'rgba(16, 185, 129, 0.7)',
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgb(16, 185, 129)',
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
                                return new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + 'đ';
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
                        beginAtZero: true,
                        grid: { color: isDark ? 'rgba(75,85,99,0.3)' : 'rgba(229,231,235,0.8)' },
                        ticks: {
                            color: isDark ? '#9ca3af' : '#6b7280',
                            font: { size: 11 },
                            callback: function(val) {
                                if (val >= 1e6) return (val / 1e6).toFixed(1) + 'tr';
                                if (val >= 1e3) return (val / 1e3).toFixed(0) + 'k';
                                return val;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
