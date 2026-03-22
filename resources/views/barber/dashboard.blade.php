@extends('layouts.tailbarber')

@section('page', 'barberDashboard')
@section('title', 'Dashboard Tho')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Lich hen hom nay</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Xin chao, <strong>{{ auth()->user()->name }}</strong>! Quan ly lich hen cua ban tai day.</p>
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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tong lich hen</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-yellow-200 dark:border-yellow-800 p-4">
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cho xac nhan</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-blue-200 dark:border-blue-800 p-4">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['in_progress'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dang thuc hien</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-200 dark:border-green-800 p-4">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['revenue'], 0, ',', '.') }}d</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Doanh thu</p>
        </div>
    </div>

    {{-- Booking List --}}
    @if($bookings->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Khong co lich hen nao trong ngay nay.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                @include('barber.partials.booking-card', ['booking' => $booking])
            @endforeach
        </div>
    @endif
@endsection
