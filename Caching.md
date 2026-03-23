# Caching & Background Jobs

## Tổng quan

Module này giúp **tăng hiệu suất** bằng cách cache dữ liệu hay truy vấn, và **xử lý bất đồng bộ** các tác vụ nặng như gửi notification qua Queue Jobs.

---

## 1. CacheService — Quản lý cache tập trung

**File:** `app/Services/CacheService.php`

### Cách hoạt động

Laravel Cache sử dụng driver `file` (mặc định) để lưu trữ dữ liệu tạm:

```
Request → CacheService → Cache có sẵn?
                            ├── Có → Trả về từ cache (nhanh)
                            └── Không → Query DB → Lưu cache → Trả về
```

### Dữ liệu được cache

| Key               | TTL      | Nội dung                     |
|--------------------|----------|------------------------------|
| `active_services`  | 1 giờ    | Danh sách dịch vụ hoạt động  |
| `active_barbers`   | 30 phút  | Danh sách thợ hoạt động      |
| `report_*`         | 15 phút  | Dữ liệu báo cáo             |

### Cache Invalidation (Xoá cache khi dữ liệu thay đổi)

Khi **tạo/sửa/xoá** dịch vụ hay thợ cắt, cache tự động bị xoá:
- `ServiceService` → gọi `clearServiceCache()` sau mỗi thay đổi
- `BarberService` → gọi `clearBarberCache()` sau mỗi thay đổi

---

## 2. Background Jobs — Queue

**File:** `app/Jobs/SendBookingNotificationJob.php`

### Cách hoạt động

```
User action → Event → Listener → Dispatch Job
                                        ↓
                              Queue (chạy background)
                                        ↓
                              Ghi Notification vào DB
```

### Tại sao dùng Queue?
- **Không block request:** Người dùng không cần chờ notification ghi xong.
- **Retry tự động:** Nếu job thất bại, Laravel tự retry.
- **Dễ scale:** Tăng worker khi traffic cao.

### Listeners đã chuyển sang Queue

| Listener                              | Tác vụ                           |
|---------------------------------------|----------------------------------|
| `SendBookingConfirmedNotification`    | Thông báo xác nhận booking       |
| `SendBookingCancelledNotification`    | Thông báo huỷ booking (2 người)  |
| `SendBookingCompletedNotification`    | Thông báo hoàn thành booking     |

### Chạy Queue Worker

```bash
# Development
php artisan queue:work

# Production (chạy nền)
php artisan queue:work --daemon --sleep=3 --tries=3
```

> **Lưu ý:** Với `QUEUE_CONNECTION=sync` (mặc định), job chạy đồng bộ. Đổi sang `database` hoặc `redis` để chạy bất đồng bộ thực sự.
