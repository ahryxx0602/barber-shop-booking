@extends('layouts.client')

@section('title', 'Thanh toán')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:1200px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:32px;">
            <div class="v-ornament" style="max-width:280px;margin:0 auto 14px;">Thanh toán</div>
            <h1 class="v-title" style="font-size:clamp(1.5rem,3vw,2rem);">Hoàn Tất Đơn Hàng</h1>
        </div>

        {{-- Flash messages --}}
        @if(session('error'))
            <div style="max-width:800px;margin:0 auto 20px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:14px;text-align:center;">
                {{ session('error') }}
            </div>
        @endif

        <form id="checkout-form" method="POST" action="{{ route('client.order.place') }}" x-data="checkoutForm()">
            @csrf
            <div style="display:grid;gap:32px;" class="md:grid-cols-5">
                {{-- ═══ CỘT TRÁI: Địa chỉ giao hàng (3/5) ═══ --}}
                <div class="md:col-span-3">
                    <div style="background:#fff;border:1px solid var(--v-rule);padding:24px;position:relative;">
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-br"></div>

                        <h2 style="font-family:var(--font-serif);font-size:18px;font-weight:700;margin-bottom:20px;">
                            <span class="material-symbols-outlined" style="font-size:20px;vertical-align:middle;margin-right:6px;color:var(--v-copper);">location_on</span>
                            Địa chỉ giao hàng
                        </h2>

                        {{-- Danh sách địa chỉ hiện tại --}}
                        @if($addresses->isNotEmpty())
                        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:20px;">
                            @foreach($addresses as $addr)
                            <label style="display:flex;align-items:start;gap:12px;padding:14px;border:1px solid var(--v-rule);cursor:pointer;transition:border-color 0.2s,box-shadow 0.2s;"
                                :style="selectedAddress == {{ $addr->id }} ? 'border-color:var(--v-copper);box-shadow:3px 3px 0 var(--v-copper)' : ''">
                                <input type="radio" name="shipping_address_id" value="{{ $addr->id }}"
                                    x-model.number="selectedAddress" @change="fetchShippingFee()"
                                    style="margin-top:3px;accent-color:var(--v-copper);"
                                    {{ $addr->is_default ? 'checked' : '' }} />
                                <div style="flex:1;">
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                        <strong style="font-size:14px;">{{ $addr->recipient_name }}</strong>
                                        <span style="font-size:12px;color:var(--v-muted);">{{ $addr->phone }}</span>
                                        @if($addr->is_default)
                                            <span style="font-size:8px;font-weight:700;letter-spacing:1px;text-transform:uppercase;background:var(--v-copper);color:#fff;padding:2px 6px;">Mặc định</span>
                                        @endif
                                    </div>
                                    <p style="font-size:13px;color:var(--v-ink-soft);line-height:1.5;">
                                        {{ $addr->address }}, {{ $addr->ward }}, {{ $addr->district }}, {{ $addr->city }}
                                    </p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @else
                        <p style="color:var(--v-muted);font-size:14px;margin-bottom:16px;">
                            Bạn chưa có địa chỉ giao hàng. Vui lòng thêm địa chỉ mới.
                        </p>
                        @endif

                        {{-- Nút thêm địa chỉ mới --}}
                        <button type="button" @click="showAddressForm = !showAddressForm"
                            style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;letter-spacing:1px;color:var(--v-copper);background:none;border:none;cursor:pointer;transition:color 0.2s;padding:0;"
                            onmouseover="this.style.color='var(--v-ink)'" onmouseout="this.style.color='var(--v-copper)'">
                            <span class="material-symbols-outlined" style="font-size:16px;">add_circle</span>
                            <span x-text="showAddressForm ? 'Ẩn form' : 'Thêm địa chỉ mới'">Thêm địa chỉ mới</span>
                        </button>

                        {{-- Form thêm địa chỉ --}}
                        <div x-show="showAddressForm" x-transition style="margin-top:16px;padding:20px;background:var(--v-surface);border:1px solid var(--v-rule);">
                            <div style="display:grid;gap:12px;" class="md:grid-cols-2">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Tên người nhận *</label>
                                    <input type="text" x-model="newAddress.recipient_name" class="v-input" style="font-size:13px;padding:8px 12px;" placeholder="Nguyễn Văn A" />
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Số điện thoại *</label>
                                    <input type="text" x-model="newAddress.phone" class="v-input" style="font-size:13px;padding:8px 12px;" placeholder="0901234567" />
                                </div>
                                <div class="md:col-span-2">
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Địa chỉ *</label>
                                    <input type="text" x-model="newAddress.address" class="v-input" style="font-size:13px;padding:8px 12px;" placeholder="123 Đường ABC" />
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Phường/Xã *</label>
                                    <input type="text" x-model="newAddress.ward" class="v-input" style="font-size:13px;padding:8px 12px;" placeholder="Phường 1" />
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Quận/Huyện *</label>
                                    <input type="text" x-model="newAddress.district" class="v-input" style="font-size:13px;padding:8px 12px;" placeholder="Quận 1" />
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Tỉnh/Thành phố *</label>
                                    <input type="text" x-model="newAddress.city" class="v-input" style="font-size:13px;padding:8px 12px;" placeholder="TP. Hồ Chí Minh" />
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Đặt mặc định</label>
                                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                                        <input type="checkbox" x-model="newAddress.is_default" style="accent-color:var(--v-copper);" />
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div>
                            </div>
                            <div style="margin-top:16px;display:flex;gap:10px;">
                                <button type="button" @click="saveAddress()" class="v-btn-primary v-btn-sm" :disabled="savingAddress">
                                    <span x-show="!savingAddress">Lưu địa chỉ</span>
                                    <span x-show="savingAddress">Đang lưu...</span>
                                </button>
                                <button type="button" @click="showAddressForm = false" class="v-btn-outline v-btn-sm">Hủy</button>
                            </div>
                            <p x-show="addressError" x-text="addressError" style="color:#dc2626;font-size:12px;margin-top:8px;"></p>
                        </div>

                        {{-- Ghi chú --}}
                        <div style="margin-top:24px;">
                            <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:6px;">Ghi chú đơn hàng</label>
                            <textarea name="note" class="v-textarea" rows="3" style="font-size:13px;" placeholder="Ghi chú cho đơn hàng (không bắt buộc)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ═══ CỘT PHẢI: Order Summary (2/5) ═══ --}}
                <div class="md:col-span-2">
                    <div style="background:#fff;border:1px solid var(--v-rule);padding:24px;position:sticky;top:96px;">
                        <h2 style="font-family:var(--font-serif);font-size:18px;font-weight:700;margin-bottom:20px;">
                            <span class="material-symbols-outlined" style="font-size:20px;vertical-align:middle;margin-right:6px;color:var(--v-copper);">receipt_long</span>
                            Đơn hàng
                        </h2>

                        {{-- Danh sách SP --}}
                        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;border-bottom:1px solid var(--v-rule);padding-bottom:16px;">
                            @foreach($cartItems as $item)
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:44px;height:44px;background:var(--v-surface);border:1px solid var(--v-rule);overflow:hidden;flex-shrink:0;">
                                    @if($item['product']->image)
                                        <img src="{{ Storage::url($item['product']->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                                    @endif
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:13px;font-weight:600;color:var(--v-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['product']->name }}</p>
                                    <p style="font-size:11px;color:var(--v-muted);">x{{ $item['quantity'] }}</p>
                                </div>
                                <span style="font-size:13px;font-weight:600;color:var(--v-ink);white-space:nowrap;">{{ number_format($item['total'], 0, ',', '.') }}₫</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Breakdown --}}
                        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;">
                            <div style="display:flex;justify-content:space-between;font-size:14px;">
                                <span style="color:var(--v-muted);">Tạm tính</span>
                                <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:14px;">
                                <span style="color:var(--v-muted);">Thuế VAT (10%)</span>
                                <span>{{ number_format($taxAmount, 0, ',', '.') }}₫</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:14px;">
                                <span style="color:var(--v-muted);">Phí vận chuyển</span>
                                <span id="shipping-fee-text" x-text="shippingText" style="font-weight:500;">Chọn địa chỉ</span>
                            </div>
                            <div x-show="distanceKm > 0" style="font-size:11px;color:var(--v-muted);text-align:right;">
                                Khoảng cách: <span x-text="distanceKm + ' km'"></span>
                            </div>
                        </div>

                        {{-- Tổng --}}
                        <div style="border-top:2px solid var(--v-ink);padding-top:12px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                            <span style="font-family:var(--font-serif);font-size:16px;font-weight:700;">Tổng cộng</span>
                            <span id="total-amount" x-text="totalText" style="font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--v-copper);">
                                ...
                            </span>
                        </div>

                        {{-- Phương thức thanh toán --}}
                        <div style="margin-bottom:24px;">
                            <h3 style="font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);margin-bottom:12px;">Phương thức thanh toán</h3>
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                @foreach($paymentMethods as $method)
                                <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--v-rule);cursor:pointer;transition:border-color 0.2s;"
                                    :style="paymentMethod === '{{ $method->value }}' ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.05)' : ''">
                                    <input type="radio" name="payment_method" value="{{ $method->value }}"
                                        x-model="paymentMethod" style="accent-color:var(--v-copper);"
                                        {{ $loop->first ? 'checked' : '' }} />
                                    <span style="font-size:13px;font-weight:500;">{{ $method->label() }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Nút đặt hàng --}}
                        <button type="submit" class="v-btn-primary" style="width:100%;"
                            :disabled="!selectedAddress || submitting"
                            :style="(!selectedAddress || submitting) ? 'opacity:0.5;cursor:not-allowed' : ''">
                            <span x-show="!submitting">
                                <span class="material-symbols-outlined" style="font-size:16px;margin-right:8px;vertical-align:middle;">shopping_cart_checkout</span>
                                Đặt hàng
                            </span>
                            <span x-show="submitting">Đang xử lý...</span>
                        </button>

                        @if(!$addresses->isEmpty())
                        <p x-show="!selectedAddress" style="color:#dc2626;font-size:11px;text-align:center;margin-top:8px;">
                            Vui lòng chọn địa chỉ giao hàng
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
function checkoutForm() {
    return {
        selectedAddress: {{ $addresses->where('is_default', true)->first()?->id ?? ($addresses->first()?->id ?? 'null') }},
        shippingFee: 0,
        distanceKm: 0,
        isFreeShip: false,
        paymentMethod: 'cod',
        submitting: false,
        showAddressForm: {{ $addresses->isEmpty() ? 'true' : 'false' }},
        savingAddress: false,
        addressError: '',
        newAddress: {
            recipient_name: '', phone: '', address: '', ward: '', district: '', city: '', is_default: false
        },

        subtotal: {{ $subtotal }},
        taxAmount: {{ $taxAmount }},

        get shippingText() {
            if (!this.selectedAddress) return 'Chọn địa chỉ';
            if (this.isFreeShip) return 'Miễn phí';
            if (this.shippingFee > 0) return new Intl.NumberFormat('vi-VN').format(this.shippingFee) + '₫';
            return 'Đang tính...';
        },

        get totalText() {
            const total = this.subtotal + this.taxAmount + this.shippingFee;
            return new Intl.NumberFormat('vi-VN').format(total) + '₫';
        },

        init() {
            if (this.selectedAddress) this.fetchShippingFee();
            // Ngăn submit kép
            this.$el.addEventListener('submit', (e) => {
                if (this.submitting) { e.preventDefault(); return; }
                this.submitting = true;
            });
        },

        fetchShippingFee() {
            if (!this.selectedAddress) return;
            fetch('{{ route("client.checkout.shipping-fee") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ shipping_address_id: this.selectedAddress })
            })
            .then(res => res.json())
            .then(data => {
                this.shippingFee = data.fee || 0;
                this.distanceKm = data.distance_km || 0;
                this.isFreeShip = data.is_free || false;
            })
            .catch(() => { this.shippingFee = 0; });
        },

        saveAddress() {
            this.addressError = '';
            if (!this.newAddress.recipient_name || !this.newAddress.phone || !this.newAddress.address ||
                !this.newAddress.ward || !this.newAddress.district || !this.newAddress.city) {
                this.addressError = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
                return;
            }
            this.savingAddress = true;
            fetch('{{ route("client.addresses.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(this.newAddress)
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    location.reload(); // Reload để hiện địa chỉ mới
                } else {
                    this.addressError = data.message || 'Có lỗi xảy ra.';
                }
            })
            .catch(() => { this.addressError = 'Không thể lưu địa chỉ.'; })
            .finally(() => { this.savingAddress = false; });
        }
    };
}
</script>
@endpush
@endsection
