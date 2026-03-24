@extends('layouts.client')

@section('title', 'Giỏ hàng')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:800px;margin:0 auto;padding:40px 24px 64px;">
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:28px;">
            <div class="v-ornament" style="max-width:200px;margin:0 auto 14px;">Giỏ hàng</div>
            <h1 class="v-title" style="font-size:clamp(1.5rem,3vw,2rem);">Giỏ Hàng Của Bạn</h1>
        </div>

        {{-- Warnings: SP hết hàng, disable, điều chỉnh SL --}}
        @if(!empty($warnings))
        <div style="margin-bottom:20px;">
            @foreach($warnings as $warning)
            <div style="padding:10px 16px;background:#fef3c7;border:1px solid #fde68a;color:#92400e;font-size:13px;margin-bottom:6px;display:flex;align-items:center;gap:8px;">
                <span class="material-symbols-outlined" style="font-size:16px;flex-shrink:0;">warning</span>
                {{ $warning }}
            </div>
            @endforeach
        </div>
        @endif

        @if(empty($cartItems))
            <div style="text-align:center;padding:48px 0;">
                <span class="material-symbols-outlined" style="font-size:56px;color:var(--v-muted);opacity:0.3;display:block;margin-bottom:16px;">shopping_cart</span>
                <p style="color:var(--v-muted);font-size:15px;margin-bottom:24px;">Giỏ hàng trống. Hãy khám phá cửa hàng!</p>
                <a href="{{ route('client.shop.index') }}" class="v-btn-primary v-btn-sm">Mua sắm ngay</a>
            </div>
        @else
            {{-- Cart Items — Card-based --}}
            <div id="cart-container" style="display:flex;flex-direction:column;gap:16px;">
                @foreach($cartItems as $item)
                @php
                    $product = $item['product'];
                    $qty = $item['quantity'];
                    $isLowStock = $product->stock_quantity <= 10 && $product->stock_quantity > 0;
                    $isAtMax = $qty >= $product->stock_quantity;
                @endphp
                <div class="cart-item" id="cart-item-{{ $product->id }}"
                    style="background:#fff;border:1px solid var(--v-rule);padding:16px;display:flex;gap:16px;align-items:start;position:relative;transition:opacity 0.3s;">

                    {{-- Ảnh SP --}}
                    <a href="{{ route('client.shop.show', $product->slug) }}" style="flex-shrink:0;">
                        <div style="width:80px;height:80px;background:var(--v-surface);border:1px solid var(--v-rule);overflow:hidden;">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;display:block;" />
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                    <span class="material-symbols-outlined" style="font-size:24px;color:var(--v-muted);opacity:0.4;">inventory_2</span>
                                </div>
                            @endif
                        </div>
                    </a>

                    {{-- Thông tin + Controls --}}
                    <div style="flex:1;min-width:0;">
                        {{-- Row 1: Tên + Xóa --}}
                        <div style="display:flex;justify-content:space-between;align-items:start;gap:8px;margin-bottom:6px;">
                            <div style="min-width:0;">
                                <a href="{{ route('client.shop.show', $product->slug) }}" style="font-family:var(--font-serif);font-size:15px;font-weight:700;color:var(--v-ink);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $product->name }}
                                </a>
                                <span style="font-size:10px;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;">{{ $product->category->label() }}</span>
                            </div>
                            <button onclick="removeCartItem({{ $product->id }})" type="button"
                                style="width:28px;height:28px;border:1px solid var(--v-rule);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--v-muted);transition:all 0.2s;flex-shrink:0;"
                                onmouseover="this.style.borderColor='#fecaca';this.style.color='#dc2626';this.style.background='#fef2f2'"
                                onmouseout="this.style.borderColor='var(--v-rule)';this.style.color='var(--v-muted)';this.style.background='#fff'"
                                title="Xóa khỏi giỏ">
                                <span class="material-symbols-outlined" style="font-size:14px;">close</span>
                            </button>
                        </div>

                        {{-- Row 2: Đơn giá + SL + Thành tiền --}}
                        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                            {{-- Đơn giá --}}
                            <span style="font-size:14px;color:var(--v-ink-soft);">
                                {{ number_format($product->price, 0, ',', '.') }}₫
                            </span>

                            <span style="color:var(--v-rule);">×</span>

                            {{-- Bộ chọn SL --}}
                            <div style="display:flex;align-items:center;border:1px solid var(--v-rule);height:32px;">
                                <button onclick="changeQty({{ $product->id }}, {{ $qty - 1 }}, {{ $product->stock_quantity }})" type="button"
                                    style="width:30px;height:100%;border:none;background:var(--v-surface);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;color:var(--v-ink);transition:background 0.2s;"
                                    onmouseover="this.style.background='var(--v-parchment)'" onmouseout="this.style.background='var(--v-surface)'">−</button>
                                <span id="qty-{{ $product->id }}" style="width:34px;text-align:center;font-size:13px;font-weight:600;border-left:1px solid var(--v-rule);border-right:1px solid var(--v-rule);height:100%;display:flex;align-items:center;justify-content:center;">{{ $qty }}</span>
                                <button onclick="changeQty({{ $product->id }}, {{ $qty + 1 }}, {{ $product->stock_quantity }})" type="button"
                                    style="width:30px;height:100%;border:none;background:var(--v-surface);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;color:var(--v-ink);transition:background 0.2s;"
                                    onmouseover="this.style.background='var(--v-parchment)'" onmouseout="this.style.background='var(--v-surface)'">+</button>
                            </div>

                            {{-- Thành tiền --}}
                            <span style="margin-left:auto;font-family:var(--font-display);font-size:16px;font-weight:700;color:var(--v-copper);">
                                {{ number_format($item['total'], 0, ',', '.') }}₫
                            </span>
                        </div>

                        {{-- Row 3: Stock info + cảnh báo --}}
                        <div style="margin-top:8px;display:flex;align-items:center;gap:6px;">
                            @if($isAtMax)
                                <span style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                                <span style="font-size:11px;color:#92400e;">Đã chọn tối đa (kho: {{ $product->stock_quantity }})</span>
                            @elseif($isLowStock)
                                <span style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                                <span style="font-size:11px;color:#92400e;">Còn {{ $product->stock_quantity }} trong kho</span>
                            @else
                                <span style="width:5px;height:5px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                                <span style="font-size:11px;color:#166534;">Còn hàng ({{ $product->stock_quantity }})</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Thanh tổng + Actions --}}
            <div style="margin-top:24px;background:#fff;border:1px solid var(--v-rule);padding:20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-size:13px;color:var(--v-muted);">Tạm tính ({{ count($cartItems) }} sản phẩm)</span>
                    <span id="cart-subtotal" style="font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--v-ink);">
                        {{ number_format($subtotal, 0, ',', '.') }}₫
                    </span>
                </div>
                <p style="font-size:11px;color:var(--v-muted);margin-bottom:16px;">Thuế VAT và phí vận chuyển sẽ được tính ở bước thanh toán.</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;">
                    <a href="{{ route('client.shop.index') }}" class="v-btn-outline v-btn-sm">Tiếp tục mua sắm</a>
                    <a href="{{ route('client.checkout') }}" class="v-btn-primary v-btn-sm">
                        <span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">shopping_cart_checkout</span>
                        Thanh toán
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Toast Notification --}}
<div id="cart-toast" style="display:none;position:fixed;bottom:24px;right:24px;z-index:1000;max-width:360px;padding:14px 20px;font-size:13px;font-weight:500;box-shadow:4px 4px 0 var(--v-copper);transition:opacity 0.3s,transform 0.3s;opacity:0;transform:translateY(10px);"></div>

