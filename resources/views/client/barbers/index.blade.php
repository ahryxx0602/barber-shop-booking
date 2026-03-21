@extends('layouts.client')

@section('title', 'Doi ngu tho cat')

@section('content')
<section class="bg-bg-light min-h-screen">
    <div class="max-w-[1200px] mx-auto px-6 md:px-12 lg:px-24 py-16 md:py-24">
        {{-- Header --}}
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="w-8 h-[1.5px] bg-primary"></div>
                <span class="text-[10px] font-semibold tracking-[4px] uppercase text-warm-gray-light">Doi ngu</span>
                <div class="w-8 h-[1.5px] bg-primary"></div>
            </div>
            <h1 class="font-serif text-4xl md:text-5xl font-bold text-warm-gray mb-4">Tho Cat Cua Chung Toi</h1>
            <p class="text-warm-gray-light max-w-md mx-auto">Doi ngu tho cat chuyen nghiep, giau kinh nghiem va dam me voi nghe.</p>
        </div>

        {{-- Search --}}
        <div class="max-w-md mx-auto mb-12" x-data="{ search: '{{ request('search') }}' }">
            <form method="GET" action="{{ route('client.barbers.index') }}">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-xl">search</span>
                    <input type="text" name="search" x-model="search" placeholder="Tim tho cat theo ten..."
                        class="w-full pl-12 pr-4 py-3 bg-white border border-muted/20 text-warm-gray placeholder-muted text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" />
                </div>
            </form>
        </div>

        {{-- Barber Grid --}}
        @if($barbers->isEmpty())
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-5xl text-muted/40 mb-4 block">content_cut</span>
                <p class="text-warm-gray-light">Khong tim thay tho cat nao.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach($barbers as $barber)
                <a href="{{ route('client.barbers.show', $barber) }}" class="group block">
                    <div class="relative overflow-hidden mb-6">
                        <div class="aspect-[3/4] w-full bg-surface">
                            @if($barber->user->avatar)
                                <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                                    class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-surface">
                                    <span class="material-symbols-outlined text-7xl text-muted/40">person</span>
                                </div>
                            @endif
                        </div>
                        {{-- Overlay on hover --}}
                        <div class="absolute inset-0 bg-primary/0 group-hover:bg-primary/10 transition-colors duration-500"></div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-warm-gray group-hover:text-primary transition-colors">{{ $barber->user->name }}</h3>
                        <p class="text-sm text-warm-gray-light mt-1">{{ $barber->experience_years }} nam kinh nghiem</p>
                        @if($barber->rating > 0)
                        <div class="flex items-center justify-center gap-1 mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined {{ $i <= round($barber->rating) ? 'fill text-primary' : 'text-muted/30' }} text-base">star</span>
                            @endfor
                            <span class="text-sm font-medium text-warm-gray ml-1">{{ number_format($barber->rating, 1) }}</span>
                        </div>
                        @endif
                        @if($barber->bio)
                        <p class="text-sm text-muted mt-3 line-clamp-2">{{ Str::limit($barber->bio, 100) }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
