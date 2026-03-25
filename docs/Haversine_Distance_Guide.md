# 📐 Hướng dẫn tính khoảng cách — Công thức Haversine

> **Dự án BarberBook** dùng công thức Haversine để tính khoảng cách giữa cửa hàng và địa chỉ giao hàng.
> **Hoàn toàn miễn phí**, không cần Google Maps API key.

---

## Tại sao dùng Haversine?

| Tiêu chí | Google Maps Distance Matrix | Haversine Formula |
|---|---|---|
| Chi phí | Trả phí sau $200/tháng | **Miễn phí** |
| Cần API key | ✅ Có | ❌ Không |
| Loại khoảng cách | Đường đi thực tế (đường bộ) | Đường chim bay (great-circle) |
| Độ chính xác | Rất cao (~100%) | Tốt (~80-95% so với đường đi thực) |
| Tốc độ | Phụ thuộc network (API call) | **Cực nhanh** (tính toán local) |
| Offline | ❌ | ✅ Hoạt động offline |

**Kết luận**: Với bài toán tính phí ship cho đồ án tốt nghiệp, Haversine là đủ chính xác và hoàn toàn miễn phí.

---

## Công thức Haversine

### Lý thuyết

Haversine formula tính **khoảng cách đường tròn lớn** (great-circle distance) giữa 2 điểm trên bề mặt hình cầu (Trái Đất) dựa trên tọa độ kinh/vĩ độ.

### Công thức toán học

```
a = sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlng/2)
c = 2 × atan2(√a, √(1-a))
d = R × c
```

Trong đó:
- `R` = Bán kính Trái Đất = **6.371 km**
- `lat1, lng1` = Tọa độ điểm 1 (đơn vị **radian**)
- `lat2, lng2` = Tọa độ điểm 2 (đơn vị **radian**)
- `Δlat` = `lat2 - lat1`
- `Δlng` = `lng2 - lng1`
- `d` = **Khoảng cách** (km)

### Minh họa

```
    Điểm A (Cửa hàng)         Điểm B (Khách hàng)
    lat: 16.047079             lat: 21.028511
    lng: 108.206230            lng: 105.804817
         \                          /
          \    Đường tròn lớn      /
           \  (great circle)      /
            \___________________/
                  d = R × c
                  ≈ 764 km
```

---

## Implementation trong BarberBook

### File: `app/Services/ShippingService.php`

```php
/**
 * Tính khoảng cách đường chim bay (km) bằng công thức Haversine.
 * Hoàn toàn miễn phí, không cần API.
 */
public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $earthRadius = 6371; // km

    // Chuyển độ → radian
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    // Công thức Haversine
    $a = sin($dLat / 2) ** 2
       + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
       * sin($dLng / 2) ** 2;

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return round($earthRadius * $c, 2); // km, 2 chữ số thập phân
}
```

### Giải thích từng bước

| Bước | Code | Ý nghĩa |
|---|---|---|
| 1 | `deg2rad()` | Chuyển tọa độ từ **độ** (degrees) sang **radian** |
| 2 | `sin($dLat/2)**2` | Tính Haversine của hiệu vĩ độ |
| 3 | `cos(lat1)*cos(lat2)*sin($dLng/2)**2` | Tính Haversine có bù trừ theo vĩ độ |
| 4 | `$a = ...` | Tổng 2 thành phần Haversine |
| 5 | `atan2(√a, √(1-a))` | Tính nửa góc tâm (angular distance) |
| 6 | `$c = 2 × ...` | Góc tâm đầy đủ |
| 7 | `R × c` | Nhân bán kính Trái Đất → **khoảng cách km** |

---

## Luồng tính phí ship

