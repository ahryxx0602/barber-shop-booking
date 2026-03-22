@extends('layouts.tailadmin')

@section('page', 'adminBookings')
@section('title', 'Quan ly Booking')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quan ly Booking</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Xem lich hen cua tung tho theo tuan</p>
    </div>

    {{-- Barber Selector + Week Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        {{-- Barber Tabs --}}
        <div class="flex flex-wrap gap-2">
            @foreach($barbers as $barber)
                <a href="{{ route('admin.bookings.index', ['barber_id' => $barber->id, 'week' => request('week')]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border-2
                        {{ $selectedBarberId == $barber->id
                            ? 'border-blue-600 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-500'
                            : 'border-transparent bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $selectedBarberId == $barber->id ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">
                        {{ mb_substr($barber->user->name, 0, 1) }}
                    </span>
                    {{ $barber->user->name }}
                </a>
            @endforeach
        </div>

        {{-- Week Navigation --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('admin.bookings.index', ['barber_id' => $selectedBarberId, 'week' => $prevWeek]) }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="{{ route('admin.bookings.index', ['barber_id' => $selectedBarberId]) }}"
                class="inline-flex items-center px-4 h-10 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors">
                Tuan nay
            </a>
            <a href="{{ route('admin.bookings.index', ['barber_id' => $selectedBarberId, 'week' => $nextWeek]) }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    @if($selectedBarber)
        {{-- Selected barber info + week range --}}
        <div class="mb-4 flex items-center gap-3">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $selectedBarber->user->name }}</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}
            </span>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tong lich hen</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-yellow-200 dark:border-yellow-800 p-4">
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cho xac nhan</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-200 dark:border-green-800 p-4">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hoan thanh</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-200 dark:border-green-800 p-4">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['revenue'], 0, ',', '.') }}d</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Doanh thu tuan</p>
            </div>
        </div>

        {{-- Days --}}
        <div class="space-y-6">
            @foreach($days as $dateStr => $day)
                <div>
                    {{-- Day Header --}}
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-sm font-semibold uppercase tracking-wider
                            {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $day['label'] }}
                        </h3>
                        @if($day['isToday'])
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                Hom nay
                            </span>
                        @endif
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $day['bookings']->count() }} lich hen
                        </span>
                        <div class="flex-1 border-t border-gray-200 dark:border-gray-700"></div>
                    </div>

                    {{-- Bookings --}}
                    @if($day['bookings']->isEmpty())
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-4 text-center">
                            <p class="text-sm text-gray-400 dark:text-gray-500">Khong co lich hen</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($day['bookings'] as $booking)
                                {{-- Admin booking card (view only, no action buttons) --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                        <div class="flex-shrink-0 w-20 text-center">
                                            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</p>
                                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-semibold text-gray-800 dark:text-white">{{ $booking->customer->name }}</span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                                    @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                                    @elseif($booking->status === 'in_progress') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                                                    @elseif($booking->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                                    @endif">
                                                    @if($booking->status === 'pending') Cho xac nhan
                                                    @elseif($booking->status === 'confirmed') Da xac nhan
                                                    @elseif($booking->status === 'in_progress') Dang thuc hien
                                                    @elseif($booking->status === 'completed') Hoan thanh
                                                    @elseif($booking->status === 'cancelled') Da huy
                                                    @endif
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $booking->services->pluck('name')->join(', ') }}
                                            </p>
                                            @if($booking->customer->phone)
                                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                        {{ $booking->customer->phone }}
                                                    </span>
                                                </p>
                                            @endif
                                            @if($booking->note)
                                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1 italic">"{{ $booking->note }}"</p>
                                            @endif
                                            @if($booking->cancel_reason)
                                                <p class="text-sm text-red-500 mt-1">Ly do huy: {{ $booking->cancel_reason }}</p>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($booking->total_price, 0, ',', '.') }}d</p>
                                            <p class="text-xs text-gray-400 font-mono">{{ $booking->booking_code }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400">Chua co tho nao trong he thong.</p>
        </div>
    @endif
@endsection
