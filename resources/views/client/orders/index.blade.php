@extends('layouts.client')

@section('title', 'Đơn hàng của tôi')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:900px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:32px;">
            <div class="v-ornament" style="max-width:240px;margin:0 auto 14px;">Đơn hàng</div>
            <h1 class="v-title" style="font-size:clamp(1.5rem,3vw,2rem);">Đơn Hàng Của Tôi</h1>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div style="padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:14px;text-align:center;margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($orders->isEmpty())
            <div style="text-align:center;padding:48px 0;">
                <span class="material-symbols-outlined" style="font-size:48px;color:var(--v-muted);opacity:0.3;display:block;margin-bottom:12px;">shopping_bag</span>
                <p style="color:var(--v-muted);font-size:14px;margin-bottom:20px;">Bạn chưa có đơn hàng nào.</p>
                <a href="{{ route('client.shop.index') }}" class="v-btn-primary v-btn-sm">Mua sắm ngay</a>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:16px;">
                @foreach($orders as $order)
                <div class="v-card" style="padding:20px;position:relative;">
                    <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:start;justify-content:space-between;">
                        {{-- Left info --}}
                        <div style="flex:1;min-width:200px;">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                                <span style="font-family:var(--font-serif);font-size:16px;font-weight:700;color:var(--v-ink);">
                                    {{ $order->order_code }}
                                </span>
                                @php
                                    $colors = ['pending' => '#fef3c7|#92400e', 'confirmed' => '#dbeafe|#1e40af', 'shipping' => '#e0e7ff|#3730a3', 'delivered' => '#dcfce7|#166534', 'cancelled' => '#fef2f2|#991b1b'];
                                    $c = explode('|', $colors[$order->status->value] ?? '#f3f4f6|#374151');
                                @endphp
                                <span style="font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;background:{{ $c[0] }};color:{{ $c[1] }};">
                                    {{ $order->status->label() }}
                                </span>
                            </div>
                            <p style="font-size:12px;color:var(--v-muted);margin-bottom:6px;">
                                Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                            </p>
                            <div style="font-size:13px;color:var(--v-ink-soft);">
                                {{ $order->items->count() }} sản phẩm •
                                {{ $order->payment?->method->label() ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Right: Tổng tiền + Action --}}
                        <div style="text-align:right;">
                            <span style="font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--v-copper);display:block;margin-bottom:8px;">
                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </span>
                            <a href="{{ route('client.orders.show', $order) }}" class="v-btn-outline v-btn-sm" style="font-size:8px;height:32px;padding:0 16px;">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div style="margin-top:28px;display:flex;justify-content:center;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
