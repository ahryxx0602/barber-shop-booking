# 💈 BarberBook — Hệ thống đặt lịch cắt tóc trực tuyến

> Đồ án tốt nghiệp — Phan Văn Thành — Khoa học Máy tính — Đại học Duy Tân  
> Stack: **Laravel 12 + Blade + MySQL + Tailwind CSS + Alpine.js**

---

## 1. Mô tả dự án

**BarberBook** là ứng dụng web cho phép khách hàng đặt lịch cắt tóc trực tuyến tại các tiệm barber. Hệ thống hỗ trợ 3 vai trò: khách hàng đặt lịch, thợ cắt quản lý lịch làm việc, và admin quản lý toàn bộ hệ thống.

### Tính năng chính

| Vai trò | Chức năng |
|---|---|
| **Khách hàng** | Xem thợ & dịch vụ, đặt lịch theo slot giờ, xem lịch sử, viết đánh giá, huỷ lịch |
| **Thợ cắt** | Cài lịch làm việc, xem danh sách booking, xác nhận / từ chối, đánh dấu hoàn thành |
| **Admin** | Quản lý thợ, dịch vụ, người dùng, xem báo cáo doanh thu |

---

## 2. Kiến trúc & Design Pattern

### 2.1 Tổng quan kiến trúc

```
Request → Middleware → Controller → Service → Repository → Model → DB
                                       ↓
                                   Event/Listener
                                       ↓
                              Notification / Mail
```

### 2.2 Các Design Pattern sử dụng

#### Repository Pattern
Tách biệt logic truy vấn database khỏi Controller. Controller chỉ nhận request và trả response, không biết gì về DB.

```
app/Repositories/
├── Contracts/
│   ├── BookingRepositoryInterface.php
│   └── BarberRepositoryInterface.php
├── BookingRepository.php
└── BarberRepository.php
```

#### Service Layer Pattern
Toàn bộ business logic nằm trong Service. Controller gọi Service, không tự xử lý logic.

```
app/Services/
├── BookingService.php      ← tạo booking, huỷ, tính giá
├── TimeSlotService.php     ← tính slot khả dụng, generate slot
├── PaymentService.php      ← xử lý thanh toán
└── NotificationService.php ← gửi thông báo
```

#### Observer / Event-Listener Pattern
Dùng Laravel Events để gửi email và notification khi trạng thái booking thay đổi, tránh nhồi nhét logic vào Service.

```
app/Events/
├── BookingCreated.php
├── BookingConfirmed.php
└── BookingCancelled.php

app/Listeners/
├── SendBookingConfirmationEmail.php
└── SendBookingNotification.php
```

#### Strategy Pattern (mở rộng sau)
Cho phép thêm phương thức thanh toán mới mà không sửa code cũ.

```
app/Strategies/Payment/
├── PaymentStrategyInterface.php
├── CashStrategy.php
├── VNPayStrategy.php
└── MoMoStrategy.php
```

---

## 3. Cấu trúc thư mục dự án

```
barbershop/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Customer/
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── BarberController.php
│   │   │   │   └── ReviewController.php
│   │   │   ├── Barber/
│   │   │   │   ├── ScheduleController.php
│   │   │   │   └── BookingController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── UserController.php
│   │   │       ├── ServiceController.php
│   │   │       └── BarberController.php
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php
│   │   │   └── EnsureBookingOwner.php
│   │   └── Requests/
│   │       ├── StoreBookingRequest.php
│   │       └── StoreReviewRequest.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Barber.php
│   │   ├── Service.php
│   │   ├── WorkingSchedule.php
│   │   ├── TimeSlot.php
│   │   ├── Booking.php
│   │   ├── BookingService.php
│   │   ├── Payment.php
│   │   ├── Review.php
│   │   └── Notification.php
│   │
│   ├── Services/
│   ├── Repositories/
│   ├── Events/
│   ├── Listeners/
│   └── Console/Commands/
│       └── GenerateTimeSlots.php   ← artisan command tạo slot hàng ngày
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php       ← layout khách hàng
│       │   ├── barber.blade.php    ← layout thợ
│       │   └── admin.blade.php     ← layout admin
│       ├── customer/
│       ├── barber/
│       ├── admin/
│       └── emails/
│
└── routes/
    ├── web.php
    ├── customer.php
    ├── barber.php
    └── admin.php
```

---

## 4. Database Schema

### Tổng quan quan hệ

```
users ──────────── barbers (1-1)
users ──────────── bookings (1-n, với tư cách customer)
barbers ─────────── working_schedules (1-n)
barbers ─────────── time_slots (1-n)
bookings ────────── booking_services (1-n)
bookings ────────── payments (1-1)
bookings ────────── reviews (1-1)
services ────────── booking_services (1-n)
users ──────────── notifications (1-n)
```

---

### Bảng: `users`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | |
| email | varchar(191) | unique |
| phone | varchar(20) | nullable |
| password | varchar(255) | |
| role | enum | `customer`, `barber`, `admin` |
| avatar | varchar(255) | nullable, đường dẫn ảnh |
| email_verified_at | timestamp | nullable |
| remember_token | varchar | |
| created_at / updated_at | timestamp | |

---

### Bảng: `barbers`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK | references users.id |
| bio | text | nullable |
| experience_years | tinyint | số năm kinh nghiệm |
| rating | decimal(3,2) | trung bình từ reviews, default 0.00 |
| is_active | boolean | default true |
| created_at / updated_at | timestamp | |

---

