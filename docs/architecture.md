# 🏗 Kiến trúc hệ thống — Classic Cut

## Tổng quan

Classic Cut sử dụng kiến trúc **Service Layer Pattern**, tách biệt rõ ràng giữa Controller (nhận request) và Service (xử lý business logic).

```
Request → Controller → Service → Model/DB → Response
              ↓
          FormRequest (validation)
              ↓
            DTO (type-safe data transfer)
```

## Cấu trúc thư mục chính

```
app/
├── DTOs/                    # Data Transfer Objects (type-safe)
│   ├── CreateBookingData       ├── StoreReviewData
│   ├── CreateBarberData        ├── ScheduleItemData
│   ├── UpdateBarberData        └── UpdateScheduleData
│
├── Enums/                   # PHP 8.1 Backed Enums
│   ├── BookingStatus (pending → confirmed → in_progress → completed/cancelled)
│   ├── PaymentStatus (pending → paid/failed/refunded)
│   ├── PaymentMethod (cash, vnpay, momo)
│   ├── TimeSlotStatus (available, booked)
│   └── UserRole (admin, barber, customer)
│
├── Events/                  # Booking lifecycle events
│   ├── BookingConfirmed     ├── BookingCancelled
│   └── BookingCompleted
│
├── Exceptions/              # Custom exceptions
│   └── SlotNotAvailableException
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # AdminDashboard, Barber, Service, User, Booking, Report, Schedule
│   │   ├── Barber/          # Dashboard, Booking, Schedule
│   │   └── Client/          # Barber, Booking, Payment, Profile, Review
│   ├── Middleware/
│   │   ├── RoleMiddleware   # Phân quyền theo UserRole enum
│   │   ├── SecurityHeaders  # XSS, Clickjacking protection
│   │   └── LogActivity      # Ghi log data modification
│   └── Requests/            # Form validation (StoreBooking, StoreReview, UpdateBarber, ...)
│
├── Listeners/               # Xử lý event → tạo notification
│
├── Models/                  # 10 Eloquent models
│   ├── User, Barber, Service, Booking, Payment
│   ├── TimeSlot, WorkingSchedule, Review, Notification
│   └── BookingService (pivot model)
│
├── Policies/
│   └── BookingPolicy        # confirm, reject, start, complete, cancel
│
└── Services/                # 9 business services
    ├── BookingService       # Tạo/huỷ booking, FSM transitions, pessimistic locking
    ├── PaymentService       # VNPay + MoMo integration, signature verify, idempotency
    ├── TimeSlotService      # Sinh slot tự động, batch upsert
    ├── ScheduleService      # CRUD lịch làm việc barber
    ├── BarberService        # CRUD barber + user
    ├── ServiceService       # CRUD dịch vụ
    ├── ReviewService        # Đánh giá + cập nhật rating trung bình
    ├── ReportService        # Thống kê doanh thu, top barber/service
    └── CacheService         # Cache layer cho barbers/services listing
```

## Luồng nghiệp vụ chính

### Đặt lịch (Booking Flow)

```
1. Client chọn dịch vụ + thợ + slot giờ (Alpine.js wizard)
2. StoreBookingRequest validate input
3. BookingService::create() — DB::transaction + lockForUpdate()
   ├── Kiểm tra slot available
   ├── Tính tổng giá + duration
   ├── Tạo Booking + attach services (pivot với price/duration snapshot)
   ├── Cập nhật TimeSlot → booked
   └── Dispatch BookingCreated event
4. Redirect → Payment page
5. Client chọn phương thức (Cash / VNPay / MoMo)
6. PaymentService tạo URL → redirect sang gateway
7. Gateway callback → verifyCallback() (signature + idempotency)
8. Hiển thị confirmation page
```

### Trạng thái Booking (FSM)

```
pending ──→ confirmed ──→ in_progress ──→ completed
   │             │
   └──→ cancelled ←──┘
```

Mọi transition đều phải qua `BookingStatus::canTransitionTo()` guard trong `BookingService`.

## Bảo mật

| Cơ chế | Mô tả |
|--------|-------|
| Pessimistic Locking | `lockForUpdate()` trong transaction chống double-booking |
| Idempotency | Check `PaymentStatus::Paid/Failed` trước khi xử lý callback |
| Signature Verify | HMAC SHA256 (MoMo) + SHA512 (VNPay) |
| VNPay IPN | Server-to-server POST callback, exclude CSRF |
| FSM Guard | `canTransitionTo()` chặn chuyển trạng thái bất hợp lệ |
| Rate Limiting | `throttle:5,1` trên booking/payment POST routes |
| Security Headers | XSS, Clickjacking, MIME sniffing protection |
| Role Middleware | Phân quyền theo `UserRole` enum |
| BookingPolicy | Authorization cho từng action booking |
