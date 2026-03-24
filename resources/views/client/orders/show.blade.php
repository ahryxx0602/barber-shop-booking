@extends('layouts.client')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
@use('App\Enums\OrderStatus')
@use('App\Enums\PaymentStatus')

<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:900px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">
        {{-- Breadcrumb --}}
        <div style="margin-bottom:24px;display:flex;align-items:center;gap:8px;font-size:12px;">
            <a href="{{ route('client.orders.index') }}" style="color:var(--v-muted);text-decoration:none;">Đơn hàng</a>
            <span style="color:var(--v-muted);">›</span>
            <span style="color:var(--v-ink);font-weight:500;">{{ $order->order_code }}</span>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div style="padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:14px;text-align:center;margin-bottom:20px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:14px;text-align:center;margin-bottom:20px;">{{ session('error') }}</div>
        @endif

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div>
                <h1 style="font-family:var(--font-serif);font-size:22px;font-weight:700;margin-bottom:4px;">Đơn hàng {{ $order->order_code }}</h1>
                <p style="font-size:12px;color:var(--v-muted);">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @php
                $colors = ['pending' => '#fef3c7|#92400e', 'confirmed' => '#dbeafe|#1e40af', 'shipping' => '#e0e7ff|#3730a3', 'delivered' => '#dcfce7|#166534', 'cancelled' => '#fef2f2|#991b1b'];
                $c = explode('|', $colors[$order->status->value] ?? '#f3f4f6|#374151');
            @endphp
            <span style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:6px 14px;background:{{ $c[0] }};color:{{ $c[1] }};">
                {{ $order->status->label() }}
            </span>
        </div>

        <div style="display:grid;gap:24px;" class="md:grid-cols-5">
            {{-- CỘT TRÁI (3/5): Sản phẩm + Timeline --}}
            <div class="md:col-span-3">
                {{-- Sản phẩm --}}
                <div style="background:#fff;border:1px solid var(--v-rule);padding:20px;margin-bottom:20px;">
                    <h3 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:16px;">Sản phẩm</h3>
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        @foreach($order->items as $item)
                        <div style="display:flex;gap:12px;align-items:center;{{ !$loop->last ? 'padding-bottom:12px;border-bottom:1px solid var(--v-rule);' : '' }}">
                            <div style="width:56px;height:56px;background:var(--v-surface);border:1px solid var(--v-rule);overflow:hidden;flex-shrink:0;">
                                @if($item->product?->image)
                                    <img src="{{ Storage::url($item->product->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                        <span class="material-symbols-outlined" style="font-size:20px;color:var(--v-muted);opacity:0.4;">inventory_2</span>
                                    </div>
                                @endif
                            </div>
                            <div style="flex:1;">
                                <p style="font-size:14px;font-weight:600;color:var(--v-ink);">{{ $item->product?->name ?? 'Sản phẩm không còn' }}</p>
                                <p style="font-size:12px;color:var(--v-muted);">
                                    {{ number_format($item->unit_price, 0, ',', '.') }}₫ × {{ $item->quantity }}
                                </p>
                            </div>
                            <span style="font-size:14px;font-weight:600;color:var(--v-ink);">
                                {{ number_format($item->total_price, 0, ',', '.') }}₫
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Timeline trạng thái --}}
                <div style="background:#fff;border:1px solid var(--v-rule);padding:20px;">
                    <h3 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:16px;">Trạng thái đơn hàng</h3>
                    @php
                        $statuses = [OrderStatus::Pending, OrderStatus::Confirmed, OrderStatus::Shipping, OrderStatus::Delivered];
                        $currentIndex = array_search($order->status, $statuses);
                        if ($order->status === OrderStatus::Cancelled) $currentIndex = -1;
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:0;">
                        @foreach($statuses as $i => $status)
                        @php
                            $isActive = $currentIndex !== false && $i <= $currentIndex;
                            $isCurrent = $order->status === $status;
                        @endphp
                        <div style="display:flex;align-items:start;gap:14px;position:relative;">
                            {{-- Dot + Line --}}
                            <div style="display:flex;flex-direction:column;align-items:center;position:relative;">
                                <div style="width:14px;height:14px;border-radius:50%;border:2px solid {{ $isActive ? 'var(--v-copper)' : 'var(--v-rule)' }};background:{{ $isCurrent ? 'var(--v-copper)' : ($isActive ? 'var(--v-copper)' : '#fff') }};z-index:2;flex-shrink:0;{{ $isCurrent ? 'box-shadow:0 0 0 4px rgba(176,137,104,0.2);' : '' }}"></div>
                                @if(!$loop->last)
                                <div style="width:2px;height:32px;background:{{ $isActive && $i < $currentIndex ? 'var(--v-copper)' : 'var(--v-rule)' }};"></div>
                                @endif
                            </div>
                            <div style="padding-bottom:{{ $loop->last ? '0' : '18' }}px;">
                                <p style="font-size:13px;font-weight:{{ $isCurrent ? '700' : '500' }};color:{{ $isActive ? 'var(--v-ink)' : 'var(--v-muted)' }};">
                                    {{ $status->label() }}
                                </p>
                            </div>
                        </div>
                        @endforeach

                        {{-- Cancelled state --}}
                        @if($order->status === OrderStatus::Cancelled)
                        <div style="display:flex;align-items:start;gap:14px;margin-top:12px;">
                            <div style="width:14px;height:14px;border-radius:50%;background:#ef4444;border:2px solid #ef4444;box-shadow:0 0 0 4px rgba(239,68,68,0.2);flex-shrink:0;"></div>
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#dc2626;">Đã hủy</p>
                                @if($order->cancel_reason)
                                <p style="font-size:12px;color:var(--v-muted);margin-top:2px;">Lý do: {{ $order->cancel_reason }}</p>
                                @endif
                                @if($order->cancelled_at)
                                <p style="font-size:11px;color:var(--v-muted);">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI (2/5): Tóm tắt + Thanh toán + Địa chỉ --}}
            <div class="md:col-span-2">
                {{-- Tóm tắt thanh toán --}}
                <div style="background:#fff;border:1px solid var(--v-rule);padding:20px;margin-bottom:20px;">
                    <h3 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:16px;">Tóm tắt</h3>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:14px;">
                            <span style="color:var(--v-muted);">Tạm tính</span>
                            <span>{{ number_format($order->subtotal, 0, ',', '.') }}₫</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;">
                            <span style="color:var(--v-muted);">Thuế VAT ({{ $order->tax_rate }}%)</span>
                            <span>{{ number_format($order->tax_amount, 0, ',', '.') }}₫</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;">
                            <span style="color:var(--v-muted);">Phí vận chuyển</span>
                            <span>
                                @if($order->shipping_fee > 0)
                                    {{ number_format($order->shipping_fee, 0, ',', '.') }}₫
                                @else
                                    <span style="color:#166534;">Miễn phí</span>
                                @endif
                            </span>
                        </div>
                        @if($order->shipping_distance_km)
                        <div style="font-size:11px;color:var(--v-muted);text-align:right;">
                            Khoảng cách: {{ $order->shipping_distance_km }} km
                        </div>
                        @endif
                        <div style="border-top:2px solid var(--v-ink);padding-top:10px;margin-top:4px;display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-family:var(--font-serif);font-size:15px;font-weight:700;">Tổng cộng</span>
                            <span style="font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--v-copper);">
                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Thanh toán --}}
                <div style="background:#fff;border:1px solid var(--v-rule);padding:20px;margin-bottom:20px;">
                    <h3 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:12px;">Thanh toán</h3>
                    <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
                        <span style="color:var(--v-muted);">Phương thức</span>
                        <span style="font-weight:600;">{{ $order->payment?->method->label() ?? 'N/A' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:14px;">
                        <span style="color:var(--v-muted);">Trạng thái</span>
                        @php $payStatus = $order->payment?->status; @endphp
                        <span style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;background:{{ $payStatus === PaymentStatus::Paid ? '#dcfce7' : ($payStatus === PaymentStatus::Failed ? '#fef2f2' : '#fef3c7') }};color:{{ $payStatus === PaymentStatus::Paid ? '#166534' : ($payStatus === PaymentStatus::Failed ? '#991b1b' : '#92400e') }};">
                            {{ $payStatus?->label() ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                {{-- Địa chỉ --}}
                @if($order->shippingAddress)
                <div style="background:#fff;border:1px solid var(--v-rule);padding:20px;margin-bottom:20px;">
                    <h3 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:12px;">Địa chỉ giao hàng</h3>
                    <p style="font-size:14px;font-weight:600;margin-bottom:4px;">{{ $order->shippingAddress->recipient_name }}</p>
                    <p style="font-size:13px;color:var(--v-muted);margin-bottom:4px;">{{ $order->shippingAddress->phone }}</p>
                    <p style="font-size:13px;color:var(--v-ink-soft);line-height:1.5;">
                        {{ $order->shippingAddress->address }}, {{ $order->shippingAddress->ward }},
                        {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->city }}
                    </p>
                </div>
                @endif

                {{-- Ghi chú --}}
                @if($order->note)
                <div style="background:#fff;border:1px solid var(--v-rule);padding:20px;margin-bottom:20px;">
                    <h3 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:8px;">Ghi chú</h3>
                    <p style="font-size:13px;color:var(--v-ink-soft);line-height:1.6;">{{ $order->note }}</p>
                </div>
                @endif

                {{-- Nút hủy (chỉ khi pending) --}}
                @if($order->status === OrderStatus::Pending)
                <div x-data="{ showCancel: false }">
                    <button @click="showCancel = true" type="button" style="width:100%;height:40px;background:none;border:1px solid #fecaca;color:#dc2626;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;"
                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                        Hủy đơn hàng
                    </button>

                    {{-- Confirm cancel --}}
                    <div x-show="showCancel" x-transition style="margin-top:12px;">
                        <form method="POST" action="{{ route('client.orders.cancel', $order) }}">
                            @csrf
                            @method('PATCH')
                            <textarea name="cancel_reason" class="v-textarea" rows="2" style="font-size:13px;margin-bottom:8px;" placeholder="Lý do hủy đơn (không bắt buộc)"></textarea>
                            <div style="display:flex;gap:8px;">
                                <button type="submit" style="flex:1;height:36px;background:#dc2626;color:#fff;border:none;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;cursor:pointer;">Xác nhận hủy</button>
                                <button type="button" @click="showCancel = false" style="flex:1;height:36px;background:var(--v-surface);color:var(--v-ink);border:1px solid var(--v-rule);font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;cursor:pointer;">Quay lại</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
