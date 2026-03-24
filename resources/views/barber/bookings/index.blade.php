@use('App\Enums\BookingStatus')

@extends('layouts.tailbarber')

@section('page', 'Booking')
@section('title', 'Tất cả lịch hẹn')

@section('content')
    {{-- Tab chuyển đổi chế độ xem --}}
    <div class="flex items-center gap-1 mb-6 bg-gray-100 dark:bg-gray-800 rounded-lg p-1 w-fit">
        <a href="{{ route('barber.bookings.index', request()->only('week')) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-medium bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Danh sách
        </a>
        <a href="{{ route('barber.bookings.calendar') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Lịch trực quan
        </a>
    </div>

    {{-- Header with week navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tất cả lịch hẹn</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Tuần {{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}
            </p>
        </div>
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <a href="{{ route('barber.bookings.index', ['week' => $prevWeek]) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="{{ route('barber.bookings.index') }}"
                class="inline-flex items-center px-3 h-8 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300 transition-colors">
                Tuần này
            </a>
            <a href="{{ route('barber.bookings.index', ['week' => $nextWeek]) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif

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

                    {{-- Booking rows — compact table style --}}
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($day['bookings'] as $booking)
                            <div class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <div class="flex items-center gap-3">
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

                                    {{-- Action Buttons --}}
                                    @if($booking->status !== BookingStatus::Completed && $booking->status !== BookingStatus::Cancelled)
                                        <div class="flex-shrink-0 flex items-center gap-1">
                                            @if($booking->status === BookingStatus::Pending)
                                                <form method="POST" action="{{ route('barber.bookings.confirm', $booking) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" title="Xác nhận" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-500 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                </form>
                                                <div x-data="{ open: false }" class="relative">
                                                    <button @click="open = !open" type="button" title="Từ chối" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-400 text-red-500 hover:bg-red-600 hover:text-white transition-colors">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" x-transition
                                                        class="absolute z-10 mt-2 right-0 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                                                        <form method="POST" action="{{ route('barber.bookings.reject', $booking) }}">
                                                            @csrf @method('PATCH')
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lý do từ chối</label>
                                                            <textarea name="cancel_reason" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" placeholder="Nhập lý do..."></textarea>
                                                            <button type="submit" class="mt-2 w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                                Xác nhận từ chối
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($booking->status === BookingStatus::Confirmed)
                                                <form method="POST" action="{{ route('barber.bookings.start', $booking) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" title="Bắt đầu phục vụ" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-purple-500 text-purple-600 hover:bg-purple-600 hover:text-white transition-colors">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($booking->status === BookingStatus::InProgress)
                                                <form method="POST" action="{{ route('barber.bookings.complete', $booking) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" title="Hoàn thành" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-green-500 text-green-600 hover:bg-green-600 hover:text-white transition-colors">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Note / Cancel reason --}}
                                @if($booking->note)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-[76px] italic">"{{ $booking->note }}"</p>
                                @endif
                                @if($booking->cancel_reason)
                                    <p class="text-xs text-red-500 mt-1 ml-[76px]">Lý do huỷ: {{ $booking->cancel_reason }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
