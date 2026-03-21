@extends('layouts.client')

@section('title', 'Thong tin ca nhan')

@section('content')
<section class="bg-bg-light min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-[900px] mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-surface transition-colors mr-4">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-2xl font-bold font-display tracking-tight text-warm-gray">Thong Tin Ca Nhan</h1>
            </div>
            <a href="{{ route('client.profile.edit') }}"
                class="flex items-center gap-2 h-10 px-5 border border-muted/20 text-warm-gray text-xs font-semibold uppercase tracking-widest hover:border-primary hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-base">edit</span>
                Chinh sua
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-primary/5 border border-primary/20 text-primary text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Profile Card --}}
        <div class="bg-white border border-muted/20 shadow-sm mb-8">
            <div class="p-6 sm:p-8">
                <div class="flex items-start gap-6">
                    {{-- Avatar --}}
                    <div class="w-20 h-20 rounded-full bg-surface flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-3xl text-muted">person</span>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-bold text-warm-gray font-display mb-1">{{ $user->name }}</h2>
                        <div class="space-y-2 mt-3">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-muted text-base">mail</span>
                                <span class="text-warm-gray">{{ $user->email }}</span>
                            </div>
                            @if($user->phone)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-muted text-base">phone</span>
                                <span class="text-warm-gray">{{ $user->phone }}</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-muted text-base">calendar_month</span>
                                <span class="text-muted">Thanh vien tu {{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Bookings --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold font-display text-warm-gray mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">event_upcoming</span>
                Lich hen sap toi
            </h2>

            @if($upcomingBookings->isEmpty())
                <div class="bg-white border border-muted/20 p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-muted/40 mb-2">calendar_today</span>
                    <p class="text-muted text-sm">Chua co lich hen nao.</p>
                    <a href="{{ route('client.booking.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 h-10 bg-primary text-white text-xs font-bold uppercase tracking-widest hover:bg-warm-gray transition-colors">
                        <span class="material-symbols-outlined text-base">add</span>
                        Dat lich ngay
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($upcomingBookings as $booking)
                        <div class="bg-white border border-muted/20 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-shrink-0 w-14 h-14 bg-primary/5 border border-primary/10 flex flex-col items-center justify-center">
                                <span class="text-lg font-bold text-primary leading-none">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</span>
                                <span class="text-[10px] uppercase tracking-wider text-primary/70">Thg {{ \Carbon\Carbon::parse($booking->booking_date)->format('m') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-warm-gray">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider
                                        @if($booking->status === 'confirmed') bg-green-50 text-green-700 border border-green-200
                                        @elseif($booking->status === 'pending') bg-yellow-50 text-yellow-700 border border-yellow-200
                                        @else bg-surface text-muted border border-muted/20
                                        @endif">
                                        @if($booking->status === 'confirmed') Xac nhan
                                        @elseif($booking->status === 'pending') Cho xac nhan
                                        @elseif($booking->status === 'in_progress') Dang thuc hien
                                        @else {{ ucfirst($booking->status) }}
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm text-muted">
                                    <span class="font-medium text-warm-gray">{{ $booking->barber->user->name }}</span>
                                    &middot; {{ $booking->services->pluck('name')->join(', ') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="text-sm font-bold text-warm-gray">{{ number_format($booking->total_price, 0, ',', '.') }}d</span>
                                <span class="text-[10px] text-muted font-mono">{{ $booking->booking_code }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Past Bookings --}}
        <div>
            <h2 class="text-lg font-semibold font-display text-warm-gray mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-muted text-xl">history</span>
                Lich su dat lich
            </h2>

            @if($pastBookings->isEmpty())
                <div class="bg-white border border-muted/20 p-8 text-center">
                    <p class="text-muted text-sm">Chua co lich su dat lich.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pastBookings as $booking)
                        <div class="bg-white border border-muted/20 p-5 flex flex-col sm:flex-row sm:items-center gap-4 opacity-80">
                            <div class="flex-shrink-0 w-14 h-14 bg-surface flex flex-col items-center justify-center">
                                <span class="text-lg font-bold text-warm-gray leading-none">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</span>
                                <span class="text-[10px] uppercase tracking-wider text-muted">Thg {{ \Carbon\Carbon::parse($booking->booking_date)->format('m') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-warm-gray">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider
                                        @if($booking->status === 'completed') bg-green-50 text-green-700 border border-green-200
                                        @elseif($booking->status === 'cancelled') bg-red-50 text-red-700 border border-red-200
                                        @else bg-surface text-muted border border-muted/20
                                        @endif">
                                        @if($booking->status === 'completed') Hoan thanh
                                        @elseif($booking->status === 'cancelled') Da huy
                                        @else {{ ucfirst($booking->status) }}
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm text-muted">
                                    <span class="font-medium text-warm-gray">{{ $booking->barber->user->name }}</span>
                                    &middot; {{ $booking->services->pluck('name')->join(', ') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="text-sm font-bold text-warm-gray">{{ number_format($booking->total_price, 0, ',', '.') }}d</span>
                                <span class="text-[10px] text-muted font-mono">{{ $booking->booking_code }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Logout --}}
        <div class="mt-10 pt-8 border-t border-muted/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-sm text-muted hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-base">logout</span>
                    Dang xuat
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
