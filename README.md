# Classic Cut - Barbershop Booking System

Hệ thống đặt lịch cắt tóc trực tuyến (Barbershop) được xây dựng bằng Laravel 11, với thiết kế giao diện Classic/Vintage đặc trưng. Dự án hỗ trợ 4 vai trò người dùng (Admin, Thợ cắt tóc, Khách hàng đã đăng ký, Khách vãng lai) với các luồng nghiệp vụ đầy đủ từ đặt lịch, thanh toán đến báo cáo thống kê.

## 🌟 Chức năng nổi bật

### Dành cho Khách hàng (Client)
- Giao diện đẹp mắt mang phong cách vintage.
- Đặt lịch qua 4 bước (Booking Wizard): Chọn dịch vụ -> Chọn thợ -> Chọn giờ -> Điền thông tin.
- Hệ thống thanh toán tích hợp **VNPay** và **MoMo** (Sandbox).
- Quản lý lịch hẹn cá nhân, huỷ lịch (trước 2 giờ), đánh giá dịch vụ (Rating/Review).
- Quản lý hồ sơ cá nhân, upload avatar.

### Dành cho Khách vãng lai (Guest)
- Đặt lịch **không cần đăng ký tài khoản** — chỉ cần điền Tên, SĐT, Email.
- Hệ thống tự động tạo tài khoản guest ngầm (`findOrCreateGuest`).
- Sau khi có tài khoản, khách vãng lai có thể đăng nhập để xem lại lịch sử booking.

### Dành cho Thợ cắt tóc (Barber)
- Dashboard quản lý công việc riêng biệt.
- Quản lý lịch làm việc linh hoạt (đăng ký ca làm việc theo tuần/ngày).
- Tự động sinh `TimeSlot` dựa trên giờ làm việc.
- Quản lý lịch hẹn, chuyển trạng thái (In Progress, Completed).

### Dành cho Quản trị viên (Admin)
- Dashboard tổng quan: Biểu đồ doanh thu 7 ngày, Top thợ cắt.
- Quản lý người dùng, thợ cắt (thêm mới, phân quyền).
- Quản lý dịch vụ (Tạo mới, upload ảnh, giá tiền, thời lượng).
- Quản lý toàn bộ lịch hẹn hệ thống.
- Báo cáo chi tiết (Doanh thu, Top Dịch vụ, Top Thợ theo tháng).

## 🛠 Công nghệ sử dụng
- **Backend:** Laravel 11.x, PHP 8.2+
- **Frontend:** Blade Templates, Tailwind CSS (Vanilla setup), Alpine.js
- **Database:** MySQL
- **Payment:** VNPay Sandbox, MoMo Sandbox
- **Others:** Chart.js

## 🔒 Bảo mật
- **Payment Idempotency** — Callback VNPay/MoMo chống xử lý trùng lặp.
- **MoMo Signature Verification** — HMAC SHA256 chống giả mạo webhook.
- **VNPay IPN** — Server-to-server callback đảm bảo cập nhật thanh toán.
- **FSM Guard** — Booking status chỉ chuyển trạng thái hợp lệ (`canTransitionTo`).
- **Rate Limiting** — Chống spam đặt lịch/thanh toán (`throttle:5,1`).
- **Pessimistic Locking** — `lockForUpdate()` chống double-booking time slot.

## 🚀 Hướng dẫn cài đặt

1. **Clone repository:**
   ```bash
   git clone <repo-url>
   cd barbershop
   ```

2. **Cài đặt dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Cấu hình môi trường:**
   - Copy file `.env.example` thành `.env`
   - Cập nhật thông tin `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
   - Cập nhật thông tin tích hợp VNPay:
     ```env
     VNPAY_TMN_CODE=your_tmn_code
     VNPAY_HASH_SECRET=your_hash_secret
     VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
     VNPAY_RETURN_URL="${APP_URL}/payment/vnpay/return"
     ```

4. **Tạo key và chạy migration:**
   ```bash
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```
   *(Note: File seeder đã có sẵn dữ liệu mẫu cho Admin, Barber, Dịch vụ và một số booking. Tài khoản mặc định xem trong file `DatabaseSeeder.php`)*

5. **Tạo symbolic link cho storage:**
   ```bash
   php artisan storage:link
   ```

6. **Chạy server:**
   ```bash
   php artisan serve
   npm run dev
   ```

## ⏰ Cronjob (Schedule)
Hệ thống có tự động sinh Time Slots theo lịch làm việc của thợ.
Cần thiết lập cronjob chạy command sau mỗi ngày (hoặc có thể chạy thủ công để test):
```bash
php artisan slots:generate
```

## 📸 Giao diện thiết kế (Design System)
Dự án sử dụng file `index.css` với các CSS Custom Properties để duy trì tính nhất quán về màu sắc và typography theo phong cách cổ điển (Classic/Vintage):
- Colors: `--v-ink`, `--v-cream`, `--v-copper`, `--v-surface`
- Fonts: Serif (Playfair Display) và Sans (Inter)

## 📚 Tài liệu dự án

| Tài liệu | Mô tả |
|-----------|-------|
| [Kiến trúc hệ thống](docs/architecture.md) | Service Layer Pattern, cấu trúc thư mục, luồng booking, FSM, bảo mật |
| [Database Schema](docs/database-schema.md) | ERD diagram + chi tiết 10 bảng |
| [API Routes](docs/api-routes.md) | Tất cả routes theo role (Client, Admin, Barber) |
| [Tính năng chi tiết](docs/features.md) | 4 vai trò, thanh toán, tự động hoá, thiết kế |

---
