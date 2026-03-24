@use('App\Enums\BookingStatus')

@extends('layouts.tailadmin')

@section('page', 'adminBookings')
@section('title', 'Quản lý Booking')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý Booking</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Xem lịch hẹn của từng thợ theo tuần</p>
    </div>

    {{-- Branch Filter + Barber Selector + Week Navigation --}}
    <div class="flex flex-col gap-4 mb-6">
        {{-- Row 1: Branch filter + Week nav --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex items-center gap-2">
                <select name="branch_id" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[180px]">
                    <option value="">Tất cả chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </form>

            {{-- Week Navigation --}}
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <a href="{{ route('admin.bookings.index', ['barber_id' => $selectedBarberId, 'branch_id' => $selectedBranchId, 'week' => $prevWeek]) }}"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <a href="{{ route('admin.bookings.index', ['barber_id' => $selectedBarberId, 'branch_id' => $selectedBranchId]) }}"
                    class="inline-flex items-center px-3 h-8 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300 transition-colors">
                    Tuần này
                </a>
                <a href="{{ route('admin.bookings.index', ['barber_id' => $selectedBarberId, 'branch_id' => $selectedBranchId, 'week' => $nextWeek]) }}"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Row 2: Barber tabs grouped by branch --}}
        <div class="space-y-2">
            @foreach($barbersByBranch as $branchName => $branchBarbers)
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 w-full sm:w-auto sm:min-w-[100px] flex-shrink-0">{{ $branchName }}</span>
                    @foreach($branchBarbers as $barber)
                        <a href="{{ route('admin.bookings.index', ['barber_id' => $barber->id, 'branch_id' => $selectedBranchId, 'week' => request('week')]) }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors border
                                {{ $selectedBarberId == $barber->id
                                    ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-500'
                                    : 'border-gray-200 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-700' }}">
                            @if($barber->user->avatar)
                                <img src="{{ asset('storage/' . $barber->user->avatar) }}" class="w-5 h-5 rounded-full object-cover">
                            @else
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold
                                    {{ $selectedBarberId == $barber->id ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">
                                    {{ mb_substr($barber->user->name, 0, 1) }}
                                </span>
                            @endif
                            {{ $barber->user->name }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    @if($selectedBarber)
        {{-- Stats row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3">
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tổng</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-yellow-200 dark:border-yellow-800 px-4 py-3">
                <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Chờ duyệt</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-green-200 dark:border-green-800 px-4 py-3">
                <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Hoàn thành</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-blue-800 px-4 py-3">
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Doanh thu</p>
            </div>
        </div>

        {{-- Week label --}}
        <div class="flex items-center gap-2 mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $selectedBarber->user->name }}</h3>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}</span>
        </div>

        {{-- Days --}}
        <div class="space-y-3">
            @foreach($days as $dateStr => $day)
                @if($day['bookings']->isEmpty())
                    {{-- Ngày trống: compact 1 dòng --}}
                    <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-dashed border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-semibold uppercase tracking-wider w-28
                            {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $day['label'] }}
                            @if($day['isToday'])
                                <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Nay</span>
                            @endif
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">— Không có lịch hẹn</span>
                    </div>
                @else
                    {{-- Ngày có booking --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border {{ $day['isToday'] ? 'border-blue-300 dark:border-blue-600 ring-1 ring-blue-100 dark:ring-blue-900/30' : 'border-gray-200 dark:border-gray-700' }} overflow-hidden">
                        {{-- Day Header --}}
                        <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs font-semibold uppercase tracking-wider
                                {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $day['label'] }}
                            </span>
                            @if($day['isToday'])
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 font-medium">Hôm nay</span>
                            @endif
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $day['bookings']->count() }} lịch hẹn</span>
                        </div>

                        {{-- Booking rows —— compact table style --}}
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($day['bookings'] as $booking)
                                <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    {{-- Time --}}
                                    <div class="flex-shrink-0 w-16 text-center">
                                        <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</p>
                                        <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                                    </div>

                                    {{-- Divider --}}
                                    <div class="w-px h-8 bg-gray-200 dark:bg-gray-600 flex-shrink-0"></div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $booking->customer->name }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium leading-none
                                                @if($booking->status === BookingStatus::Pending) bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                                @elseif($booking->status === BookingStatus::Confirmed) bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300
                                                @elseif($booking->status === BookingStatus::InProgress) bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300
                                                @elseif($booking->status === BookingStatus::Completed) bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                                @elseif($booking->status === BookingStatus::Cancelled) bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300
                                                @endif">
                                                {{ $booking->status->label() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $booking->services->pluck('name')->join(', ') }}</p>
                                    </div>

                                    {{-- Phone --}}
                                    @if($booking->customer->phone)
                                        <div class="hidden sm:block flex-shrink-0">
                                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $booking->customer->phone }}</span>
                                        </div>
                                    @endif

                                    {{-- Price + Code --}}
                                    <div class="flex-shrink-0 text-right">
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ number_format($booking->total_price, 0, ',', '.') }}đ</p>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $booking->booking_code }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400">Chưa có thợ nào trong hệ thống.</p>
        </div>
    @endif
@endsection
