@extends('layouts.client')

@section('title', $product->name)

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:960px;margin:0 auto;padding:32px 24px 48px;" class="md:px-12">
        {{-- Breadcrumb --}}
        <div style="margin-bottom:20px;display:flex;align-items:center;gap:6px;font-size:11px;">
            <a href="{{ route('client.shop.index') }}" style="color:var(--v-muted);text-decoration:none;" onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">Cửa hàng</a>
            <span style="color:var(--v-muted);">›</span>
            <a href="{{ route('client.shop.index', ['category' => $product->category->value]) }}" style="color:var(--v-muted);text-decoration:none;" onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">{{ $product->category->label() }}</a>
            <span style="color:var(--v-muted);">›</span>
            <span style="color:var(--v-ink);font-weight:500;">{{ Str::limit($product->name, 30) }}</span>
        </div>

        {{-- Product Detail: 2 cột compact --}}
        <div style="display:grid;gap:28px;" class="md:grid-cols-2">
            {{-- Ảnh --}}
            <div style="position:relative;">
                <div style="aspect-ratio:4/3;width:100%;background:var(--v-surface);border:1px solid var(--v-rule);box-shadow:4px 4px 0 var(--v-copper);overflow:hidden;">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                            style="width:100%;height:100%;object-fit:cover;display:block;" />
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                            <span class="material-symbols-outlined" style="font-size:56px;color:var(--v-muted);opacity:0.3;">inventory_2</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Thông tin gọn --}}
            <div>
                <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-copper);display:block;margin-bottom:4px;">
                    {{ $product->category->label() }}
                </span>
                <h1 style="font-family:var(--font-serif);font-size:clamp(1.2rem,2.5vw,1.6rem);font-weight:700;margin-bottom:8px;line-height:1.3;">
                    {{ $product->name }}
                </h1>

                {{-- Giá + Stock trên 1 dòng --}}
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <span style="font-family:var(--font-display);font-size:26px;font-weight:700;color:var(--v-copper);">
                        {{ number_format($product->price, 0, ',', '.') }}₫
                    </span>
                    <div style="display:flex;align-items:center;gap:4px;">
                        @if($product->stock_quantity > 10)
                            <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                            <span style="font-size:11px;color:#166534;">Kho: {{ $product->stock_quantity }}</span>
                        @elseif($product->stock_quantity > 0)
                            <span style="width:6px;height:6px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                            <span style="font-size:11px;color:#92400e;">Còn {{ $product->stock_quantity }}</span>
                        @else
                            <span style="width:6px;height:6px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                            <span style="font-size:11px;color:#dc2626;">Hết hàng</span>
                        @endif
                    </div>
                </div>

                {{-- SKU nhỏ gọn --}}
                <p style="font-size:11px;color:var(--v-muted);margin-bottom:12px;">
                    SKU: <span style="font-weight:600;color:var(--v-ink);">{{ $product->sku }}</span>
                </p>

                {{-- Mô tả gọn (max 3 dòng, expandable) --}}
                @if($product->description)
                <div x-data="{ expanded: false }" style="margin-bottom:16px;border-top:1px solid var(--v-rule);padding-top:12px;">
                    <div :style="expanded ? '' : 'max-height:72px;overflow:hidden;'" style="font-size:13px;line-height:1.7;color:var(--v-ink-soft);transition:max-height 0.3s;">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                    @if(strlen($product->description) > 120)
                    <button @click="expanded = !expanded" type="button"
                        style="font-size:11px;color:var(--v-copper);background:none;border:none;cursor:pointer;padding:4px 0;font-weight:600;">
                        <span x-text="expanded ? 'Thu gọn ▲' : 'Xem thêm ▼'">Xem thêm ▼</span>
                    </button>
                    @endif
                </div>
                @endif

                {{-- Thêm giỏ hàng --}}
                @if($product->stock_quantity > 0)
                <div x-data="{ quantity: 1 }" style="border-top:1px solid var(--v-rule);padding-top:16px;">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        {{-- Chọn SL --}}
                        <div style="display:flex;align-items:center;border:1px solid var(--v-rule);height:38px;">
                            <button @click="quantity = Math.max(1, quantity - 1)" type="button"
                                style="width:36px;height:100%;border:none;background:var(--v-surface);color:var(--v-ink);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">−</button>
                            <input type="number" x-model.number="quantity" min="1" max="{{ $product->stock_quantity }}"
                                style="width:44px;height:100%;border:none;border-left:1px solid var(--v-rule);border-right:1px solid var(--v-rule);text-align:center;font-size:13px;font-weight:600;color:var(--v-ink);background:#fff;outline:none;" />
                            <button @click="quantity = Math.min({{ $product->stock_quantity }}, quantity + 1)" type="button"
                                style="width:36px;height:100%;border:none;background:var(--v-surface);color:var(--v-ink);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">+</button>
                        </div>
                        {{-- Nút thêm --}}
                        @auth
                        <button @click="addToCartDetail({{ $product->id }}, quantity, $el)" type="button"
                            class="v-btn-primary" style="flex:1;min-width:160px;height:38px;font-size:9px;">
                            <span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">add_shopping_cart</span>
                            Thêm vào giỏ hàng
                        </button>
                        @else
                        <button onclick="openAuthModal('Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.')" type="button"
                            class="v-btn-primary" style="flex:1;min-width:160px;height:38px;font-size:9px;">
                            <span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">add_shopping_cart</span>
                            Thêm vào giỏ hàng
                        </button>
                        @endauth
                    </div>
                </div>
                @else
                <div style="border-top:1px solid var(--v-rule);padding-top:16px;">
                    <button disabled style="width:100%;height:40px;background:var(--v-surface);color:var(--v-muted);border:1px solid var(--v-rule);font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:not-allowed;">
                        Sản phẩm tạm hết hàng
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Sản phẩm liên quan --}}
        @if($relatedProducts->isNotEmpty())
        <div style="margin-top:40px;">
            <h2 style="font-family:var(--font-serif);font-size:16px;font-weight:700;text-align:center;margin-bottom:20px;">Sản Phẩm Tương Tự</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;">
                @foreach($relatedProducts as $related)
                <a href="{{ route('client.shop.show', $related->slug) }}" class="v-card" style="overflow:hidden;text-decoration:none;display:block;">
                    <div style="aspect-ratio:1/1;width:100%;background:var(--v-surface);overflow:hidden;">
                        @if($related->image)
                            <img src="{{ Storage::url($related->image) }}" alt="{{ $related->name }}"
                                class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;display:block;" />
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <span class="material-symbols-outlined" style="font-size:28px;color:var(--v-muted);opacity:0.3;">inventory_2</span>
                            </div>
                        @endif
                    </div>
                    <div style="padding:10px;">
                        <h3 style="font-family:var(--font-serif);font-size:13px;font-weight:700;color:var(--v-ink);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $related->name }}</h3>
                        <span style="font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--v-copper);">
                            {{ number_format($related->price, 0, ',', '.') }}₫
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Login Modal cho guest --}}
@guest
    @include('components.auth-required-modal')
