# Giám sát hệ thống & Ghi log (Monitoring & Logging)

## Tổng quan

Hệ thống monitoring & logging giúp theo dõi hoạt động của ứng dụng, phát hiện lỗi, và kiểm tra hành vi người dùng. Laravel sử dụng thư viện **Monolog** để xử lý log.

---

## 1. Logging Channels (Kênh ghi log)

**File:** `config/logging.php`

| Channel     | Mô tả                          | File log                         | Giữ lại |
|-------------|--------------------------------|----------------------------------|---------|
| `daily`     | Log chung (mặc định)           | `storage/logs/laravel-DATE.log`  | 14 ngày |
| `booking`   | Log riêng cho booking          | `storage/logs/booking-DATE.log`  | 30 ngày |
| `security`  | Log riêng cho bảo mật          | `storage/logs/security-DATE.log` | 60 ngày |

### Cách hoạt động
- Laravel dùng **daily driver** → tạo file log mới mỗi ngày, giúp dễ quản lý dung lượng.
- Mỗi channel ghi vào file riêng → dễ tìm kiếm khi debug.
- Sử dụng: `Log::channel('booking')->info('message', ['data'])`.

---

## 2. LogActivity Middleware

**File:** `app/Http/Middleware/LogActivity.php`

### Cách hoạt động
1. Middleware chạy **sau** mỗi request (POST/PUT/PATCH/DELETE).
2. Thu thập: user ID, role, HTTP method, URL, IP, status code, thời gian xử lý.
3. Ghi vào log channel mặc định (`daily`).

### Luồng xử lý
```
Request → Controller → Response
                              ↓
                    LogActivity ghi log
```

### Ví dụ log
```
[2026-03-23 10:00:00] local.INFO: HTTP Request {"user_id":1,"role":"admin","method":"POST","url":"/admin/services","ip":"127.0.0.1","status":302,"duration":"120ms"}
```

---

## 3. BookingService Logging

**File:** `app/Services/BookingService.php`

Các sự kiện được ghi log vào channel `booking`:
- **Booking created** — khi tạo booking mới
- **Booking confirmed** — khi barber xác nhận
- **Booking rejected** — khi barber từ chối (kèm lý do)
- **Booking completed** — khi hoàn thành dịch vụ
- **Booking cancelled** — khi khách hàng huỷ (kèm lý do)

---

## 4. Artisan Commands

### `logs:cleanup` — Dọn dẹp log cũ
**File:** `app/Console/Commands/CleanupLogs.php`

```bash
php artisan logs:cleanup           # Xoá log cũ hơn 30 ngày (mặc định)
php artisan logs:cleanup --days=7  # Xoá log cũ hơn 7 ngày
```

**Cách hoạt động:**
1. Quét thư mục `storage/logs/`.
2. Tìm file có ngày trong tên (vd: `laravel-2026-03-01.log`).
3. So sánh ngày file với ngày hiện tại.
4. Xoá file nếu quá số ngày cho phép.
5. **Lịch tự động:** Chạy mỗi Chủ nhật lúc 02:00.

### `bookings:expire` — Tự động huỷ booking quá hạn
**File:** `app/Console/Commands/ExpireBookings.php`

```bash
php artisan bookings:expire              # Huỷ booking pending > 30 phút (mặc định)
php artisan bookings:expire --minutes=15 # Huỷ booking pending > 15 phút
```

**Cách hoạt động:**
1. Tìm tất cả booking có `status = Pending` và `created_at ≤ 30 phút trước`.
2. Cập nhật status → `Cancelled`, ghi `cancel_reason`.
3. Giải phóng time slot → `Available`.
4. Ghi log vào channel `booking`.
5. **Lịch tự động:** Chạy mỗi 5 phút.

---

## 5. Admin Log Viewer (Trang xem log)

**Route:** `GET /admin/system/logs`
**Controller:** `SystemLogController`
**View:** `admin/system/logs.blade.php`

### Tính năng
- **Filter theo channel:** chọn xem log `laravel`, `booking`, hoặc `security`.
- **Filter theo level:** INFO, WARNING, ERROR, DEBUG.
- Hiển thị tối đa **200 entries mới nhất**, sắp xếp mới → cũ.
- Color-coded badges theo level.

---

## 6. Scheduled Tasks (Lịch tự động)

**File:** `routes/console.php`

| Command            | Lịch chạy                 |
|--------------------|---------------------------|
| `slots:generate`   | Hằng ngày lúc 00:30       |
| `bookings:expire`  | Mỗi 5 phút                |
| `logs:cleanup`     | Mỗi Chủ nhật lúc 02:00    |

### Kích hoạt Scheduler
Thêm cron job trên server:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
