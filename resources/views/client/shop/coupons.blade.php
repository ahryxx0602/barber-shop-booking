@extends('layouts.client')

@section('title', 'Mã Giảm Giá')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:900px;margin:0 auto;padding:24px 16px 40px;">
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:24px;">
            <div class="v-ornament" style="max-width:200px;margin:0 auto 8px;font-size:10px;">Ưu đãi</div>
            <h1 class="v-title" style="font-size:1.4rem;">Mã Giảm Giá</h1>
            <p style="font-size:13px;color:var(--v-muted);margin-top:4px;">Nhập mã khi thanh toán để được giảm giá</p>
        </div>

        @if($coupons->isEmpty())
            <div style="text-align:center;padding:40px;color:var(--v-muted);">
                <span class="material-symbols-outlined" style="font-size:48px;opacity:0.3;">local_offer</span>
                <p style="margin-top:8px;font-size:14px;">Hiện chưa có mã giảm giá nào.</p>
            </div>
        @else
            <style>
                .coupon-grid { display:grid;gap:16px;grid-template-columns:1fr; }
                @media(min-width:600px) { .coupon-grid { grid-template-columns:1fr 1fr; } }
                .coupon-card {
                    background:#fff;border:1px dashed var(--v-rule);padding:0;overflow:hidden;
                    transition:box-shadow 0.2s,border-color 0.2s;position:relative;
                }
                .coupon-card:hover { box-shadow:3px 3px 0 var(--v-copper);border-color:var(--v-copper); }
                .coupon-left {
                    background:var(--v-ink);color:#fff;padding:12px 16px;text-align:center;
                }
                .coupon-right { padding:12px 16px; }
                .coupon-code {
                    font-family:var(--font-display);font-size:18px;font-weight:800;letter-spacing:2px;
                    color:var(--v-copper);cursor:pointer;user-select:all;
                }
                .coupon-badge {
                    display:inline-block;font-size:9px;font-weight:700;letter-spacing:0.5px;
                    text-transform:uppercase;padding:2px 8px;border:1px solid;
                }
                .coupon-badge-product { color:#059669;border-color:#059669;background:#ecfdf5; }
                .coupon-badge-shipping { color:#2563eb;border-color:#2563eb;background:#eff6ff; }
                .coupon-badge-booking { color:#9333ea;border-color:#9333ea;background:#faf5ff; }
                .coupon-info { font-size:12px;color:var(--v-muted);line-height:1.6; }
                .coupon-info strong { color:var(--v-ink); }
                .coupon-value { font-size:28px;font-weight:800;font-family:var(--font-display);line-height:1; }
                .coupon-expired { opacity:0.5;pointer-events:none; }
                .coupon-expired::after {
                    content:'HẾT HẠN';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-15deg);
                    font-size:20px;font-weight:900;color:#dc2626;border:3px solid #dc2626;padding:4px 16px;
                    letter-spacing:3px;opacity:0.6;
                }
            </style>

            <div class="coupon-grid">
                @foreach($coupons as $coupon)
                @php
                    $isExpired = $coupon->expiry_date && $coupon->expiry_date->isPast();
                    $isUsedUp = $coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit;
                    $remaining = $coupon->usage_limit !== null ? max(0, $coupon->usage_limit - $coupon->used_count) : null;
                    $daysLeft = $coupon->expiry_date ? (int) now()->diffInDays($coupon->expiry_date, false) : null;
                    $badgeClass = match($coupon->applies_to->value) {
                        'shipping' => 'coupon-badge-shipping',
                        'booking' => 'coupon-badge-booking',
                        default => 'coupon-badge-product',
                    };
                @endphp
                <div class="coupon-card {{ ($isExpired || $isUsedUp) ? 'coupon-expired' : '' }}">
                    <div class="coupon-left">
                        <div class="coupon-value">
                            @if($coupon->type->value === 'percent')
                                {{ (int) $coupon->value }}<span style="font-size:16px;">%</span>
                            @else
                                {{ number_format($coupon->value, 0, ',', '.') }}<span style="font-size:12px;">đ</span>
                            @endif
                        </div>
                        <div style="font-size:11px;margin-top:4px;opacity:0.8;">
                            {{ $coupon->type->value === 'percent' ? 'Giảm theo %' : 'Giảm cố định' }}
                        </div>
                    </div>
                    <div class="coupon-right">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                            <span class="coupon-code" title="Click để copy" onclick="navigator.clipboard.writeText('{{ $coupon->code }}').then(()=>alert('Đã copy: {{ $coupon->code }}'))">{{ $coupon->code }}</span>
                            <span class="coupon-badge {{ $badgeClass }}">{{ $coupon->applies_to->label() }}</span>
                        </div>
                        <div class="coupon-info">
                            @if($coupon->min_amount > 0)
                                <div>Đơn tối thiểu: <strong>{{ number_format($coupon->min_amount, 0, ',', '.') }}đ</strong></div>
                            @endif
                            @if($coupon->max_discount)
                                <div>Giảm tối đa: <strong>{{ number_format($coupon->max_discount, 0, ',', '.') }}đ</strong></div>
                            @endif
                            @if($coupon->expiry_date)
                                <div>
                                    @if($isExpired)
                                        <span style="color:#dc2626;">Đã hết hạn ({{ $coupon->expiry_date->format('d/m/Y') }})</span>
                                    @elseif($daysLeft <= 3)
                                        <span style="color:#dc2626;font-weight:600;">HSD: {{ $coupon->expiry_date->format('d/m/Y') }} (còn {{ $daysLeft }} ngày)</span>
                                    @else
                                        HSD: <strong>{{ $coupon->expiry_date->format('d/m/Y') }}</strong>
                                    @endif
                                </div>
                            @endif
                            @if($remaining !== null)
                                <div>
                                    Còn lại: <strong>{{ $remaining }}</strong> lượt
                                    @if($isUsedUp)
                                        <span style="color:#dc2626;"> — Đã hết</span>
                                    @endif
                                </div>
                            @else
                                <div style="color:var(--v-muted);font-style:italic;">Không giới hạn lượt</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        <div style="text-align:center;margin-top:24px;">
            <a href="{{ route('client.shop.index') }}" class="v-btn-secondary" style="font-size:13px;">
                <span class="material-symbols-outlined" style="font-size:14px;vertical-align:middle;margin-right:4px;">arrow_back</span>
                Tiếp tục mua sắm
            </a>
        </div>
    </div>
</section>
@endsection
