@extends('layouts.client')

@section('title', 'Giỏ hàng')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:1000px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:32px;">
            <div class="v-ornament" style="max-width:200px;margin:0 auto 14px;">Giỏ hàng</div>
            <h1 class="v-title" style="font-size:clamp(1.5rem,3vw,2rem);">Giỏ Hàng Của Bạn</h1>
        </div>

        @if(empty($cartItems))
            {{-- Giỏ hàng trống --}}
            <div style="text-align:center;padding:48px 0;">
                <span class="material-symbols-outlined" style="font-size:56px;color:var(--v-muted);opacity:0.3;display:block;margin-bottom:16px;">shopping_cart</span>
                <p style="color:var(--v-muted);font-size:15px;margin-bottom:24px;">Giỏ hàng trống. Hãy khám phá cửa hàng!</p>
                <a href="{{ route('client.shop.index') }}" class="v-btn-primary v-btn-sm">Mua sắm ngay</a>
            </div>
        @else
            {{-- Cart Items --}}
            <div id="cart-container">
                {{-- Desktop Table Header --}}
                <div class="hidden md:grid" style="grid-template-columns:80px 1fr 120px 120px 120px 40px;gap:12px;align-items:center;padding:0 0 12px;border-bottom:2px solid var(--v-rule);margin-bottom:16px;">
                    <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);">Ảnh</span>
                    <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);">Sản phẩm</span>
                    <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-align:center;">Đơn giá</span>
                    <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-align:center;">Số lượng</span>
                    <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-align:right;">Thành tiền</span>
                    <span></span>
                </div>

                @foreach($cartItems as $item)
                <div class="cart-item" id="cart-item-{{ $item['product']->id }}" style="border-bottom:1px solid var(--v-rule);padding:16px 0;">
                    {{-- Desktop View --}}
                    <div class="hidden md:grid" style="grid-template-columns:80px 1fr 120px 120px 120px 40px;gap:12px;align-items:center;">
                        {{-- Ảnh --}}
                        <div style="width:80px;height:80px;background:var(--v-surface);border:1px solid var(--v-rule);overflow:hidden;">
                            @if($item['product']->image)
                                <img src="{{ Storage::url($item['product']->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                    <span class="material-symbols-outlined" style="font-size:24px;color:var(--v-muted);opacity:0.4;">inventory_2</span>
                                </div>
                            @endif
                        </div>
                        {{-- Tên --}}
                        <div>
                            <a href="{{ route('client.shop.show', $item['product']->slug) }}" style="font-family:var(--font-serif);font-size:15px;font-weight:700;color:var(--v-ink);text-decoration:none;">
                                {{ $item['product']->name }}
                            </a>
                            <p style="font-size:11px;color:var(--v-muted);margin-top:2px;">{{ $item['product']->category->label() }}</p>
                        </div>
                        {{-- Đơn giá --}}
                        <div style="text-align:center;font-size:14px;color:var(--v-ink);">
                            {{ number_format($item['product']->price, 0, ',', '.') }}₫
                        </div>
                        {{-- Số lượng --}}
                        <div style="display:flex;align-items:center;justify-content:center;">
                            <div style="display:flex;align-items:center;border:1px solid var(--v-rule);height:32px;">
                                <button onclick="updateCartQty({{ $item['product']->id }}, {{ $item['quantity'] - 1 }})" type="button"
                                    style="width:32px;height:100%;border:none;background:var(--v-surface);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                                <span id="qty-{{ $item['product']->id }}" style="width:36px;text-align:center;font-size:13px;font-weight:600;">{{ $item['quantity'] }}</span>
                                <button onclick="updateCartQty({{ $item['product']->id }}, {{ $item['quantity'] + 1 }})" type="button"
                                    style="width:32px;height:100%;border:none;background:var(--v-surface);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;" {{ $item['quantity'] >= $item['product']->stock_quantity ? 'disabled' : '' }}>+</button>
                            </div>
                        </div>
                        {{-- Thành tiền --}}
                        <div id="total-{{ $item['product']->id }}" style="text-align:right;font-size:14px;font-weight:600;color:var(--v-copper);">
                            {{ number_format($item['total'], 0, ',', '.') }}₫
                        </div>
                        {{-- Xóa --}}
                        <button onclick="removeCartItem({{ $item['product']->id }})" type="button"
                            style="width:32px;height:32px;border:none;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--v-muted);transition:color 0.2s;"
                            onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='var(--v-muted)'">
                            <span class="material-symbols-outlined" style="font-size:18px;">close</span>
                        </button>
                    </div>

                    {{-- Mobile View --}}
                    <div class="md:hidden" style="display:flex;gap:12px;">
                        <div style="width:70px;height:70px;background:var(--v-surface);border:1px solid var(--v-rule);overflow:hidden;flex-shrink:0;">
                            @if($item['product']->image)
                                <img src="{{ Storage::url($item['product']->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex;justify-content:space-between;align-items:start;">
                                <a href="{{ route('client.shop.show', $item['product']->slug) }}" style="font-family:var(--font-serif);font-size:14px;font-weight:700;color:var(--v-ink);text-decoration:none;">{{ $item['product']->name }}</a>
                                <button onclick="removeCartItem({{ $item['product']->id }})" type="button" style="border:none;background:none;cursor:pointer;color:var(--v-muted);">
                                    <span class="material-symbols-outlined" style="font-size:16px;">close</span>
                                </button>
                            </div>
                            <p style="font-size:13px;color:var(--v-copper);margin:4px 0;">{{ number_format($item['product']->price, 0, ',', '.') }}₫</p>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                                <div style="display:flex;align-items:center;border:1px solid var(--v-rule);height:28px;">
                                    <button onclick="updateCartQty({{ $item['product']->id }}, {{ $item['quantity'] - 1 }})" type="button" style="width:28px;height:100%;border:none;background:var(--v-surface);cursor:pointer;font-size:12px;">−</button>
                                    <span style="width:30px;text-align:center;font-size:12px;font-weight:600;">{{ $item['quantity'] }}</span>
                                    <button onclick="updateCartQty({{ $item['product']->id }}, {{ $item['quantity'] + 1 }})" type="button" style="width:28px;height:100%;border:none;background:var(--v-surface);cursor:pointer;font-size:12px;">+</button>
                                </div>
                                <span id="total-mobile-{{ $item['product']->id }}" style="font-size:14px;font-weight:600;color:var(--v-ink);">{{ number_format($item['total'], 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Subtotal & Actions --}}
                <div style="margin-top:24px;display:flex;flex-direction:column;align-items:flex-end;gap:16px;">
                    <div style="display:flex;align-items:center;gap:16px;">
                        <span style="font-size:14px;color:var(--v-muted);">Tạm tính:</span>
                        <span id="cart-subtotal" style="font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--v-ink);">
                            {{ number_format($subtotal, 0, ',', '.') }}₫
                        </span>
                    </div>
                    <p style="font-size:12px;color:var(--v-muted);">Thuế và phí vận chuyển sẽ được tính ở bước thanh toán.</p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="{{ route('client.shop.index') }}" class="v-btn-outline v-btn-sm">Tiếp tục mua sắm</a>
                        <a href="{{ route('client.checkout') }}" class="v-btn-primary v-btn-sm">
                            <span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">shopping_cart_checkout</span>
                            Thanh toán
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
function updateCartQty(productId, newQty) {
    if (newQty < 1) { removeCartItem(productId); return; }

    fetch('{{ route("client.cart.update") }}', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: newQty })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            // Reload trang để cập nhật UI đầy đủ (nút +/-)
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra.');
        }
    })
    .catch(() => alert('Không thể cập nhật.'));
}

function removeCartItem(productId) {
    fetch('{{ route("client.cart.remove") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            location.reload();
        }
    })
    .catch(() => alert('Không thể xóa.'));
}
</script>
@endpush
@endsection
