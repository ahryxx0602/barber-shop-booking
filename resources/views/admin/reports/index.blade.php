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

    {{-- Biểu đồ doanh thu --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
        {{-- Header + Tabs --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h3 id="chartTitle" class="text-lg font-semibold text-gray-800 dark:text-white">Doanh thu 30 ngày gần nhất</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Chỉ tính booking đã xác nhận, đang phục vụ và hoàn thành</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                {{-- Tab buttons --}}
                <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-600 p-0.5 bg-gray-50 dark:bg-gray-700/50">
                    <button type="button" data-mode="recent"
                        class="chart-tab active px-3 py-1.5 text-xs font-medium rounded-md transition-all">
                        30 ngày
                    </button>
                    <button type="button" data-mode="monthly"
                        class="chart-tab px-3 py-1.5 text-xs font-medium rounded-md transition-all">
                        Theo tháng
                    </button>
                    <button type="button" data-mode="yearly"
                        class="chart-tab px-3 py-1.5 text-xs font-medium rounded-md transition-all">
                        Theo năm
                    </button>
                </div>

                {{-- Dropdown tháng (ẩn mặc định) --}}
                <select id="filterMonth" class="hidden text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>

                {{-- Dropdown năm (ẩn mặc định) --}}
                <select id="filterYear" class="hidden text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Loading overlay --}}
        <div id="chartLoading" class="hidden absolute inset-0 bg-white/60 dark:bg-gray-800/60 rounded-xl flex items-center justify-center z-10">
            <svg class="w-8 h-8 text-green-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>

        <div class="relative" style="height: 320px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Top Thợ & Top Dịch vụ --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Thợ theo doanh thu --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Top thợ cắt</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">Theo doanh thu tháng này</span>
            </div>

            @if (count($topBarbers) > 0)
                <div class="space-y-3">
                    @foreach ($topBarbers as $index => $barber)
                        <div class="flex items-center gap-3 p-3 rounded-lg {{ $index === 0 ? 'bg-amber-50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }} transition-colors">
                            {{-- Rank badge --}}
                            <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $index === 0 ? 'bg-amber-400 text-white' : ($index === 1 ? 'bg-gray-300 dark:bg-gray-500 text-white' : ($index === 2 ? 'bg-orange-400 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400')) }}">
                                {{ $index + 1 }}
                            </div>

                            {{-- Avatar --}}
                            @if ($barber['avatar'])
                                <img src="{{ asset('storage/' . $barber['avatar']) }}" alt="{{ $barber['name'] }}"
                                    class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-9 h-9 rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ mb_substr($barber['name'], 0, 1) }}</span>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $barber['name'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $barber['bookings'] }} booking · ⭐ {{ number_format($barber['rating'], 1) }}</p>
                            </div>

                            {{-- Revenue --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ number_format($barber['revenue'], 0, ',', '.') }} ₫</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">Chưa có dữ liệu</p>
            @endif
        </div>

        {{-- Top Dịch vụ theo số lần đặt --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Top dịch vụ</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">Theo số lần đặt tháng này</span>
            </div>

            @if (count($topServices) > 0)
                <div class="space-y-3">
                    @foreach ($topServices as $index => $service)
                        <div class="flex items-center gap-3 p-3 rounded-lg {{ $index === 0 ? 'bg-blue-50 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }} transition-colors">
                            {{-- Rank badge --}}
                            <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $index === 0 ? 'bg-blue-500 text-white' : ($index === 1 ? 'bg-gray-300 dark:bg-gray-500 text-white' : ($index === 2 ? 'bg-orange-400 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400')) }}">
                                {{ $index + 1 }}
                            </div>

                            {{-- Service image --}}
                            @if ($service['image'])
                                <img src="{{ asset('storage/' . $service['image']) }}" alt="{{ $service['name'] }}"
                                    class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $service['name'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($service['price'], 0, ',', '.') }} ₫/lần</p>
                            </div>

                            {{-- Times booked --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $service['times_booked'] }} lần</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($service['revenue'], 0, ',', '.') }} ₫</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">Chưa có dữ liệu</p>
            @endif
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

