@extends('layouts.tailadmin')

@section('title', 'Báo cáo tổng quan')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Báo cáo tổng quan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Thống kê tháng {{ $overview['month'] }}
                @if($branchId)
                    · <span class="text-green-600 dark:text-green-400 font-medium">{{ $branches->firstWhere('id', $branchId)?->name }}</span>
                @endif
            </p>
        </div>
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <select name="branch_id" onchange="this.form.submit()"
                class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-[180px]">
                <option value="">Tất cả chi nhánh</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Tabs Alpine --}}
    <div x-data="{ currentTab: 'service' }" @change-tab.window="currentTab = $event.detail; document.getElementById('reportType').value = currentTab; updateTitle(); fetchChartData();">
        <div class="flex gap-6 mb-6 border-b border-gray-200 dark:border-gray-700">
            <button @click="$dispatch('change-tab', 'service')" 
                :class="currentTab === 'service' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" 
                class="pb-3 border-b-2 font-semibold text-sm transition-colors">Dịch vụ & Booking</button>
            <button @click="$dispatch('change-tab', 'product')" 
                :class="currentTab === 'product' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" 
                class="pb-3 border-b-2 font-semibold text-sm transition-colors">Sản phẩm E-commerce</button>
        </div>

        <input type="hidden" id="reportType" value="service">

        {{-- Tab Dịch vụ Stats --}}
        <div x-show="currentTab === 'service'">
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
        </div>

        {{-- Tab Sản phẩm Stats --}}
        <div x-show="currentTab === 'product'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Card: Tổng Đơn Hàng --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        @php $orderChange = $productOverview['orders']['change']; @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $orderChange > 0 ? 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400' : ($orderChange < 0 ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-gray-50 text-gray-500 dark:bg-gray-600/10 dark:text-gray-400') }}">
                            @if ($orderChange > 0)
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3m0 0L3 6m3-3l3 3" /></svg>
                                +{{ $orderChange }}%
                            @elseif ($orderChange < 0)
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v6m0 0l3-3m-3 3L3 6" /></svg>
                                {{ $orderChange }}%
                            @else 0% @endif
                        </span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($productOverview['orders']['total']) }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tổng đơn hàng trong tháng</p>
                </div>

                {{-- Card: Doanh thu Sản phẩm --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-green-50 dark:bg-green-500/10">
                            <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        @php $productRevenueChange = $productOverview['revenue']['change']; @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $productRevenueChange > 0 ? 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400' : ($productRevenueChange < 0 ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-gray-50 text-gray-500 dark:bg-gray-600/10 dark:text-gray-400') }}">
                            @if ($productRevenueChange > 0)
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3m0 0L3 6m3-3l3 3" /></svg>
                                +{{ $productRevenueChange }}%
                            @elseif ($productRevenueChange < 0)
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v6m0 0l3-3m-3 3L3 6" /></svg>
                                {{ $productRevenueChange }}%
                            @else 0% @endif
                        </span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($productOverview['revenue']['total'], 0, ',', '.') }} ₫</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Doanh thu sản phẩm</p>
                </div>

                {{-- Card: Sản phẩm đang bán --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10">
                            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($productOverview['products']['total']) }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sản phẩm đang bán</p>
                </div>
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

        <div class="relative" style="height: 220px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Top Thợ & Top Dịch vụ (Dịch vụ Tab) --}}
    <div x-show="currentTab === 'service'" class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

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
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $barber['bookings'] }} booking · ⭐ {{ number_format($barber['rating'], 1) }}
                                    @if($barber['branch'])
                                        · <span class="text-green-500">{{ $barber['branch'] }}</span>
                                    @endif
                                </p>
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

    {{-- Top Sản phẩm (Sản phẩm Tab) --}}
    <div x-show="currentTab === 'product'" x-cloak class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Top sản phẩm bán chạy</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">Tháng này</span>
            </div>

            @if (count($topSellingProducts) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($topSellingProducts as $index => $product)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            {{-- Rank --}}
                            @php
                                $rankColors = ['bg-yellow-400 text-yellow-900', 'bg-gray-300 text-gray-700', 'bg-amber-600 text-white'];
                            @endphp
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $rankColors[$index] ?? 'bg-gray-200 text-gray-600' }}">
                                {{ $index + 1 }}
                            </div>

                            {{-- Image --}}
                            @if ($product['image'])
                                <img src="{{ Storage::url($product['image']) }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-500 flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $product['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá: {{ number_format($product['price'], 0, ',', '.') }}₫</p>
                            </div>

                            {{-- Stats --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $product['sold'] }} đã bán</p>
                                <p class="text-xs font-semibold text-green-600 dark:text-green-400 mt-0.5">{{ number_format($product['revenue'], 0, ',', '.') }}₫</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">Chưa có dữ liệu</p>
            @endif
        </div>
    </div>
    
    </div> {{-- End Tabs Alpine wrapper --}}

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
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
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
        const typeMode = document.getElementById('reportType').value;
        const params = new URLSearchParams({ mode: currentMode, type: typeMode });

        if (currentMode === 'monthly') {
            params.append('month', filterMonth.value);
            params.append('year', filterYear.value);
        } else if (currentMode === 'yearly') {
            params.append('year', filterYear.value);
        }

        try {
            const branchId = '{{ $branchId ?? '' }}';
            if (branchId) params.append('branch_id', branchId);
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
        const typeMode = document.getElementById('reportType').value;
        const typeStr = typeMode === 'product' ? 'Doanh thu sản phẩm' : 'Doanh thu dịch vụ';

        if (currentMode === 'recent') {
            chartTitle.textContent = `${typeStr} 30 ngày gần nhất`;
        } else if (currentMode === 'monthly') {
            chartTitle.textContent = `${typeStr} ${MONTH_NAMES[filterMonth.value]} năm ${filterYear.value}`;
        } else {
            chartTitle.textContent = `${typeStr} năm ${filterYear.value}`;
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
