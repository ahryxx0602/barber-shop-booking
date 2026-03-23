# BarberBook — Hệ thống đặt lịch cắt tóc trực tuyến

> Đồ án tốt nghiệp — Phan Văn Thành — Khoa học Máy tính — Đại học Duy Tân
> Stack: **Laravel 12 + Blade + SQLite + Tailwind CSS + Alpine.js**

---

## 1. Mô tả dự án

**BarberBook** là ứng dụng web cho phép khách hàng đặt lịch cắt tóc trực tuyến tại các tiệm barber. Hệ thống hỗ trợ 3 vai trò: khách hàng đặt lịch, thợ cắt quản lý lịch làm việc, và admin quản lý toàn bộ hệ thống.

### Tính năng chính

| Vai trò | Chức năng |
|---|---|
| **Khách hàng (Client)** | Xem thợ & dịch vụ, đặt lịch theo slot giờ (cả guest lẫn authenticated), xem lịch sử, viết đánh giá, huỷ lịch |
| **Thợ cắt (Barber)** | Cài lịch làm việc, xem danh sách booking, xác nhận / từ chối, đánh dấu hoàn thành |
| **Admin** | Quản lý thợ, dịch vụ, lịch làm việc, xem booking tất cả thợ, xem báo cáo doanh thu |

---

## 2. Kiến trúc & Design Pattern

### 2.1 Tổng quan kiến trúc

```
Request → Middleware(RoleMiddleware) → Controller → Service → Model → DB
                                          ↓
                                      Event/Listener
                                          ↓
                                    Notification (in-app)
```

### 2.2 Các Design Pattern sử dụng

#### Service Layer Pattern
Toàn bộ business logic nằm trong Service. Controller gọi Service, không tự xử lý logic.

```
app/Services/
├── BookingService.php      ← tạo booking, huỷ, xác nhận, hoàn thành
├── BarberService.php       ← CRUD barber (tạo user + barber record)
├── ScheduleService.php     ← quản lý working schedule + regenerate slots
├── ServiceService.php      ← CRUD dịch vụ
└── TimeSlotService.php     ← generate time slots từ working schedule
```

#### Enum Pattern
PHP 8.1 backed enums thay thế string literals, đảm bảo type-safety:

```
app/Enums/
├── BookingStatus.php       ← Pending, Confirmed, InProgress, Completed, Cancelled
├── TimeSlotStatus.php      ← Available, Booked
└── UserRole.php            ← Admin, Barber, Customer
```

Mỗi Enum có methods: `label()` (tên tiếng Việt), `color()` (cho UI).
`BookingStatus` có thêm `canTransitionTo()` để validate state machine.

#### Observer / Event-Listener Pattern
Dùng Laravel Events để gửi notification khi trạng thái booking thay đổi:

```
app/Events/
├── BookingConfirmed.php
├── BookingCancelled.php
└── BookingCompleted.php

app/Listeners/
├── SendBookingConfirmedNotification.php
├── SendBookingCancelledNotification.php
└── SendBookingCompletedNotification.php
```

#### Policy Pattern
Phân quyền hành động trên booking:

```
app/Policies/
└── BookingPolicy.php       ← confirm, reject, start, complete, cancel
```

---

## 3. Cấu trúc thư mục dự án

