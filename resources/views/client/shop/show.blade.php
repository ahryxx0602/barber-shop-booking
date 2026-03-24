@extends('layouts.client')

@section('title', $product->name)

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:1200px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">
        {{-- Breadcrumb --}}
        <div style="margin-bottom:24px;display:flex;align-items:center;gap:8px;font-size:12px;">
            <a href="{{ route('client.shop.index') }}" style="color:var(--v-muted);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">Cửa hàng</a>
            <span style="color:var(--v-muted);">›</span>
            <a href="{{ route('client.shop.index', ['category' => $product->category->value]) }}" style="color:var(--v-muted);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">{{ $product->category->label() }}</a>
            <span style="color:var(--v-muted);">›</span>
            <span style="color:var(--v-ink);font-weight:500;">{{ $product->name }}</span>
        </div>

        {{-- Product Detail --}}
        <div style="display:grid;gap:40px;" class="md:grid-cols-2">
            {{-- Ảnh --}}
            <div style="position:relative;">
                <div style="aspect-ratio:1/1;width:100%;background:var(--v-surface);border:1px solid var(--v-rule);box-shadow:6px 6px 0 var(--v-copper);overflow:hidden;">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                            style="width:100%;height:100%;object-fit:cover;display:block;" />
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                            <span class="material-symbols-outlined" style="font-size:80px;color:var(--v-muted);opacity:0.3;">inventory_2</span>
                        </div>
                    @endif
                </div>
                <div class="corner corner-tl"></div>
                <div class="corner corner-br"></div>
            </div>

            {{-- Thông tin --}}
            <div>
                <span style="font-size:9px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--v-copper);margin-bottom:8px;display:block;">
                    {{ $product->category->label() }}
                </span>
                <h1 class="v-title" style="font-size:clamp(1.5rem,3vw,2.25rem);margin-bottom:16px;">
                    {{ $product->name }}
                </h1>

                {{-- Giá --}}
                <div style="margin-bottom:20px;">
                    <span style="font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--v-copper);">
                        {{ number_format($product->price, 0, ',', '.') }}₫
                    </span>
                </div>

                {{-- Trạng thái stock --}}
                <div style="margin-bottom:24px;display:flex;align-items:center;gap:8px;">
                    @if($product->stock_quantity > 10)
                        <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                        <span style="font-size:13px;color:#166534;">Còn hàng ({{ $product->stock_quantity }})</span>
                    @elseif($product->stock_quantity > 0)
                        <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                        <span style="font-size:13px;color:#92400e;">Chỉ còn {{ $product->stock_quantity }} sản phẩm</span>
                    @else
                        <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                        <span style="font-size:13px;color:#dc2626;">Hết hàng</span>
                    @endif
                </div>

                {{-- SKU --}}
                <div style="margin-bottom:24px;font-size:12px;color:var(--v-muted);">
                    Mã SP: <span style="font-weight:600;color:var(--v-ink);">{{ $product->sku }}</span>
                </div>

                {{-- Mô tả --}}
                @if($product->description)
                <div style="margin-bottom:28px;border-top:1px solid var(--v-rule);padding-top:20px;">
                    <h3 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:10px;">Mô tả sản phẩm</h3>
                    <div style="font-size:14px;line-height:1.8;color:var(--v-ink-soft);">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
                @endif

                {{-- Thêm giỏ hàng --}}
                @if($product->stock_quantity > 0)
                <div x-data="{ quantity: 1 }" style="border-top:1px solid var(--v-rule);padding-top:24px;">
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
                        {{-- Chọn SL --}}
                        <div style="display:flex;align-items:center;border:1px solid var(--v-rule);height:44px;">
                            <button @click="quantity = Math.max(1, quantity - 1)" type="button"
                                style="width:44px;height:100%;border:none;background:var(--v-surface);color:var(--v-ink);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                onmouseover="this.style.background='var(--v-parchment)'" onmouseout="this.style.background='var(--v-surface)'">−</button>
                            <input type="number" x-model.number="quantity" min="1" max="{{ $product->stock_quantity }}"
                                style="width:56px;height:100%;border:none;border-left:1px solid var(--v-rule);border-right:1px solid var(--v-rule);text-align:center;font-size:14px;font-weight:600;color:var(--v-ink);background:#fff;outline:none;" />
                            <button @click="quantity = Math.min({{ $product->stock_quantity }}, quantity + 1)" type="button"
                                style="width:44px;height:100%;border:none;background:var(--v-surface);color:var(--v-ink);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                onmouseover="this.style.background='var(--v-parchment)'" onmouseout="this.style.background='var(--v-surface)'">+</button>
                        </div>

                        {{-- Nút thêm --}}
                        <button @click="addToCartDetail({{ $product->id }}, quantity, $el)" type="button"
                            class="v-btn-primary" style="flex:1;min-width:200px;">
                            <span class="material-symbols-outlined" style="font-size:16px;margin-right:8px;">add_shopping_cart</span>
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
                @else
                <div style="border-top:1px solid var(--v-rule);padding-top:24px;">
                    <button disabled style="width:100%;height:52px;background:var(--v-surface);color:var(--v-muted);border:1px solid var(--v-rule);font-size:10px;font-weight:600;letter-spacing:3px;text-transform:uppercase;cursor:not-allowed;">
                        Sản phẩm tạm hết hàng
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Sản phẩm liên quan --}}
        @if($relatedProducts->isNotEmpty())
        <div style="margin-top:64px;">
            <div class="v-ornament" style="max-width:280px;margin:0 auto 24px;">Liên quan</div>
            <h2 class="v-title-sm" style="text-align:center;margin-bottom:32px;">Sản Phẩm Tương Tự</h2>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:20px;">
                @foreach($relatedProducts as $related)
                <a href="{{ route('client.shop.show', $related->slug) }}" class="v-card" style="overflow:hidden;text-decoration:none;display:block;">
                    <div style="aspect-ratio:1/1;width:100%;background:var(--v-surface);overflow:hidden;">
                        @if($related->image)
                            <img src="{{ Storage::url($related->image) }}" alt="{{ $related->name }}"
                                class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;display:block;" />
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <span class="material-symbols-outlined" style="font-size:36px;color:var(--v-muted);opacity:0.3;">inventory_2</span>
                            </div>
                        @endif
                    </div>
                    <div style="padding:12px;">
                        <h3 style="font-family:var(--font-serif);font-size:14px;font-weight:700;color:var(--v-ink);margin-bottom:4px;">{{ $related->name }}</h3>
                        <span style="font-family:var(--font-display);font-size:16px;font-weight:600;color:var(--v-copper);">
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
