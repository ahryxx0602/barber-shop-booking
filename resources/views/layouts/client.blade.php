<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Classic Cut') - {{ config('app.name', 'Classic Cut') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-bg-light text-warm-gray antialiased font-display">
    {{-- Navigation --}}
    <nav class="fixed top-0 w-full z-50 transition-all duration-300 bg-bg-light/95 backdrop-blur-sm border-b border-warm-gray/10 h-[72px] flex flex-col justify-center"
        id="main-nav">
        <div class="px-6 md:px-12 lg:px-24 flex justify-between items-center w-full max-w-[1600px] mx-auto">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="size-4 text-primary">
                    <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                            fill="currentColor"></path>
                    </svg>
                </div>
                <span class="text-[15px] font-bold tracking-[3px] text-warm-gray uppercase">Classic Cut</span>
            </a>

            {{-- Desktop Links --}}
            <div class="hidden md:flex items-center gap-10">
                <a class="text-warm-gray-light hover:text-primary transition-colors text-[11px] font-semibold tracking-[2.5px] uppercase"
                    href="{{ url('/') }}#services">Dịch vụ</a>
                <a class="text-warm-gray-light hover:text-primary transition-colors text-[11px] font-semibold tracking-[2.5px] uppercase"
                    href="{{ url('/') }}#story">Câu chuyện</a>
                <a class="text-warm-gray-light hover:text-primary transition-colors text-[11px] font-semibold tracking-[2.5px] uppercase"
                    href="{{ route('client.barbers.index') }}">Thợ cắt</a>
                @auth
                    <a class="text-warm-gray-light hover:text-primary transition-colors text-[11px] font-semibold tracking-[2.5px] uppercase"
                        href="{{ route('client.profile.show') }}">Tài khoản</a>
                @else
                    <a class="text-warm-gray-light hover:text-primary transition-colors text-[11px] font-semibold tracking-[2.5px] uppercase"
                        href="{{ route('login') }}">Đăng nhập</a>
                @endauth
                <a href="{{ route('client.booking.create') }}"
                    class="flex items-center justify-center h-[44px] px-6 bg-primary text-white text-[10px] font-bold uppercase tracking-[2.5px] hover:bg-warm-gray transition-colors duration-300">Đặt
                    lịch</a>
            </div>

            {{-- Mobile Menu --}}
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-warm-gray">
                    <span class="material-symbols-outlined" x-text="open ? 'close' : 'menu'">menu</span>
                </button>
                <div x-show="open" x-cloak @click.away="open = false" x-transition
                    class="absolute top-[72px] left-0 right-0 bg-bg-light border-b border-warm-gray/10 px-6 py-6 flex flex-col gap-4">
                    <a class="text-warm-gray-light hover:text-primary text-[11px] font-semibold tracking-[2.5px] uppercase"
                        href="{{ url('/') }}#services">Dịch vụ</a>
                    <a class="text-warm-gray-light hover:text-primary text-[11px] font-semibold tracking-[2.5px] uppercase"
                        href="{{ url('/') }}#story">Câu chuyện</a>
                    <a class="text-warm-gray-light hover:text-primary text-[11px] font-semibold tracking-[2.5px] uppercase"
                        href="{{ route('client.barbers.index') }}">Thợ cắt</a>
                    @auth
                        <a class="text-warm-gray-light hover:text-primary text-[11px] font-semibold tracking-[2.5px] uppercase"
                            href="{{ route('client.profile.show') }}">Tài khoản</a>
                    @else
                        <a class="text-warm-gray-light hover:text-primary text-[11px] font-semibold tracking-[2.5px] uppercase"
                            href="{{ route('login') }}">Đăng nhập</a>
                    @endauth
                    <a href="{{ route('client.booking.create') }}"
                        class="flex items-center justify-center h-[44px] bg-primary text-white text-[10px] font-bold uppercase tracking-[2.5px] hover:bg-warm-gray transition-colors">Dat
                        lich</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="pt-[72px]">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-warm-gray text-bg-light/80 noise-bg">
        <div class="max-w-[1400px] mx-auto px-6 md:px-12 lg:px-24 py-16 md:py-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-16 mb-16">
                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="size-4 text-primary">
                            <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                                    fill="currentColor"></path>
                            </svg>
                        </div>
                        <span class="text-[14px] font-bold tracking-[3px] text-bg-light uppercase">Classic Cut</span>
                    </div>
                    <p class="text-sm leading-relaxed text-bg-light/60 max-w-xs">Tiệm cắt tóc nam phong cách vintage.
                        Nơi nghệ thuật và phong cách hội tụ.</p>
                </div>
                {{-- Quick Links --}}
                <div>
                    <h4 class="text-[10px] font-bold tracking-[3px] uppercase text-bg-light/40 mb-6">Liên kết</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/') }}#services" class="text-sm hover:text-primary transition-colors">Dịch
                                vụ</a></li>
                        <li><a href="{{ route('client.barbers.index') }}"
                                class="text-sm hover:text-primary transition-colors">Đội ngũ thợ cắt</a></li>
                        <li><a href="{{ route('client.booking.create') }}"
                                class="text-sm hover:text-primary transition-colors">Đặt lịch ngay</a></li>
                    </ul>
                </div>
                {{-- Hours --}}
                <div>
                    <h4 class="text-[10px] font-bold tracking-[3px] uppercase text-bg-light/40 mb-6">Giờ làm việc</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between"><span>Thu 2 - Thu 6</span><span>9:00 - 19:00</span></li>
                        <li class="flex justify-between"><span>Thu 7</span><span>9:00 - 17:00</span></li>
                        <li class="flex justify-between"><span>Chủ Nhật</span><span>Nghỉ</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-bg-light/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-bg-light/40">&copy; {{ date('Y') }} Classic Cut Barbershop. Đồ án tốt nghiệp -
                    Phan Văn Thành.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>