@push('scripts')
<script>
// Toast thay cho alert()
function showToast(message, type = 'error') {
    const toast = document.getElementById('cart-toast');
    toast.textContent = message;
    toast.style.background = type === 'success' ? 'var(--v-ink)' : '#991b1b';
    toast.style.color = '#fff';
    toast.style.display = 'block';
    requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; });
    setTimeout(() => {
        toast.style.opacity = '0'; toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.style.display = 'none', 300);
    }, 3500);
}

// Validate SL phía client trước khi gửi request
function changeQty(productId, newQty, maxStock) {
    if (newQty < 1) { removeCartItem(productId); return; }
    if (newQty > maxStock) {
        showToast('Kho chỉ còn ' + maxStock + ' sản phẩm. Không thể thêm nữa.');
        return;
    }
    updateCartQty(productId, newQty);
}

// Cập nhật SL sản phẩm trong giỏ
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
            location.reload();
        } else {
            if (data.removed) {
                location.reload();
            } else {
                showToast(data.message || 'Có lỗi xảy ra.');
            }
        }
    })
    .catch(() => showToast('Không thể cập nhật giỏ hàng.'));
}

// Xóa SP khỏi giỏ
function removeCartItem(productId) {
    const item = document.getElementById('cart-item-' + productId);
    if (item) item.style.opacity = '0.3';

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
    .catch(() => { if (item) item.style.opacity = '1'; showToast('Không thể xóa sản phẩm.'); });
}
</script>
@endpush
@endsection
