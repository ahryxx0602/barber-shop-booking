# Bảo mật ứng dụng (Security Posture)

## Tổng quan

Module bảo mật áp dụng 3 lớp bảo vệ: **HTTP Security Headers**, **Rate Limiting**, và **Security Audit** command.

---

## 1. SecurityHeaders Middleware

**File:** `app/Http/Middleware/SecurityHeaders.php`

### Cách hoạt động

Middleware tự động thêm security headers vào **mọi HTTP response**:

| Header                    | Giá trị                          | Chống                                 |
|---------------------------|----------------------------------|---------------------------------------|
| `X-Content-Type-Options`  | `nosniff`                        | MIME type sniffing                    |
| `X-Frame-Options`         | `DENY`                           | Clickjacking (nhúng iframe)           |
| `X-XSS-Protection`        | `1; mode=block`                  | XSS trên trình duyệt cũ              |
| `Referrer-Policy`         | `strict-origin-when-cross-origin`| Rò rỉ URL qua Referer header         |
| `Permissions-Policy`      | `camera=(), microphone=()...`    | Truy cập camera/mic không cần thiết   |

### Luồng
```
Request → ... → Controller → Response
                                  ↓
                        SecurityHeaders thêm headers
                                  ↓
                        Response gửi về browser
```

---

## 2. Rate Limiting

**File:** `app/Providers/AppServiceProvider.php`

### Cách hoạt động

Rate Limiting **giới hạn số lượng request** từ 1 IP/user trong khoảng thời gian:

| Limiter    | Giới hạn     | Áp dụng cho                                    |
|------------|--------------|------------------------------------------------|
| `login`    | 5 lần/phút   | Trang đăng nhập — chống brute force            |
| `booking`  | 10 lần/phút  | Tạo booking — chống spam                        |

### Sử dụng
Áp dụng rate limiter vào route bằng middleware:
```php
Route::post('/login', ...)->middleware('throttle:login');
Route::post('/bookings', ...)->middleware('throttle:booking');
```

### Khi vượt giới hạn
- Response: **HTTP 429 Too Many Requests**
- Header `Retry-After` cho biết thời gian chờ (giây)

---

## 3. Security Audit Command

**File:** `app/Console/Commands/SecurityAudit.php`

```bash
php artisan security:audit
```

### Kiểm tra 7 hạng mục

| #  | Hạng mục                        | Đạt ✅                     | Cảnh báo ⚠️                       |
|----|----------------------------------|----------------------------|-------------------------------------|
| 1  | APP_DEBUG                        | `false`                    | `true` trên production              |
| 2  | APP_ENV                          | `production`               | Không phải production               |
| 3  | APP_KEY                          | Đã thiết lập               | Chưa thiết lập                      |
| 4  | HTTPS                            | URL bắt đầu `https://`    | Chưa dùng HTTPS                     |
| 5  | Session Secure Cookie            | `true`                     | `false`                             |
| 6  | .env exposure                    | Không trong public         | .env nằm trong public (NGHIÊM TRỌNG)|
| 7  | SQLite file permissions          | `0644` hoặc `0600`        | Quyền quá mở                        |

### Ví dụ output
```
🔒 Kiểm tra bảo mật BarberBook

  ✅ APP_KEY đã được thiết lập
  ⚠️  APP_DEBUG đang BẬT — phải tắt trên production
  ⚠️  APP_ENV = local — nên là "production" trên server
  ✅ .env không nằm trong thư mục public

Kết quả: ✅ 2 đạt | ❌ 2 cần xem lại
```

---

## 4. Bảo mật có sẵn từ Laravel

Ngoài các module trên, Laravel đã tích hợp sẵn:
- **CSRF Protection:** Token tự động trong mọi form (`@csrf`)
- **SQL Injection:** Eloquent sử dụng prepared statements
- **XSS:** Blade tự động escape output (`{{ }}`)
- **Password Hashing:** `Hash::make()` dùng bcrypt
- **Authentication:** Laravel Breeze với session-based auth
