@extends('layouts.client')

@section('title', 'Đặt hàng thành công')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:600px;margin:0 auto;padding:80px 24px 64px;text-align:center;">
        {{-- Checkmark Animation --}}
        <div style="width:100px;height:100px;margin:0 auto 32px;position:relative;">
            <svg viewBox="0 0 100 100" style="width:100%;height:100%;">
                <circle cx="50" cy="50" r="45" fill="none" stroke="var(--v-copper)" stroke-width="3" stroke-dasharray="283" stroke-dashoffset="283" style="animation:drawCircle 0.8s ease forwards;" />
                <path d="M30 50 L45 65 L70 35" fill="none" stroke="var(--v-ink)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="60" stroke-dashoffset="60" style="animation:drawCheck 0.6s ease 0.6s forwards;" />
            </svg>
        </div>

        <h1 class="v-title" style="font-size:clamp(1.5rem,3vw,2.25rem);margin-bottom:12px;">Đặt Hàng Thành Công!</h1>
        <p style="color:var(--v-muted);font-size:15px;margin-bottom:32px;">
            Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ xử lý đơn hàng sớm nhất.
        </p>

        {{-- Order Info Card --}}
        <div style="background:#fff;border:1px solid var(--v-rule);box-shadow:4px 4px 0 var(--v-copper);padding:28px;margin-bottom:32px;position:relative;text-align:left;">
            <div class="corner corner-tl"></div>
            <div class="corner corner-br"></div>

            <div style="display:flex;flex-direction:column;gap:14px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;">Mã đơn hàng</span>
                    <span style="font-family:var(--font-serif);font-size:18px;font-weight:700;color:var(--v-ink);">{{ $order->order_code }}</span>
                </div>
                <div style="height:1px;background:var(--v-rule);"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;">Tổng tiền</span>
                    <span style="font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--v-copper);">
                        {{ number_format($order->total_amount, 0, ',', '.') }}₫
                    </span>
                </div>
                <div style="height:1px;background:var(--v-rule);"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;">Thanh toán</span>
                    <span style="font-size:14px;font-weight:600;">{{ $order->payment?->method->label() ?? 'N/A' }}</span>
                </div>
                <div style="height:1px;background:var(--v-rule);"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;">Trạng thái</span>
                    <span style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 10px;background:{{ $order->status === \App\Enums\OrderStatus::Pending ? '#fef3c7' : '#dcfce7' }};color:{{ $order->status === \App\Enums\OrderStatus::Pending ? '#92400e' : '#166534' }};">
                        {{ $order->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Action buttons --}}
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('client.orders.show', $order) }}" class="v-btn-primary v-btn-sm">
                <span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">visibility</span>
                Xem chi tiết đơn
            </a>
            <a href="{{ route('client.shop.index') }}" class="v-btn-outline v-btn-sm">Tiếp tục mua sắm</a>
        </div>
    </div>
</section>

@push('styles')
<style>
@keyframes drawCircle {
    to { stroke-dashoffset: 0; }
}
@keyframes drawCheck {
    to { stroke-dashoffset: 0; }
}
</style>
@endpush
@endsection
