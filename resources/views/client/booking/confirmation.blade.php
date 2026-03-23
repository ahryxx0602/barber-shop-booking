@extends('layouts.client')

@section('title', 'Xác nhận đặt lịch')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:80px 16px;">
    <div style="width:100%;max-width:600px;border:1px solid var(--v-rule);background:#fff;box-shadow:5px 5px 0 var(--v-copper);overflow:hidden;position:relative;">
        {{-- Corner accents --}}
        <div class="corner corner-tl" style="width:24px;height:24px;z-index:2;"></div>
        <div class="corner corner-tr" style="width:24px;height:24px;z-index:2;"></div>
        <div class="corner corner-bl" style="width:24px;height:24px;z-index:2;"></div>
        <div class="corner corner-br" style="width:24px;height:24px;z-index:2;"></div>

        <div style="padding:48px 24px 40px;display:flex;flex-direction:column;align-items:center;" class="sm:px-12">
            {{-- Success Icon --}}
            <div style="width:56px;height:56px;border:2px solid var(--v-copper);display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
                <span class="material-symbols-outlined fill" style="font-size:28px;color:var(--v-copper);">check_circle</span>
            </div>

            {{-- Headline --}}
            <h1 class="v-title" style="text-align:center;margin-bottom:8px;font-size:clamp(1.75rem,3vw,2.25rem);">
                Đặt lịch thành công!
            </h1>
            <p style="color:var(--v-muted);font-size:13px;margin-bottom:32px;">Mã đặt lịch: <span style="font-weight:700;color:var(--v-ink);font-family:var(--font-serif);letter-spacing:1px;">{{ $booking->booking_code }}</span></p>

            {{-- Details Box --}}
            <div style="width:100%;border:1px solid var(--v-rule);background:var(--v-surface);padding:24px;margin-bottom:32px;">
                <div style="display:flex;flex-direction:column;gap:0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--v-rule);">
                        <p class="v-label">Ngày</p>
                        <p style="font-weight:600;color:var(--v-ink);font-size:14px;text-align:right;">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d/m/Y') }}
                        </p>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--v-rule);">
                        <p class="v-label">Giờ</p>
                        <p style="font-weight:600;color:var(--v-ink);font-size:14px;text-align:right;">
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        </p>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--v-rule);">
                        <p class="v-label">Thợ cắt</p>
                        <p style="font-weight:600;color:var(--v-ink);font-size:14px;text-align:right;">{{ $booking->barber->user->name }}</p>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:12px 0;border-bottom:1px solid var(--v-rule);">
                        <p class="v-label">Dịch vụ</p>
                        <div style="text-align:right;">
                            @foreach($booking->services as $service)
                                <p style="font-weight:500;color:var(--v-ink);font-size:13px;">{{ $service->name }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;">
                        <p class="v-label">Tổng cộng</p>
                        <p style="font-family:var(--font-serif);font-weight:700;font-size:20px;color:var(--v-copper);text-align:right;">{{ number_format($booking->total_price, 0, ',', '.') }}d</p>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div style="width:100%;border:1px solid var(--v-copper);background:rgba(176,137,104,0.06);padding:16px;margin-bottom:32px;text-align:center;">
                <p style="font-size:13px;color:var(--v-ink);display:flex;align-items:center;justify-content:center;gap:6px;">
                    <span class="material-symbols-outlined" style="font-size:16px;color:var(--v-copper);">schedule</span>
                    Trạng thái: <span style="font-weight:700;">Đang chờ xác nhận</span>
                </p>
                <p style="font-size:11px;color:var(--v-muted);margin-top:4px;">Thợ cắt sẽ xác nhận lịch hẹn của bạn trong thời gian sớm nhất.</p>
            </div>

            {{-- Actions --}}
            <div style="display:flex;flex-direction:column;gap:12px;width:100;" class="sm:flex-row">
                <a href="{{ url('/') }}" class="v-btn-outline v-btn-sm" style="flex:1;justify-content:center;gap:8px;">
                    <span class="material-symbols-outlined" style="font-size:16px;">home</span>
                    Về trang chủ
                </a>
                @auth
                <a href="{{ route('client.profile.show') }}" class="v-btn-outline v-btn-sm" style="flex:1;justify-content:center;gap:8px;">
                    <span class="material-symbols-outlined" style="font-size:16px;">history</span>
                    Lịch sử đặt lịch
                </a>
                @endauth
            </div>
        </div>

        {{-- Bottom Decorator --}}
        <div style="height:3px;width:100%;background:var(--v-copper);"></div>
    </div>
</section>
@endsection
