@extends('layouts.client')

@section('title', 'Cửa hàng')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:1200px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:32px;">
            <div class="v-ornament" style="max-width:280px;margin:0 auto 14px;">Cửa hàng</div>
            <h1 class="v-title" style="font-size:clamp(1.75rem,3vw,2.5rem);">Sản Phẩm Chăm Sóc</h1>
            <p style="color:var(--v-muted);max-width:400px;margin:10px auto 0;font-size:14px;line-height:1.6;">
                Các sản phẩm chăm sóc tóc chất lượng cao, được tuyển chọn kỹ lưỡng.
            </p>
        </div>

        {{-- Filter Bar --}}
        <div style="max-width:640px;margin:0 auto 28px;">
            <form method="GET" action="{{ route('client.shop.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <div style="position:relative;flex:1;min-width:180px;">
                    <span class="material-symbols-outlined" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--v-muted);font-size:18px;">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm sản phẩm..."
                        class="v-input" style="padding:8px 16px 8px 40px;font-size:14px;" />
                </div>
                <div style="position:relative;min-width:180px;">
                    <select name="category" onchange="this.form.submit()"
                        class="v-input" style="padding:8px 32px 8px 16px;font-size:14px;cursor:pointer;appearance:none;-webkit-appearance:none;">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}" {{ request('category') == $cat->value ? 'selected' : '' }}>
                                {{ $cat->label() }}
                            </option>
                        @endforeach
                    </select>
                    <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--v-muted);font-size:14px;">▾</span>
                </div>
            </form>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div style="max-width:640px;margin:0 auto 20px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:14px;text-align:center;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="max-width:640px;margin:0 auto 20px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:14px;text-align:center;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Product Grid --}}
        @if($products->isEmpty())
            <div style="text-align:center;padding:48px 0;">
                <span class="material-symbols-outlined" style="font-size:40px;color:var(--v-muted);opacity:0.4;display:block;margin-bottom:10px;">inventory_2</span>
                <p style="color:var(--v-muted);font-size:14px;">Không tìm thấy sản phẩm nào.</p>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:24px;">
                @foreach($products as $product)
                <div class="v-card" style="overflow:hidden;position:relative;" id="product-card-{{ $product->id }}">
                    <a href="{{ route('client.shop.show', $product->slug) }}" style="text-decoration:none;display:block;">
                        {{-- Ảnh sản phẩm --}}
                        <div style="aspect-ratio:1/1;width:100%;background:var(--v-surface);overflow:hidden;">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                    class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;display:block;" />
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                    <span class="material-symbols-outlined" style="font-size:48px;color:var(--v-muted);opacity:0.3;">inventory_2</span>
                                </div>
                            @endif
                        </div>

                        {{-- Category Badge --}}
                        <div style="position:absolute;top:12px;left:12px;">
                            <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;background:var(--v-ink);color:var(--v-cream);padding:4px 10px;">
                                {{ $product->category->label() }}
                            </span>
                        </div>

                        {{-- Info --}}
                        <div style="padding:16px;">
                            <h3 style="font-family:var(--font-serif);font-size:16px;font-weight:700;color:var(--v-ink);margin-bottom:6px;line-height:1.3;">
                                {{ $product->name }}
                            </h3>
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span style="font-family:var(--font-display);font-size:20px;font-weight:600;color:var(--v-copper);">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </span>
                                @if($product->stock_quantity <= 0)
                                    <span style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#dc2626;">Hết hàng</span>
                                @elseif($product->stock_quantity <= 5)
                                    <span style="font-size:10px;color:var(--v-muted);">Còn {{ $product->stock_quantity }}</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    {{-- Nút Thêm giỏ hàng --}}
                    @if($product->stock_quantity > 0)
                    <div style="padding:0 16px 16px;">
                        <button onclick="addToCart({{ $product->id }}, this)" type="button"
                            class="v-btn-primary v-btn-sm" style="width:100%;font-size:9px;">
                            <span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">add_shopping_cart</span>
                            Thêm giỏ hàng
                        </button>
                    </div>
                    @else
                    <div style="padding:0 16px 16px;">
                        <button disabled style="width:100%;height:40px;background:var(--v-surface);color:var(--v-muted);border:1px solid var(--v-rule);font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:not-allowed;">
                            Hết hàng
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div style="margin-top:32px;display:flex;justify-content:center;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>

{{-- Toast Notification --}}
<div id="shop-toast" style="display:none;position:fixed;bottom:24px;right:24px;z-index:1000;padding:14px 24px;background:var(--v-ink);color:var(--v-cream);font-size:13px;font-weight:500;box-shadow:4px 4px 0 var(--v-copper);max-width:320px;transition:opacity 0.3s,transform 0.3s;opacity:0;transform:translateY(10px);">
</div>

@push('scripts')
<script>
// Thêm sản phẩm vào giỏ hàng AJAX
function addToCart(productId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:14px;animation:spin 1s linear infinite;">progress_activity</span>';

    fetch('{{ route("client.cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            showToast(data.message, 'success');
            // Cập nhật cart badge trên nav
            updateCartBadge(data.cart_count);
        } else {
            showToast(data.message || 'Có lỗi xảy ra.', 'error');
        }
    })
    .catch(() => showToast('Không thể thêm sản phẩm.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">add_shopping_cart</span> Thêm giỏ hàng';
    });
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('shop-toast');
    toast.textContent = message;
    toast.style.background = type === 'success' ? 'var(--v-ink)' : '#991b1b';
    toast.style.display = 'block';
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
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
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
@endsection
