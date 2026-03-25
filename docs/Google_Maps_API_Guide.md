# 🗺️ Hướng dẫn tích hợp Google Maps API — BarberBook E-commerce

> Module phí vận chuyển sử dụng 2 APIs của Google Maps:
> - **Distance Matrix API** (server-side) — tính khoảng cách cửa hàng → địa chỉ giao
> - **Places API + Maps JavaScript API** (client-side) — autocomplete địa chỉ

---

## 1. Tạo Google Cloud Project & API Key

### Bước 1: Tạo Project
1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Nhấn **Select a project** → **New Project**
3. Đặt tên (VD: `BarberBook`) → **Create**

### Bước 2: Bật Billing
> [!IMPORTANT]
> Google Maps APIs **yêu cầu tài khoản thanh toán** (billing). Google cho **$200 credit miễn phí/tháng** — đủ cho ~40,000 lượt gọi Distance Matrix.

1. Vào **Billing** → **Link a billing account**
2. Thêm thẻ tín dụng/ghi nợ (sẽ không bị charge nếu nằm trong free tier)

### Bước 3: Enable APIs
Vào **APIs & Services** → **Library**, tìm và bật **3 APIs**:

| API | Công dụng | Nơi dùng |
|-----|-----------|----------|
| **Distance Matrix API** | Tính khoảng cách (km) giữa 2 tọa độ | `ShippingService::getDistance()` (server) |
| **Places API** | Autocomplete địa chỉ | `checkout.blade.php` (client) |
| **Maps JavaScript API** | Load Places library phía client | `checkout.blade.php` (client) |

### Bước 4: Tạo API Key
1. Vào **APIs & Services** → **Credentials** → **Create Credentials** → **API Key**
2. Copy key → dán vào `.env`:
   ```env
   GOOGLE_MAPS_API_KEY=AIzaSy...your_key_here
   ```

### Bước 5: Giới hạn API Key (khuyến nghị)
1. Nhấn **Edit** trên API key vừa tạo
2. **Application restrictions**:
   - **HTTP referrers** — thêm domain: `http://localhost:*`, `https://yourdomain.com/*`
3. **API restrictions**:
   - Chọn **Restrict key** → chọn 3 APIs ở trên
4. **Save**

---

## 2. Cấu hình `.env`

```env
# Google Maps API
GOOGLE_MAPS_API_KEY=AIzaSy...your_key_here

# Tọa độ cửa hàng (mặc định: TP.HCM)
SHOP_LATITUDE=10.762622
SHOP_LONGITUDE=106.660172

# Phí vận chuyển (đơn vị: VNĐ)
SHIPPING_BASE_FEE=15000        # Phí cơ bản
SHIPPING_PER_KM_FEE=5000       # Phí mỗi km
SHIPPING_MAX_FEE=100000        # Phí tối đa (cap)
SHIPPING_FREE_ABOVE=500000     # Miễn phí khi đơn >= 500,000₫ (0 = không miễn phí)
```

> [!TIP]
> Để tìm tọa độ cửa hàng: mở Google Maps → click phải vào vị trí → Copy tọa độ.

---

## 3. Cách hoạt động

### Luồng tính phí vận chuyển

```
Client chọn địa chỉ giao hàng (có lat/lng)
    ↓
AJAX POST → ShopController::getShippingFee()
    ↓
ShippingService::calculateFee(destLat, destLng, subtotal)
    ├── getShopCoordinates() → lấy tọa độ shop từ config
    ├── getDistance(origin, destination) → gọi Google Distance Matrix API
    │       → Trả về khoảng cách (km)
    │       → Nếu API fail → fallback 10km
    ├── feeFromDistance(distanceKm)
    │       → fee = base_fee + (distance × per_km_fee)
    │       → cap: min(fee, max_fee)
    │       → Làm tròn số nguyên
    └── Kiểm tra miễn phí: subtotal >= free_above → fee = 0
    ↓
Response JSON: { fee, distance_km, is_free }
    ↓
Frontend cập nhật checkout summary real-time
```

### Luồng Places Autocomplete (checkout)