### Bảng: `services`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | |
| description | text | nullable |
| price | decimal(10,2) | đơn vị VNĐ |
| duration_minutes | int | thời gian thực hiện |
| image | varchar(255) | nullable |
| is_active | boolean | default true |
| created_at / updated_at | timestamp | |

---

### Bảng: `working_schedules`

Lưu lịch làm việc mặc định mỗi tuần của thợ.

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| barber_id | bigint FK | references barbers.id |
| day_of_week | tinyint | 0=CN, 1=T2, ..., 6=T7 |
| start_time | time | giờ bắt đầu |
| end_time | time | giờ kết thúc |
| is_day_off | boolean | default false (ngày nghỉ) |

---

### Bảng: `time_slots`

Slot giờ cụ thể cho từng ngày, được generate từ `working_schedules`.

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| barber_id | bigint FK | references barbers.id |
| slot_date | date | ngày cụ thể |
| start_time | time | |
| end_time | time | |
| status | enum | `available`, `booked`, `blocked` |
| created_at / updated_at | timestamp | |

**Unique:** `(barber_id, slot_date, start_time)`  
**Index:** `(barber_id, slot_date, status)` để query nhanh

---

### Bảng: `bookings`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_code | varchar(20) | unique, dạng `BB-20250101-XXXX` |
| customer_id | bigint FK | references users.id |
| barber_id | bigint FK | references barbers.id |
| time_slot_id | bigint FK | references time_slots.id |
| booking_date | date | |
| start_time | time | snapshot từ time_slot |
| end_time | time | tính theo tổng duration dịch vụ |
| total_price | decimal(10,2) | tổng tiền snapshot |
| status | enum | `pending`, `confirmed`, `in_progress`, `completed`, `cancelled` |
| note | text | nullable, ghi chú của khách |
| cancelled_at | timestamp | nullable |
| cancel_reason | text | nullable |
| created_at / updated_at | timestamp | |

**Luồng trạng thái:**
```
pending → confirmed → in_progress → completed
        ↘ cancelled (khách huỷ)
                    ↘ cancelled (thợ từ chối)
```

---

### Bảng: `booking_services` (pivot)

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_id | bigint FK | |
| service_id | bigint FK | |
| price_snapshot | decimal(10,2) | **giá tại thời điểm đặt** |
| duration_snapshot | int | **thời gian tại thời điểm đặt** |

> ⚠️ Quan trọng: Snapshot lại giá và duration để tránh bị ảnh hưởng khi admin sửa giá dịch vụ về sau.

---

### Bảng: `payments`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_id | bigint FK | unique (1 booking - 1 payment) |
| amount | decimal(10,2) | |
| method | enum | `cash`, `vnpay`, `momo` |
| status | enum | `pending`, `paid`, `refunded` |
| transaction_id | varchar(255) | nullable, mã giao dịch từ cổng |
| paid_at | timestamp | nullable |
| created_at / updated_at | timestamp | |

---

### Bảng: `reviews`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_id | bigint FK | unique (chỉ review 1 lần/booking) |
| customer_id | bigint FK | |
| barber_id | bigint FK | |
| rating | tinyint | 1–5 |
| comment | text | nullable |
| created_at / updated_at | timestamp | |

**Ràng buộc:** Chỉ được review khi booking có status = `completed`.

---

### Bảng: `notifications`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK | |
| type | varchar(50) | `booking_created`, `booking_confirmed`, `booking_cancelled`... |
| title | varchar(255) | |
| message | text | |
| is_read | boolean | default false |
| created_at / updated_at | timestamp | |

---

## 5. Phân quyền (Role & Permission)

Dùng **Laravel Gate + Policy** (không cần package ngoài cho đồ án cơ bản).

```php
// Middleware kiểm tra role
Route::middleware(['auth', 'role:customer'])->group(...);
Route::middleware(['auth', 'role:barber'])->group(...);
Route::middleware(['auth', 'role:admin'])->group(...);
```

| Hành động | Customer | Barber | Admin |
|---|:---:|:---:|:---:|
| Xem danh sách thợ / dịch vụ | ✅ | ✅ | ✅ |
| Tạo booking | ✅ | ❌ | ✅ |
| Huỷ booking của mình | ✅ | ❌ | ✅ |
| Xác nhận / từ chối booking | ❌ | ✅ | ✅ |
| Cài working schedule | ❌ | ✅ | ✅ |
| Viết review | ✅ | ❌ | ❌ |
| CRUD dịch vụ | ❌ | ❌ | ✅ |
| CRUD thợ | ❌ | ❌ | ✅ |
| Xem báo cáo doanh thu | ❌ | ❌ | ✅ |

---

## 6. Artisan Commands tự định nghĩa

```bash
# Tự động generate time slots cho 7 ngày tới (chạy qua Scheduler hằng ngày)
php artisan slots:generate

# Tự động huỷ booking pending quá 30 phút không có xác nhận
php artisan bookings:expire
```

---

## 7. Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Backend Framework | Laravel 12 |
| Template Engine | Blade |
| CSS Framework | Tailwind CSS v3 |
| JS nhẹ (UI) | Alpine.js |
| Database | MySQL 8 |
| Authentication | Laravel Breeze |
| Email | Laravel Mail + Mailtrap (dev) |
| Queue | Laravel Queue (database driver) |
| Scheduler | Laravel Task Scheduling |
| Version Control | Git + GitHub |

---

*Tài liệu này mô tả toàn bộ kiến trúc và database của hệ thống BarberBook. Xem file `02_development_plan.md` để biết kế hoạch thực hiện chi tiết từng giai đoạn.*