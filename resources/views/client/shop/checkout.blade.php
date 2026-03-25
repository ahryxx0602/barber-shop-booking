@extends('layouts.client')

@section('title', 'Thanh toán')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:1100px;margin:0 auto;padding:24px 16px 40px;">
        <style>
            .checkout-grid { display:grid; gap:20px; grid-template-columns:1fr; }
            @media(min-width:768px) { .checkout-grid { grid-template-columns:3fr 2fr; } }
        </style>
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:20px;">
            <div class="v-ornament" style="max-width:200px;margin:0 auto 8px;font-size:10px;">Thanh toán</div>
            <h1 class="v-title" style="font-size:1.4rem;">Hoàn Tất Đơn Hàng</h1>
        </div>

        {{-- Flash messages --}}
        @if(session('error'))
            <div style="max-width:800px;margin:0 auto 12px;padding:8px 12px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:13px;text-align:center;">
                {{ session('error') }}
            </div>
        @endif

        <form id="checkout-form" method="POST" action="{{ route('client.order.place') }}" x-data="checkoutForm()">
            @csrf
            <div class="checkout-grid">
                {{-- ═══ CỘT TRÁI: Địa chỉ giao hàng ═══ --}}
                <div>
                    <div style="background:#fff;border:1px solid var(--v-rule);padding:16px;position:relative;">
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-br"></div>

                        <h2 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:12px;">
                            <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;color:var(--v-copper);">location_on</span>
                            Địa chỉ giao hàng
                        </h2>

                        {{-- Danh sách địa chỉ hiện tại --}}
                        @if($addresses->isNotEmpty())
                        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                            @foreach($addresses as $addr)
                            <label style="display:flex;align-items:start;gap:8px;padding:10px 12px;border:1px solid var(--v-rule);cursor:pointer;transition:border-color 0.2s,box-shadow 0.2s;"
                                :style="selectedAddress == {{ $addr->id }} ? 'border-color:var(--v-copper);box-shadow:2px 2px 0 var(--v-copper)' : ''">
                                <input type="radio" name="shipping_address_id" value="{{ $addr->id }}"
                                    x-model.number="selectedAddress"
                                    style="margin-top:3px;accent-color:var(--v-copper);"
                                    {{ $addr->is_default ? 'checked' : '' }} />
                                <div style="flex:1;">
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px;">
                                        <strong style="font-size:13px;">{{ $addr->recipient_name }}</strong>
                                        <span style="font-size:11px;color:var(--v-muted);">{{ $addr->phone }}</span>
                                        @if($addr->is_default)
                                            <span style="font-size:8px;font-weight:700;letter-spacing:1px;text-transform:uppercase;background:var(--v-copper);color:#fff;padding:2px 6px;">Mặc định</span>
                                        @endif
                                    </div>
                                    <p style="font-size:12px;color:var(--v-ink-soft);line-height:1.4;">
                                        {{ $addr->address }}, {{ $addr->ward }}, {{ $addr->district }}, {{ $addr->city }}
                                    </p>
                                </div>
                                <button type="button" @click.prevent="deleteAddress({{ $addr->id }})"
                                    title="Xóa địa chỉ"
                                    style="background:none;border:none;cursor:pointer;color:var(--v-muted);padding:2px;transition:color 0.2s;"
                                    onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='var(--v-muted)'">
                                    <span class="material-symbols-outlined" style="font-size:16px;">delete</span>
                                </button>
                            </label>
                            @endforeach
                        </div>

                        {{-- Nút xác nhận giao hàng --}}
                        <div x-show="selectedAddress" style="margin-bottom:10px;display:flex;align-items:center;gap:8px;">
                            <button type="button" @click="fetchShippingFee()" class="v-btn-primary v-btn-sm"
                                :disabled="calculatingShip"
                                style="display:inline-flex;align-items:center;gap:4px;font-size:11px;padding:6px 12px;">
                                <span class="material-symbols-outlined" style="font-size:14px;">local_shipping</span>
                                <span x-show="!calculatingShip">Tính phí giao hàng</span>
                                <span x-show="calculatingShip">Đang tính...</span>
                            </button>
                            <span x-show="distanceKm > 0" style="font-size:11px;color:var(--v-muted);">
                                📍 <strong x-text="distanceKm.toFixed(1)"></strong> km
                            </span>
                        </div>
                        @else
                        <p style="color:var(--v-muted);font-size:13px;margin-bottom:12px;">
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
                                    <input type="text" id="address-autocomplete" x-model="newAddress.address" class="v-input" style="font-size:13px;padding:8px 12px;" placeholder="Nhập địa chỉ để tìm kiếm..." autocomplete="off" />
                                    <p x-show="autocompleteActive" style="font-size:10px;color:var(--v-copper);margin-top:3px;">✓ Google Maps Autocomplete đang hoạt động</p>
                                </div>
                                {{-- Searchable Combobox: Tỉnh/Thành phố --}}
                                <div style="position:relative;">
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Tỉnh/Thành phố *</label>
                                    <div style="position:relative;">
                                        <input type="text" class="v-input" style="font-size:13px;padding:8px 12px;padding-right:30px;"
                                            :placeholder="loadingProvinces ? 'Đang tải...' : '-- Tìm Tỉnh/Thành phố --'"
                                            x-model="searchProvince"
                                            @focus="showProvinceDropdown = true"
                                            @click="showProvinceDropdown = true"
                                            @input="showProvinceDropdown = true"
                                            autocomplete="off" />
                                        <span class="material-symbols-outlined" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);font-size:16px;color:var(--v-muted);pointer-events:none;">expand_more</span>
                                    </div>
                                    <div x-show="showProvinceDropdown && filteredProvinces.length > 0" @click.outside="showProvinceDropdown = false"
                                        x-transition.opacity style="position:absolute;z-index:50;top:100%;left:0;right:0;max-height:200px;overflow-y:auto;background:#fff;border:1px solid var(--v-rule);box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                        <template x-for="p in filteredProvinces" :key="p.code">
                                            <div @click="selectProvince(p)" x-text="p.name"
                                                style="padding:8px 12px;font-size:13px;cursor:pointer;transition:background 0.15s;"
                                                onmouseover="this.style.background='var(--v-surface)'" onmouseout="this.style.background='#fff'"></div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Searchable Combobox: Quận/Huyện --}}
                                <div style="position:relative;">
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Quận/Huyện *</label>
                                    <div style="position:relative;">
                                        <input type="text" class="v-input" style="font-size:13px;padding:8px 12px;padding-right:30px;"
                                            :placeholder="loadingDistricts ? 'Đang tải...' : (districts.length ? '-- Tìm Quận/Huyện --' : 'Chọn Tỉnh trước')"
                                            :disabled="!districts.length"
                                            x-model="searchDistrict"
                                            @focus="showDistrictDropdown = true"
                                            @click="showDistrictDropdown = true"
                                            @input="showDistrictDropdown = true"
                                            autocomplete="off" />
                                        <span class="material-symbols-outlined" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);font-size:16px;color:var(--v-muted);pointer-events:none;">expand_more</span>
                                    </div>
                                    <div x-show="showDistrictDropdown && filteredDistricts.length > 0" @click.outside="showDistrictDropdown = false"
                                        x-transition.opacity style="position:absolute;z-index:50;top:100%;left:0;right:0;max-height:200px;overflow-y:auto;background:#fff;border:1px solid var(--v-rule);box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                        <template x-for="d in filteredDistricts" :key="d.code">
                                            <div @click="selectDistrict(d)" x-text="d.name"
                                                style="padding:8px 12px;font-size:13px;cursor:pointer;transition:background 0.15s;"
                                                onmouseover="this.style.background='var(--v-surface)'" onmouseout="this.style.background='#fff'"></div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Searchable Combobox: Phường/Xã --}}
                                <div style="position:relative;">
                                    <label style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Phường/Xã *</label>
                                    <div style="position:relative;">
                                        <input type="text" class="v-input" style="font-size:13px;padding:8px 12px;padding-right:30px;"
                                            :placeholder="loadingWards ? 'Đang tải...' : (wards.length ? '-- Tìm Phường/Xã --' : 'Chọn Quận trước')"
                                            :disabled="!wards.length"
                                            x-model="searchWard"
                                            @focus="showWardDropdown = true"
                                            @click="showWardDropdown = true"
                                            @input="showWardDropdown = true"
                                            autocomplete="off" />
                                        <span class="material-symbols-outlined" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);font-size:16px;color:var(--v-muted);pointer-events:none;">expand_more</span>
                                    </div>
                                    <div x-show="showWardDropdown && filteredWards.length > 0" @click.outside="showWardDropdown = false"
                                        x-transition.opacity style="position:absolute;z-index:50;top:100%;left:0;right:0;max-height:200px;overflow-y:auto;background:#fff;border:1px solid var(--v-rule);box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                        <template x-for="w in filteredWards" :key="w.code">
                                            <div @click="selectWard(w)" x-text="w.name"
                                                style="padding:8px 12px;font-size:13px;cursor:pointer;transition:background 0.15s;"
                                                onmouseover="this.style.background='var(--v-surface)'" onmouseout="this.style.background='#fff'"></div>
                                        </template>
                                    </div>
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
                        <div style="margin-top:14px;">
                            <label style="font-size:10px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:4px;">Ghi chú đơn hàng</label>
                            <textarea name="note" class="v-textarea" rows="2" style="font-size:12px;" placeholder="Ghi chú (không bắt buộc)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ═══ CỘT PHẢI: Order Summary ═══ --}}
                <div>
                    <div style="background:#fff;border:1px solid var(--v-rule);padding:16px;position:sticky;top:80px;">
                        <h2 style="font-family:var(--font-serif);font-size:15px;font-weight:700;margin-bottom:12px;">
                            <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;color:var(--v-copper);">receipt_long</span>
                            Đơn hàng
                        </h2>

                        {{-- Danh sách SP --}}
                        <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:12px;border-bottom:1px solid var(--v-rule);padding-bottom:10px;">
                            @foreach($cartItems as $item)
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:36px;height:36px;background:var(--v-surface);border:1px solid var(--v-rule);overflow:hidden;flex-shrink:0;">
                                    @if($item['product']->image)
                                        <img src="{{ Storage::url($item['product']->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                                    @endif
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:12px;font-weight:600;color:var(--v-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['product']->name }}</p>
                                    <p style="font-size:10px;color:var(--v-muted);">x{{ $item['quantity'] }}</p>
                                </div>
                                <span style="font-size:12px;font-weight:600;color:var(--v-ink);white-space:nowrap;">{{ number_format($item['total'], 0, ',', '.') }}₫</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Breakdown --}}
                        <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:12px;">
                            <div style="display:flex;justify-content:space-between;font-size:13px;">
                                <span style="color:var(--v-muted);">Tạm tính</span>
                                <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;">
                                <span style="color:var(--v-muted);">Thuế VAT (10%)</span>
                                <span>{{ number_format($taxAmount, 0, ',', '.') }}₫</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;">
                                <span style="color:var(--v-muted);">Phí vận chuyển</span>
                                <span id="shipping-fee-text" x-text="shippingText" style="font-weight:500;">Chọn địa chỉ</span>
                            </div>
                            <div x-show="distanceKm > 0" style="font-size:11px;color:var(--v-muted);text-align:right;">
                                Khoảng cách: <span x-text="distanceKm + ' km'"></span>
                            </div>
                        </div>

                        {{-- Mã giảm giá --}}
                        <div style="margin-bottom:12px;">
                            {{-- Coupon sản phẩm --}}
                            <div style="margin-bottom:8px;">
                                <label style="font-size:11px;font-weight:600;color:var(--v-muted);text-transform:uppercase;letter-spacing:0.5px;">Mã giảm giá sản phẩm</label>
                                <div style="display:flex;gap:6px;margin-top:4px;">
                                    <input type="text" x-model="productCouponInput" placeholder="Nhập mã..." :disabled="productCouponApplied"
                                        style="flex:1;padding:6px 10px;border:1px solid var(--v-rule);font-size:12px;text-transform:uppercase;font-family:var(--font-display);" />
                                    <template x-if="!productCouponApplied">
                                        <button type="button" @click="applyCoupon('product')" :disabled="productCouponLoading || !productCouponInput"
                                            style="padding:6px 12px;background:var(--v-ink);color:#fff;border:none;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;">
                                            <span x-show="!productCouponLoading">Áp dụng</span>
                                            <span x-show="productCouponLoading">...</span>
                                        </button>
                                    </template>
                                    <template x-if="productCouponApplied">
                                        <button type="button" @click="removeCoupon('product')"
                                            style="padding:6px 12px;background:#dc2626;color:#fff;border:none;font-size:11px;font-weight:700;cursor:pointer;">✕</button>
                                    </template>
                                </div>
                                <p x-show="productCouponMsg" x-text="productCouponMsg"
                                    :style="productCouponApplied ? 'color:#16a34a' : 'color:#dc2626'"
                                    style="font-size:11px;margin-top:3px;"></p>
                            </div>
                            {{-- Coupon ship --}}
                            <div>
                                <label style="font-size:11px;font-weight:600;color:var(--v-muted);text-transform:uppercase;letter-spacing:0.5px;">Mã giảm phí ship</label>
                                <div style="display:flex;gap:6px;margin-top:4px;">
                                    <input type="text" x-model="shippingCouponInput" placeholder="Nhập mã..." :disabled="shippingCouponApplied"
                                        style="flex:1;padding:6px 10px;border:1px solid var(--v-rule);font-size:12px;text-transform:uppercase;font-family:var(--font-display);" />
                                    <template x-if="!shippingCouponApplied">
                                        <button type="button" @click="applyCoupon('shipping')" :disabled="shippingCouponLoading || !shippingCouponInput"
                                            style="padding:6px 12px;background:var(--v-ink);color:#fff;border:none;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;">
                                            <span x-show="!shippingCouponLoading">Áp dụng</span>
                                            <span x-show="shippingCouponLoading">...</span>
                                        </button>
                                    </template>
                                    <template x-if="shippingCouponApplied">
                                        <button type="button" @click="removeCoupon('shipping')"
                                            style="padding:6px 12px;background:#dc2626;color:#fff;border:none;font-size:11px;font-weight:700;cursor:pointer;">✕</button>
                                    </template>
                                </div>
                                <p x-show="shippingCouponMsg" x-text="shippingCouponMsg"
                                    :style="shippingCouponApplied ? 'color:#16a34a' : 'color:#dc2626'"
                                    style="font-size:11px;margin-top:3px;"></p>
                            </div>
                        </div>

                        {{-- Discount display --}}
                        <template x-if="productDiscount > 0">
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
                                <span style="color:#16a34a;">Giảm giá SP</span>
                                <span style="color:#16a34a;font-weight:600;">-<span x-text="new Intl.NumberFormat('vi-VN').format(productDiscount)"></span>₫</span>
                            </div>
                        </template>
                        <template x-if="shippingDiscount > 0">
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
                                <span style="color:#16a34a;">Giảm phí ship</span>
                                <span style="color:#16a34a;font-weight:600;">-<span x-text="new Intl.NumberFormat('vi-VN').format(shippingDiscount)"></span>₫</span>
                            </div>
                        </template>

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

                        {{-- Hidden coupon fields --}}
                        <input type="hidden" name="product_coupon_code" :value="productCouponApplied || ''">
                        <input type="hidden" name="shipping_coupon_code" :value="shippingCouponApplied || ''">

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
{{-- Google Maps JavaScript API với Places library (chỉ load nếu có API key) --}}
@if(config('services.google_maps.api_key'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&language=vi&region=VN" async defer></script>
@endif

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
        autocompleteActive: false,
        calculatingShip: false,
        shippingCalculated: false,

        // Coupon state
        productCouponInput: '',
        productCouponApplied: null,
        productDiscount: 0,
        productCouponMsg: '',
        productCouponLoading: false,
        shippingCouponInput: '',
        shippingCouponApplied: null,
        shippingDiscount: 0,
        shippingCouponMsg: '',
        shippingCouponLoading: false,

        // Searchable combobox data
        provinces: [],
        districts: [],
        wards: [],
        selectedProvince: '',
        selectedDistrict: '',
        selectedWard: '',
        searchProvince: '',
        searchDistrict: '',
        searchWard: '',
        showProvinceDropdown: false,
        showDistrictDropdown: false,
        showWardDropdown: false,
        loadingProvinces: false,
        loadingDistricts: false,
        loadingWards: false,

        newAddress: {
            recipient_name: '', phone: '', address: '',
            ward: '', district: '', city: '',
            latitude: null, longitude: null,
            is_default: false
        },

        subtotal: {{ $subtotal }},
        taxAmount: {{ $taxAmount }},

        // Filtered lists (lọc theo từ khóa gõ)
        get filteredProvinces() {
            if (!this.searchProvince) return this.provinces;
            return this.provinces.filter(p => this.fuzzyMatch(p.name, this.searchProvince));
        },
        get filteredDistricts() {
            if (!this.searchDistrict) return this.districts;
            return this.districts.filter(d => this.fuzzyMatch(d.name, this.searchDistrict));
        },
        get filteredWards() {
            if (!this.searchWard) return this.wards;
            return this.wards.filter(w => this.fuzzyMatch(w.name, this.searchWard));
        },

        get shippingText() {
            if (!this.selectedAddress) return 'Chọn địa chỉ';
            if (this.calculatingShip) return 'Đang tính...';
            if (this.isFreeShip) return 'Miễn phí';
            if (this.shippingFee > 0) return new Intl.NumberFormat('vi-VN').format(this.shippingFee) + '₫';
            if (!this.shippingCalculated) return 'Bấm tính phí';
            return 'Miễn phí';
        },

        get totalText() {
            const total = this.subtotal + this.taxAmount + this.shippingFee - this.productDiscount - this.shippingDiscount;
            return new Intl.NumberFormat('vi-VN').format(Math.max(0, total)) + '₫';
        },

        init() {
            this.$el.addEventListener('submit', (e) => {
                if (this.submitting) { e.preventDefault(); return; }
                this.submitting = true;
            });
            this.loadProvinces();
            this.initPlacesAutocomplete();
        },

        // ═══ Searchable Combobox: Tỉnh → Quận → Phường ═══

        loadProvinces() {
            this.loadingProvinces = true;
            fetch('https://provinces.open-api.vn/api/p/')
                .then(res => res.json())
                .then(data => { this.provinces = data; })
                .catch(() => {})
                .finally(() => { this.loadingProvinces = false; });
        },

        selectProvince(p) {
            this.selectedProvince = p.code;
            this.searchProvince = p.name;
            this.newAddress.city = p.name;
            this.showProvinceDropdown = false;

            // Reset dependent
            this.districts = [];
            this.wards = [];
            this.selectedDistrict = '';
            this.selectedWard = '';
            this.searchDistrict = '';
            this.searchWard = '';
            this.newAddress.district = '';
            this.newAddress.ward = '';

            this.loadingDistricts = true;
            fetch(`https://provinces.open-api.vn/api/p/${p.code}?depth=2`)
                .then(res => res.json())
                .then(data => { this.districts = data.districts || []; })
                .catch(() => {})
                .finally(() => { this.loadingDistricts = false; });
        },

        selectDistrict(d) {
            this.selectedDistrict = d.code;
            this.searchDistrict = d.name;
            this.newAddress.district = d.name;
            this.showDistrictDropdown = false;

            // Reset dependent
            this.wards = [];
            this.selectedWard = '';
            this.searchWard = '';
            this.newAddress.ward = '';

            this.loadingWards = true;
            fetch(`https://provinces.open-api.vn/api/d/${d.code}?depth=2`)
                .then(res => res.json())
                .then(data => { this.wards = data.wards || []; })
                .catch(() => {})
                .finally(() => { this.loadingWards = false; });
        },

        selectWard(w) {
            this.selectedWard = w.code;
            this.searchWard = w.name;
            this.newAddress.ward = w.name;
            this.showWardDropdown = false;
        },

        // ═══ Google Places Autocomplete ═══

        initPlacesAutocomplete() {
            const tryInit = () => {
                if (typeof google === 'undefined' || !google.maps || !google.maps.places) return false;

                const input = document.getElementById('address-autocomplete');
                if (!input) return false;

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    componentRestrictions: { country: 'vn' },
                    fields: ['address_components', 'geometry', 'formatted_address'],
                    types: ['address']
                });

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;

                    // Lấy lat/lng
                    this.newAddress.latitude = place.geometry.location.lat();
                    this.newAddress.longitude = place.geometry.location.lng();

                    // Parse address components
                    let streetNumber = '';
                    let route = '';
                    let wardName = '';
                    let districtName = '';
                    let cityName = '';

                    for (const component of place.address_components) {
                        const types = component.types;
                        if (types.includes('street_number')) {
                            streetNumber = component.long_name;
                        } else if (types.includes('route')) {
                            route = component.long_name;
                        } else if (types.includes('sublocality_level_1') || types.includes('sublocality')) {
                            // Phường/Xã
                            wardName = component.long_name;
                        } else if (types.includes('administrative_area_level_2')) {
                            // Quận/Huyện
                            districtName = component.long_name;
                        } else if (types.includes('administrative_area_level_1')) {
                            // Tỉnh/Thành phố
                            cityName = component.long_name;
                        }
                    }

                    // Cập nhật street address
                    this.newAddress.address = streetNumber ? `${streetNumber} ${route}` : (route || place.formatted_address);

                    // Tự động chọn đúng dropdown dựa trên tên từ Google
                    if (cityName) this.autoSelectProvince(cityName, districtName, wardName);
                });

                this.autocompleteActive = true;
                return true;
            };

            // Thử ngay, nếu chưa load → retry mỗi 500ms (tối đa 10s)
            if (!tryInit()) {
                let attempts = 0;
                const interval = setInterval(() => {
                    attempts++;
                    if (tryInit() || attempts >= 20) clearInterval(interval);
                }, 500);
            }
        },

        /**
         * Tự động chọn Tỉnh → Quận → Phường từ tên Google Places trả về.
         * So sánh fuzzy (includes) vì tên Google có thể khác chút so với API provinces.
         */
        async autoSelectProvince(cityName, districtName, wardName) {
            // Tìm tỉnh
            const prov = this.provinces.find(p =>
                this.fuzzyMatch(p.name, cityName)
            );
            if (!prov) {
                this.newAddress.city = cityName;
                this.newAddress.district = districtName;
                this.newAddress.ward = wardName;
                this.searchProvince = cityName;
                this.searchDistrict = districtName;
                this.searchWard = wardName;
                return;
            }

            this.selectedProvince = prov.code;
            this.searchProvince = prov.name;
            this.newAddress.city = prov.name;

            // Load quận
            try {
                const res = await fetch(`https://provinces.open-api.vn/api/p/${prov.code}?depth=2`);
                const data = await res.json();
                this.districts = data.districts || [];
            } catch { return; }

            if (districtName) {
                const dist = this.districts.find(d =>
                    this.fuzzyMatch(d.name, districtName)
                );
                if (dist) {
                    this.selectedDistrict = dist.code;
                    this.searchDistrict = dist.name;
                    this.newAddress.district = dist.name;

                    // Load phường
                    try {
                        const res = await fetch(`https://provinces.open-api.vn/api/d/${dist.code}?depth=2`);
                        const data = await res.json();
                        this.wards = data.wards || [];
                    } catch { return; }

                    if (wardName) {
                        const w = this.wards.find(w =>
                            this.fuzzyMatch(w.name, wardName)
                        );
                        if (w) {
                            this.selectedWard = w.code;
                            this.searchWard = w.name;
                            this.newAddress.ward = w.name;
                        }
                    }
                }
            }
        },

        /**
         * So sánh fuzzy: loại bỏ dấu + lowercase, kiểm tra includes.
         */
        fuzzyMatch(a, b) {
            const normalize = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/đ/g, 'd');
            return normalize(a).includes(normalize(b)) || normalize(b).includes(normalize(a));
        },

        // ═══ Shipping Fee & Save Address ═══

        fetchShippingFee() {
            if (!this.selectedAddress) return;
            this.calculatingShip = true;
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
            .catch(() => { this.shippingFee = 0; })
            .finally(() => { this.calculatingShip = false; this.shippingCalculated = true; });
        },

        async saveAddress() {
            this.addressError = '';
            if (!this.newAddress.recipient_name || !this.newAddress.phone || !this.newAddress.address ||
                !this.newAddress.ward || !this.newAddress.district || !this.newAddress.city) {
                this.addressError = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
                return;
            }
            this.savingAddress = true;

            // Tự động lấy tọa độ bằng Nominatim (OpenStreetMap) nếu chưa có lat/lng
            if (!this.newAddress.latitude || !this.newAddress.longitude) {
                try {
                    const fullAddr = `${this.newAddress.address}, ${this.newAddress.ward}, ${this.newAddress.district}, ${this.newAddress.city}, Vietnam`;
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddr)}&limit=1&countrycodes=vn`);
                    const data = await res.json();
                    if (data.length > 0) {
                        this.newAddress.latitude = parseFloat(data[0].lat);
                        this.newAddress.longitude = parseFloat(data[0].lon);
                    }
                } catch (e) {
                    // Nếu geocoding fail → vẫn lưu, shipping dùng fallback 10km
                    console.warn('Geocoding failed, using fallback', e);
                }
            }

            fetch('{{ route("client.addresses.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(this.newAddress)
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    location.reload();
                } else {
                    this.addressError = data.message || 'Có lỗi xảy ra.';
                }
            })
            .catch(() => { this.addressError = 'Không thể lưu địa chỉ.'; })
            .finally(() => { this.savingAddress = false; });
        },

        deleteAddress(id) {
            if (!confirm('Bạn có chắc muốn xóa địa chỉ này?')) return;
            fetch(`/client/addresses/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Không thể xóa địa chỉ.');
                }
            })
            .catch(() => { alert('Lỗi kết nối.'); });
        },

        // ═══ Coupon ═══
        applyCoupon(type) {
            const isProduct = type === 'product';
            const code = isProduct ? this.productCouponInput : this.shippingCouponInput;
            if (!code) return;

            if (isProduct) { this.productCouponLoading = true; this.productCouponMsg = ''; }
            else { this.shippingCouponLoading = true; this.shippingCouponMsg = ''; }

            fetch('{{ route("client.checkout.apply-coupon") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ code: code, applies_to: type, shipping_fee: this.shippingFee })
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (isProduct) {
                    this.productCouponLoading = false;
                    if (ok && data.success) {
                        this.productCouponApplied = data.code;
                        this.productDiscount = data.discount;
                        this.productCouponMsg = data.message;
                    } else {
                        this.productCouponMsg = data.message || 'Mã không hợp lệ.';
                    }
                } else {
                    this.shippingCouponLoading = false;
                    if (ok && data.success) {
                        this.shippingCouponApplied = data.code;
                        this.shippingDiscount = data.discount;
                        this.shippingCouponMsg = data.message;
                    } else {
                        this.shippingCouponMsg = data.message || 'Mã không hợp lệ.';
                    }
                }
            })
            .catch(() => {
                if (isProduct) { this.productCouponLoading = false; this.productCouponMsg = 'Lỗi kết nối.'; }
                else { this.shippingCouponLoading = false; this.shippingCouponMsg = 'Lỗi kết nối.'; }
            });
        },

        removeCoupon(type) {
            if (type === 'product') {
                this.productCouponApplied = null;
                this.productDiscount = 0;
                this.productCouponMsg = '';
                this.productCouponInput = '';
            } else {
                this.shippingCouponApplied = null;
                this.shippingDiscount = 0;
                this.shippingCouponMsg = '';
                this.shippingCouponInput = '';
            }
        }
    };
}
</script>
@endpush
@endsection