```
User mở form "Thêm địa chỉ mới" trên trang Checkout
    ↓
Google Maps JS API load (async, chỉ khi có API key)
    ↓
Khởi tạo Autocomplete trên input #address-autocomplete
    - Giới hạn: country = 'vn', types = 'address'
    ↓
User gõ địa chỉ → Google hiện suggestions
    ↓
User chọn suggestion → place_changed event
    ├── Lấy lat/lng từ place.geometry.location
    ├── Parse address_components:
    │     street_number → số nhà
    │     route → tên đường
    │     sublocality_level_1 → Phường/Xã
    │     administrative_area_level_2 → Quận/Huyện
    │     administrative_area_level_1 → Tỉnh/Thành phố
    └── Auto-fill: address, ward, district, city, latitude, longitude
```

---

## 4. Bảng giá & Free Tier

| API | Free tier (mỗi tháng) | Giá sau free tier |
|-----|----------------------|-------------------|
| Distance Matrix | ~40,000 requests ($200 credit ÷ $5/1000) | $5 / 1,000 requests |
| Places Autocomplete | ~11,000 sessions | $17 / 1,000 sessions |
| Maps JavaScript | ~28,500 loads | $7 / 1,000 loads |

> [!NOTE]
> Với dự án demo/học tập, **$200 free credit** hàng tháng là **dư sức**. Chỉ cần đặt budget alert ở mức $10 để tránh phát sinh.

---

## 5. Fallback khi không có API Key

Hệ thống **vẫn hoạt động** khi `GOOGLE_MAPS_API_KEY=` (để trống):

| Tính năng | Hành vi |
|-----------|---------|
| **Places Autocomplete** | Không load script → user nhập tay bình thường |
| **Distance Matrix** | `ShippingService::getDistance()` trả về **fallback 10km** |
| **Phí vận chuyển** | `feeFromDistance(10.0)` → **65,000₫** (15k base + 10×5k) |
| **Lưu địa chỉ** | lat/lng = null → vẫn lưu được, phí dùng fallback |

---

## 6. Files liên quan

| File | Vai trò |
|------|---------|
| `config/services.php` | Config `google_maps.api_key` + `shipping.*` |
| `app/Services/ShippingService.php` | Business logic: calculateFee, getDistance, feeFromDistance |
| `app/Http/Controllers/Client/ShopController.php` | AJAX endpoint `getShippingFee()` |
| `resources/views/client/shop/checkout.blade.php` | Places Autocomplete UI + fetchShippingFee JS |
| `app/Http/Controllers/Client/ShippingAddressController.php` | Lưu địa chỉ (lat/lng) vào DB |
| `.env` | API key + shipping params |

---

## 7. Troubleshooting

| Vấn đề | Nguyên nhân | Giải pháp |
|--------|-------------|-----------|
| Autocomplete không hiện | API key chưa có hoặc chưa enable Places API | Kiểm tra `.env` + Google Console |
| Phí ship luôn = 65,000₫ | Distance Matrix API chưa enable hoặc key sai | Check console log + enable API |
| `ApiNotActivatedMapError` | Chưa bật Maps JavaScript API | Enable ở Google Console |
| `RefererNotAllowedMapError` | Domain chưa được whitelist | Thêm domain vào API key restrictions |
| Response `ZERO_RESULTS` | Địa chỉ không tìm được đường đi | Hệ thống tự fallback 10km |
| Phí = 0 khi đơn > 500k | Hoạt động đúng — miễn phí ship | Thay đổi `SHIPPING_FREE_ABOVE` nếu muốn |

---

## 8. Công thức phí vận chuyển

```
fee = SHIPPING_BASE_FEE + (distance_km × SHIPPING_PER_KM_FEE)
fee = min(fee, SHIPPING_MAX_FEE)

Nếu subtotal >= SHIPPING_FREE_ABOVE → fee = 0 (miễn phí)
```

**Ví dụ** với config mặc định:

| Khoảng cách | Tính | Phí |
|-------------|------|-----|
| 3 km | 15,000 + 3×5,000 = 30,000 | **30,000₫** |
| 10 km | 15,000 + 10×5,000 = 65,000 | **65,000₫** |
| 20 km | 15,000 + 20×5,000 = 115,000 → cap 100,000 | **100,000₫** |
| Đơn 600k, 5km | subtotal 600k >= 500k | **Miễn phí** |
