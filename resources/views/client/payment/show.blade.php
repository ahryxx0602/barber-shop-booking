@extends('layouts.client')
@use('App\Enums\PaymentMethod')

@section('title', 'Thanh toán')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:80px 16px;">
    <div style="width:100%;max-width:800px;border:1px solid var(--v-rule);background:#fff;box-shadow:5px 5px 0 var(--v-copper);overflow:hidden;position:relative;">
        {{-- Corner accents --}}
        <div class="corner corner-tl" style="width:24px;height:24px;z-index:2;"></div>
        <div class="corner corner-tr" style="width:24px;height:24px;z-index:2;"></div>
        <div class="corner corner-bl" style="width:24px;height:24px;z-index:2;"></div>
        <div class="corner corner-br" style="width:24px;height:24px;z-index:2;"></div>

        <div style="padding:32px 24px 24px;display:flex;flex-direction:column;align-items:center;" class="sm:px-8">
            {{-- Header --}}
            <div style="text-align:center;margin-bottom:24px;">
                <h1 class="v-title" style="margin-bottom:4px;font-size:clamp(1.5rem,3vw,2rem);">
                    Thanh toán
                </h1>
                <p style="color:var(--v-muted);font-size:13px;">Mã đặt lịch: <span style="font-weight:700;color:var(--v-ink);font-family:var(--font-serif);letter-spacing:1px;">{{ $booking->booking_code }}</span></p>
            </div>

            {{-- Flash messages --}}
            @if(session('error'))
                <div style="width:100%;border:1px solid #dc2626;background:rgba(220,38,38,0.06);padding:12px 16px;margin-bottom:16px;text-align:center;">
                    <p style="font-size:13px;color:#dc2626;">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Layout 2 columns on desktop --}}
            <div style="display:flex;flex-wrap:wrap;gap:24px;width:100%;align-items:flex-start;">
                
                {{-- Left Column: Order Summary --}}
                <div style="flex:1 1 300px;border:1px solid var(--v-rule);background:var(--v-surface);padding:20px;">
                    <p class="v-label" style="margin-bottom:12px;font-size:11px;text-transform:uppercase;letter-spacing:2px;">Thông tin đơn hàng</p>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--v-rule);">
                        <span style="color:var(--v-muted);font-size:13px;">Thợ cắt</span>
                        <span style="font-weight:600;font-size:13px;">{{ $booking->barber->user->name }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--v-rule);">
                        <span style="color:var(--v-muted);font-size:13px;">Ngày</span>
                        <span style="font-weight:600;font-size:13px;">{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d/m/Y') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--v-rule);">
                        <span style="color:var(--v-muted);font-size:13px;">Giờ</span>
                        <span style="font-weight:600;font-size:13px;">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:8px 0;border-bottom:1px solid var(--v-rule);">
                        <span style="color:var(--v-muted);font-size:13px;">Dịch vụ</span>
                        <div style="text-align:right;">
                            @foreach($booking->services as $service)
                                <p style="font-weight:500;font-size:13px;">{{ $service->name }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding-top:12px;">
                        <span style="font-weight:700;font-size:14px;">Tổng cộng</span>
                        <span style="font-family:var(--font-serif);font-weight:700;font-size:20px;color:var(--v-copper);">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                {{-- Right Column: Payment Method Selection --}}
                <form action="{{ route('client.payment.process', $booking) }}" method="POST" style="flex:1 1 340px;" x-data="{ method: 'cash' }">
                    @csrf
                    <p class="v-label" style="margin-bottom:12px;font-size:11px;text-transform:uppercase;letter-spacing:2px;">Chọn phương thức thanh toán</p>

                    {{-- Cash --}}
                    <label style="display:flex;align-items:center;gap:12px;padding:12px 16px;border:2px solid var(--v-rule);cursor:pointer;margin-bottom:10px;transition:all 0.2s;"
                           :style="method === 'cash' ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.06)' : ''">
                        <input type="radio" name="payment_method" value="cash" x-model="method" style="accent-color:var(--v-copper);">
                        <span class="material-symbols-outlined" style="font-size:20px;color:var(--v-copper);">payments</span>
                        <div>
                            <p style="font-weight:600;font-size:13px;">Tiền mặt tại quán</p>
                            <p style="font-size:11px;color:var(--v-muted);">Thanh toán khi đến làm dịch vụ</p>
                        </div>
                    </label>

                    {{-- VNPay --}}
                    <label style="display:flex;align-items:center;gap:12px;padding:12px 16px;border:2px solid var(--v-rule);cursor:pointer;margin-bottom:10px;transition:all 0.2s;"
                           :style="method === 'vnpay' ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.06)' : ''">
                        <input type="radio" name="payment_method" value="vnpay" x-model="method" style="accent-color:var(--v-copper);">
                        <span class="material-symbols-outlined" style="font-size:20px;color:var(--v-copper);">credit_card</span>
                        <div>
                            <p style="font-weight:600;font-size:13px;">VNPay</p>
                            <p style="font-size:11px;color:var(--v-muted);">Thanh toán qua thẻ ATM / QR Code</p>
                        </div>
                    </label>

                    {{-- Momo --}}
                    <label style="display:flex;align-items:center;gap:12px;padding:12px 16px;border:2px solid var(--v-rule);cursor:pointer;margin-bottom:16px;transition:all 0.2s;"
                           :style="method === 'momo' ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.06)' : ''">
                        <input type="radio" name="payment_method" value="momo" x-model="method" style="accent-color:var(--v-copper);">
                        <span class="material-symbols-outlined" style="font-size:20px;color:var(--v-copper);">account_balance_wallet</span>
                        <div>
                            <p style="font-weight:600;font-size:13px;">Ví MoMo</p>
                            <p style="font-size:11px;color:var(--v-muted);">Thanh toán qua ví điện tử MoMo</p>
                        </div>
                    </label>

                    {{-- Sandbox Notice --}}
                    <div style="border:1px solid var(--v-copper);background:rgba(176,137,104,0.06);padding:10px 12px;margin-bottom:16px;text-align:center;" x-show="method !== 'cash'">
                        <p style="font-size:11px;color:var(--v-ink);">
                            <span class="material-symbols-outlined" style="font-size:12px;vertical-align:middle;color:var(--v-copper);">info</span>
                            Môi trường <strong>Sandbox</strong> (Chưa trừ tiền thật).
                        </p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="v-btn-primary" style="width:100%;justify-content:center;height:48px;gap:8px;font-size:14px;">
                        <span class="material-symbols-outlined" style="font-size:18px;">lock</span>
                        <span x-text="method === 'cash' ? 'Hoàn tất đặt lịch' : 'Tiến hành thanh toán'"></span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Bottom Decorator --}}
        <div style="height:3px;width:100%;background:var(--v-copper);"></div>
    </div>
</section>
@endsection