```
barbershop/
├── app/
│   ├── Enums/
│   │   ├── BookingStatus.php
│   │   ├── TimeSlotStatus.php
│   │   └── UserRole.php
│   ├── Events/
│   │   ├── BookingConfirmed.php
│   │   ├── BookingCancelled.php
│   │   └── BookingCompleted.php
│   ├── Exceptions/
│   │   └── SlotNotAvailableException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Client/
│   │   │   │   ├── BarberController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── ReviewController.php
│   │   │   ├── Barber/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   └── ScheduleController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── BarberController.php
│   │   │   │   ├── ServiceController.php
│   │   │   │   ├── ScheduleController.php
│   │   │   │   └── BookingController.php
│   │   │   └── Auth/
│   │   │       ├── AuthenticatedSessionController.php
│   │   │       ├── RegisteredUserController.php
│   │   │       └── ...
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreBarberRequest.php
│   │       │   ├── UpdateBarberRequest.php
│   │       │   ├── StoreServiceRequest.php
│   │       │   └── UpdateServiceRequest.php
│   │       ├── Barber/
│   │       │   └── UpdateScheduleRequest.php
│   │       ├── Client/
│   │       │   ├── StoreBookingRequest.php
│   │       │   └── StoreReviewRequest.php
│   │       └── Auth/
│   │           └── LoginRequest.php
│   ├── Listeners/
│   │   ├── SendBookingConfirmedNotification.php
│   │   ├── SendBookingCancelledNotification.php
│   │   └── SendBookingCompletedNotification.php
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
│   ├── Policies/
│   │   └── BookingPolicy.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   ├── Services/
│   │   ├── BookingService.php
│   │   ├── BarberService.php
│   │   ├── ReviewService.php
│   │   ├── ScheduleService.php
│   │   ├── ServiceService.php
│   │   └── TimeSlotService.php
│   └── Console/Commands/
│       └── GenerateTimeSlots.php
│
├── resources/views/
│   ├── layouts/
│   │   ├── client.blade.php
│   │   ├── tailadmin.blade.php
│   │   ├── tailbarber.blade.php
│   │   ├── guest.blade.php
│   │   └── app.blade.php
│   ├── client/
│   │   ├── barbers/ (index, show)
│   │   ├── booking/ (create, confirmation)
│   │   └── profile/ (show, edit)
│   ├── barber/
│   │   ├── bookings/ (index)
│   │   ├── partials/ (booking-card)
│   │   ├── schedule/ (edit)
│   │   └── dashboard.blade.php
│   ├── admin/
│   │   ├── barbers/ (index, create, edit)
│   │   ├── services/ (index, create, edit)
│   │   ├── bookings/ (index)
│   │   ├── schedules/ (index, edit)
│   │   └── dashboard.blade.php
│   ├── auth/                       ← vintage style, dùng layouts.client
│   │   ├── login.blade.php
│   │   ├── register.blade.php
│   │   ├── forgot-password.blade.php
│   │   ├── reset-password.blade.php
│   │   ├── verify-email.blade.php
│   │   └── confirm-password.blade.php
│   ├── errors/                     ← standalone vintage HTML (không dùng layout)
│   │   ├── 403.blade.php
│   │   ├── 404.blade.php
│   │   ├── 419.blade.php
│   │   ├── 429.blade.php
│   │   ├── 500.blade.php
│   │   └── 503.blade.php
│   ├── profile/                    ← Breeze profile (admin/barber)
│   │   ├── edit.blade.php
│   │   └── partials/
│   ├── components/
│   └── partials/
│       ├── tailadmin-header.blade.php  ← avatar + notification bell
│       └── tailbarber-header.blade.php ← avatar + notification bell
│
└── routes/
    ├── web.php         ← client routes + dashboard redirect
    ├── admin.php       ← admin routes (role:admin middleware)
    ├── barber.php      ← barber routes (role:barber,admin middleware)
    ├── auth.php        ← Laravel Breeze auth routes
    └── console.php     ← scheduler commands
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
| role | enum | `customer`, `barber`, `admin` — cast → `UserRole` |
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

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| barber_id | bigint FK | references barbers.id |
| slot_date | date | ngày cụ thể — cast → `date` |
| start_time | time | |
| end_time | time | |
| status | enum | `available`, `booked` — cast → `TimeSlotStatus` |
| created_at / updated_at | timestamp | |

---

### Bảng: `bookings`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_code | varchar(20) | unique, dạng `BB-20250101-XXXX` |
| customer_id | bigint FK | references users.id |
| barber_id | bigint FK | references barbers.id |
| time_slot_id | bigint FK | references time_slots.id |
| booking_date | date | cast → `date` |
| start_time | time | snapshot từ time_slot |
| end_time | time | tính theo tổng duration dịch vụ |
| total_price | decimal(10,2) | cast → `decimal:2` |
| status | enum | cast → `BookingStatus` |
| note | text | nullable |
| cancelled_at | timestamp | nullable, cast → `datetime` |
| cancel_reason | text | nullable |
| created_at / updated_at | timestamp | |

---

### Bảng: `booking_services` (pivot)

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_id | bigint FK | |
| service_id | bigint FK | |
| price_snapshot | decimal(10,2) | **giá tại thời điểm đặt** |
| duration_snapshot | int | **thời gian tại thời điểm đặt** |

---

### Bảng: `payments`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_id | bigint FK | unique (1 booking - 1 payment) |
| amount | decimal(10,2) | |
| method | enum | `cash`, `vnpay`, `momo` |
| status | enum | `pending`, `paid`, `refunded` |
| transaction_id | varchar(255) | nullable |
| paid_at | timestamp | nullable |
| created_at / updated_at | timestamp | |

---

### Bảng: `reviews`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| booking_id | bigint FK | unique |
| customer_id | bigint FK | |
| barber_id | bigint FK | |
| rating | tinyint | 1–5 |
| comment | text | nullable |
| created_at / updated_at | timestamp | |

---

### Bảng: `notifications`

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK | |
| message | text | |
| read_at | timestamp | nullable |
| created_at / updated_at | timestamp | |

---

## 5. Phân quyền (Role & Permission)

Dùng **Laravel Gate + Policy + Enum** (không cần package ngoài).

```php
// Middleware kiểm tra role (dùng UserRole enum)
Route::middleware(['auth', 'role:admin'])->group(...);
Route::middleware(['auth', 'role:barber,admin'])->group(...);
```

| Hành động | Client | Barber | Admin |
|---|:---:|:---:|:---:|
| Xem danh sách thợ / dịch vụ | ✅ | ✅ | ✅ |
| Tạo booking | ✅ (+ guest) | ❌ | ✅ |
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
# Tự động generate time slots cho 7 ngày tới
php artisan slots:generate

# Tự động huỷ booking pending quá 30 phút
php artisan bookings:expire
```

---

*Tài liệu này mô tả toàn bộ kiến trúc và database của hệ thống BarberBook. Xem file `02developer-plan.md` để biết kế hoạch thực hiện chi tiết từng giai đoạn.*