@push('styles')
<style>
    .chart-tab {
        color: #6b7280;
    }
    .chart-tab:hover:not(.active) {
        color: #374151;
        background: rgba(0,0,0,0.03);
    }
    .chart-tab.active {
        background: #fff;
        color: #059669;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    }
    .dark .chart-tab {
        color: #9ca3af;
    }
    .dark .chart-tab:hover:not(.active) {
        color: #e5e7eb;
        background: rgba(255,255,255,0.05);
    }
    .dark .chart-tab.active {
        background: #374151;
        color: #34d399;
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');
    const chartTitle = document.getElementById('chartTitle');
    const filterMonth = document.getElementById('filterMonth');
    const filterYear = document.getElementById('filterYear');
    const tabs = document.querySelectorAll('.chart-tab');

    let currentChart = null;
    let currentMode = 'recent';

    const MONTH_NAMES = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                         'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];

    // Tạo gradient
    function createGradient() {
        const gradient = ctx.createLinearGradient(0, 0, 0, 320);
        gradient.addColorStop(0, isDark ? 'rgba(34, 197, 94, 0.3)' : 'rgba(34, 197, 94, 0.2)');
        gradient.addColorStop(1, isDark ? 'rgba(34, 197, 94, 0)' : 'rgba(34, 197, 94, 0)');
        return gradient;
    }

    // Render hoặc cập nhật chart
    function renderChart(labels, data, chartType) {
        if (currentChart) {
            currentChart.destroy();
        }

        const isBar = chartType === 'bar';
        currentChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: data,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: isBar
                        ? (isDark ? 'rgba(34, 197, 94, 0.6)' : 'rgba(34, 197, 94, 0.7)')
                        : createGradient(),
                    borderWidth: isBar ? 0 : 2.5,
                    borderRadius: isBar ? 6 : 0,
                    pointRadius: 0,
                    pointHoverRadius: isBar ? 0 : 6,
                    pointHoverBackgroundColor: 'rgb(34, 197, 94)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    fill: !isBar,
                    tension: 0.4,
                    hoverBackgroundColor: isBar ? 'rgb(34, 197, 94)' : undefined,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1f2937' : '#fff',
                        titleColor: isDark ? '#e5e7eb' : '#111827',
                        bodyColor: isDark ? '#d1d5db' : '#374151',
                        borderColor: isDark ? '#374151' : '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' ₫';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: isDark ? '#9ca3af' : '#6b7280',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: isBar ? 12 : 10,
                            font: { size: 11 }
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 0.8)' },
                        ticks: {
                            color: isDark ? '#9ca3af' : '#6b7280',
                            font: { size: 11 },
                            callback: function (value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'tr';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                return value;
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Fetch dữ liệu từ API
    async function fetchChartData() {
        const params = new URLSearchParams({ mode: currentMode });

        if (currentMode === 'monthly') {
            params.append('month', filterMonth.value);
            params.append('year', filterYear.value);
        } else if (currentMode === 'yearly') {
            params.append('year', filterYear.value);
        }

        try {
            const response = await fetch(`{{ route('admin.reports.chartData') }}?${params}`);
            const result = await response.json();
            const chartType = currentMode === 'yearly' ? 'bar' : 'line';
            renderChart(result.labels, result.data, chartType);
        } catch (error) {
            console.error('Chart fetch error:', error);
        }
    }

    // Cập nhật title
    function updateTitle() {
        if (currentMode === 'recent') {
            chartTitle.textContent = 'Doanh thu 30 ngày gần nhất';
        } else if (currentMode === 'monthly') {
            chartTitle.textContent = `Doanh thu ${MONTH_NAMES[filterMonth.value]} năm ${filterYear.value}`;
        } else {
            chartTitle.textContent = `Doanh thu năm ${filterYear.value}`;
        }
    }

    // Cập nhật hiển thị dropdowns
    function updateFilters() {
        filterMonth.classList.toggle('hidden', currentMode !== 'monthly');
        filterYear.classList.toggle('hidden', currentMode === 'recent');
    }

    // Tab click
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentMode = this.dataset.mode;
            updateFilters();
            updateTitle();
            fetchChartData();
        });
    });

    // Dropdown change
    filterMonth.addEventListener('change', () => { updateTitle(); fetchChartData(); });
    filterYear.addEventListener('change', () => { updateTitle(); fetchChartData(); });

    // Render chart ban đầu với dữ liệu có sẵn
    renderChart(@json($dailyRevenue['labels']), @json($dailyRevenue['data']), 'line');
});
</script>
@endpush
