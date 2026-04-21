<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Classic Cut') }} — Thợ</title>
    
    {{-- Favicon Logo --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg fill='%23b08968' viewBox='0 0 48 48' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <span class="font-bold text-gray-800">✂️ Classic Cut</span>
                        <a href="{{ route('barber.dashboard') }}"
                            class="text-sm font-medium {{ request()->routeIs('barber.dashboard') ? 'text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Dashboard</a>
                        <a href="{{ route('barber.schedule.edit') }}"
                            class="text-sm font-medium {{ request()->routeIs('barber.schedule.*') ? 'text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Lịch
                            làm việc</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>