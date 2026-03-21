@extends('layouts.client')

@section('title', 'Xac nhan dat lich')

@section('content')
<section class="bg-bg-light min-h-screen flex items-center justify-center p-4 py-20">
    <div class="w-full max-w-[600px] bg-white border border-muted/20 shadow-sm overflow-hidden">
        <div class="pt-12 pb-10 px-6 sm:px-12 flex flex-col items-center">
            {{-- Success Icon --}}
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-primary text-3xl fill">check_circle</span>
            </div>

            {{-- Headline --}}
            <h1 class="text-warm-gray text-[28px] md:text-[32px] font-bold leading-tight text-center mb-2 font-display">
                Dat lich thanh cong!
            </h1>
            <p class="text-muted text-sm mb-8">Ma dat lich: <span class="font-bold text-warm-gray">{{ $booking->booking_code }}</span></p>

            {{-- Details Box --}}
            <div class="w-full bg-surface/40 border border-muted/10 p-6 mb-8">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-muted/10">
                        <p class="text-muted text-sm font-medium uppercase tracking-wider">Ngay</p>
                        <p class="text-warm-gray text-base font-semibold text-right">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d/m/Y') }}
                        </p>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-muted/10">
                        <p class="text-muted text-sm font-medium uppercase tracking-wider">Gio</p>
                        <p class="text-warm-gray text-base font-semibold text-right">
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        </p>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-muted/10">
                        <p class="text-muted text-sm font-medium uppercase tracking-wider">Tho cat</p>
                        <p class="text-warm-gray text-base font-semibold text-right">{{ $booking->barber->user->name }}</p>
                    </div>
                    <div class="flex justify-between items-start py-2 border-b border-muted/10">
                        <p class="text-muted text-sm font-medium uppercase tracking-wider">Dich vu</p>
                        <div class="text-right">
                            @foreach($booking->services as $service)
                                <p class="text-warm-gray text-sm font-medium">{{ $service->name }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <p class="text-muted text-sm font-medium uppercase tracking-wider">Tong cong</p>
                        <p class="text-primary text-lg font-bold text-right">{{ number_format($booking->total_price, 0, ',', '.') }}d</p>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="w-full bg-warning-50 border border-warning-500/20 p-4 mb-8 text-center">
                <p class="text-sm text-warm-gray">
                    <span class="material-symbols-outlined text-warning-500 text-base align-middle mr-1">schedule</span>
                    Trang thai: <span class="font-bold">Dang cho xac nhan</span>
                </p>
                <p class="text-xs text-muted mt-1">Tho cat se xac nhan lich hen cua ban trong thoi gian som nhat.</p>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 w-full">
                <a href="{{ url('/') }}" class="flex-1 flex items-center justify-center gap-2 h-12 px-6 border border-muted/20 bg-white text-warm-gray text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">home</span>
                    Ve trang chu
                </a>
                @auth
                <a href="{{ route('client.profile.show') }}" class="flex-1 flex items-center justify-center gap-2 h-12 px-6 border border-muted/20 bg-white text-warm-gray text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">history</span>
                    Lich su dat lich
                </a>
                @endauth
            </div>
        </div>

        {{-- Bottom Decorator --}}
        <div class="h-1 w-full bg-primary"></div>
    </div>
</section>
@endsection
