@extends('layouts.tailbarber')

@section('page', 'Booking')
@section('title', 'Tat ca lich hen')

@section('content')
    {{-- Header with week navigation --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tat ca lich hen</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Tuan {{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('barber.bookings.index', ['week' => $prevWeek]) }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="{{ route('barber.bookings.index') }}"
                class="inline-flex items-center px-4 h-10 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors">
                Tuan nay
            </a>
            <a href="{{ route('barber.bookings.index', ['week' => $nextWeek]) }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Week Stats --}}
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

                {{-- Bookings for this day --}}
                @if($day['bookings']->isEmpty())
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-4 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500">Khong co lich hen</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($day['bookings'] as $booking)
                            @include('barber.partials.booking-card', ['booking' => $booking])
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endsection