@endguest

{{-- Toast --}}
<div id="shop-toast" style="display:none;position:fixed;bottom:24px;right:24px;z-index:1000;padding:14px 24px;background:var(--v-ink);color:var(--v-cream);font-size:13px;font-weight:500;box-shadow:4px 4px 0 var(--v-copper);max-width:320px;transition:opacity 0.3s,transform 0.3s;opacity:0;transform:translateY(10px);"></div>

@push('scripts')
<script>
function addToCartDetail(productId, quantity, btn) {
    btn.disabled = true;

    fetch('{{ route("client.cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            showToast(data.message, 'success');
            updateCartBadge(data.cart_count);
        } else {
            showToast(data.message || 'Có lỗi xảy ra.', 'error');
        }
    })
    .catch(() => showToast('Không thể thêm sản phẩm.', 'error'))
    .finally(() => btn.disabled = false);
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('shop-toast');
    toast.textContent = message;
    toast.style.background = type === 'success' ? 'var(--v-ink)' : '#991b1b';
    toast.style.display = 'block';
    requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; });
    setTimeout(() => {
        toast.style.opacity = '0'; toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.style.display = 'none', 300);
    }, 3000);
}

function updateCartBadge(count) {
    document.querySelectorAll('.cart-badge-count').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}
</script>
@endpush
@endsection