```
Khách chọn địa chỉ giao hàng
         │
         ▼
Nominatim geocode: địa chỉ → (lat, lng)
         │
         ▼
ShippingService::calculateFee(destLat, destLng, subtotal)
         │
         ├── getShopCoordinates() → (shopLat, shopLng)
         │
         ├── getDistance(origin, destination)
         │       │
         │       ├── Có Google API key? → Gọi Distance Matrix (chính xác hơn)
         │       │
         │       └── Không có key (mặc định) → haversineDistance() ← MIỄN PHÍ
         │
         ├── feeFromDistance(distanceKm)
         │       │
         │       ├── ≤ 20km → Miễn phí ship
         │       │
         │       └── > 20km → base_fee + (km_vượt × per_km_fee), cap max_fee
         │
         └── subtotal ≥ 500k? → Miễn phí ship (ghi đè)
         │
         ▼
Return: { fee, distance_km, is_free }
```

---

## Bảng phí vận chuyển

| Khoảng cách | Phí ship | Ghi chú |
|---|---|---|
| 0 – 20 km | **0đ** (Miễn phí) | Trong bán kính free_within_km |
| 21 km | 12.000đ | 10k + (1 × 2k) |
| 25 km | 20.000đ | 10k + (5 × 2k) |
| 30 km | 30.000đ | 10k + (10 × 2k) |
| 40 km | 50.000đ | **Cap** max_fee |
| > 40 km | 50.000đ | Tối đa 50k |
| Bất kỳ (đơn ≥ 500k) | **0đ** | Miễn phí ship |

---

## Cấu hình `.env`

```env
# Tọa độ cửa hàng (Đà Nẵng)
SHOP_LATITUDE=16.047079
SHOP_LONGITUDE=108.206230

# Phí vận chuyển
SHIPPING_FREE_WITHIN_KM=20    # Miễn phí trong bán kính (km)
SHIPPING_BASE_FEE=10000       # Phí cơ bản (đ)
SHIPPING_PER_KM_FEE=2000      # Phí mỗi km vượt (đ)
SHIPPING_MAX_FEE=50000         # Phí tối đa (đ)
SHIPPING_FREE_ABOVE=500000     # Miễn phí khi đơn ≥ X đ
```

---

## Ví dụ thực tế

### 1. Đà Nẵng → Hội An (~30km)
```
Haversine: ~25 km (đường chim bay)
Phí ship: 10.000 + (5 × 2.000) = 20.000đ
```

### 2. Đà Nẵng → Huế (~100km)
```
Haversine: ~88 km (đường chim bay)
Phí ship: 10.000 + (68 × 2.000) = 146.000 → cap 50.000đ
```

### 3. Đà Nẵng → Đà Nẵng (quận khác, ~5km)
```
Haversine: ~5 km
Phí ship: **Miễn phí** (≤ 20km)
```

### 4. Đà Nẵng → Hà Nội (~764km) nhưng đơn ≥ 500k
```
Haversine: ~764 km
Phí ship: **Miễn phí** (đơn ≥ 500.000đ)
```

---

## Geocoding: Nominatim (OpenStreetMap)

Để tính Haversine cần tọa độ (lat, lng). Dự án dùng **Nominatim** (miễn phí) để geocode:

```
Địa chỉ text → Nominatim API → { lat, lng }
```

**API endpoint:**
```
https://nominatim.openstreetmap.org/search?q={address}&format=json&limit=1
```

**Trong checkout:** Khi khách xác nhận địa chỉ, client gọi Nominatim geocode để lấy lat/lng, sau đó gọi AJAX `getShippingFee` để tính phí ship.

---

## So sánh kết quả

| Tuyến đường | Google Maps (đường bộ) | Haversine (chim bay) | Sai lệch |
|---|---|---|---|
| ĐN → Hội An | ~30 km | ~25 km | ~17% |
| ĐN → Huế | ~105 km | ~88 km | ~16% |
| ĐN → HCM | ~960 km | ~764 km | ~20% |
| Nội thành ĐN | ~8 km | ~5 km | ~37% |

> **Lưu ý**: Haversine cho khoảng cách ngắn hơn thực tế (vì đo đường chim bay).
> Với phí ship cap 50k và free ≤20km, sai lệch này không ảnh hưởng nhiều đến phí cuối cùng.
