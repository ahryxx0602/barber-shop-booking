# 📖 BarberBook — Developer Handbook

> Cuốn sổ tay dành cho developer, giải thích đầy đủ kiến trúc, luồng xử lý, nghiệp vụ,  
> và cách các thành phần trong dự án liên kết với nhau.

---

## Mục lục

1. [Tổng quan kiến trúc](#1-tổng-quan-kiến-trúc)
2. [Cấu trúc thư mục & Đường đi file](#2-cấu-trúc-thư-mục--đường-đi-file)
3. [Luồng xử lý một request](#3-luồng-xử-lý-một-request)
4. [Hệ thống Enum — Tại sao cần & cách hoạt động](#4-hệ-thống-enum)
5. [DTO (Data Transfer Object) — Là gì & tác dụng](#5-dto-data-transfer-object)
6. [Service Layer — Nơi chứa business logic](#6-service-layer)
7. [Luồng đặt lịch (Booking Flow)](#7-luồng-đặt-lịch-booking-flow)
8. [Luồng thanh toán (Payment Flow)](#8-luồng-thanh-toán-payment-flow)
9. [Hệ thống Event / Listener / Job](#9-hệ-thống-event--listener--job)
10. [Caching — CacheService](#10-caching--cacheservice)
11. [Middleware — Bộ lọc request](#11-middleware--bộ-lọc-request)
12. [Policy — Phân quyền chi tiết](#12-policy--phân-quyền-chi-tiết)
13. [Console Commands — Tác vụ nền](#13-console-commands--tác-vụ-nền)
14. [Database Schema — Sơ đồ CSDL](#14-database-schema--sơ-đồ-cơ-sở-dữ-liệu)
15. [Tổng kết sơ đồ kiến trúc](#15-tổng-kết-sơ-đồ-kiến-trúc)

---

## 1. Tổng quan kiến trúc

Dự án sử dụng mô hình **MVC + Service Layer**:

```
Request → Middleware → Controller → Service → Model/DB
                                  ↘ DTO (dữ liệu vào)
                                  ↘ Enum (giá trị cố định)
                                  ↘ Event → Listener → Job (side effects)
```

**Nguyên tắc cốt lõi:**
- **Controller**: Chỉ nhận request, gọi Service, trả response. **Không chứa business logic.**
- **Service**: Chứa toàn bộ business logic (tính toán, validate nghiệp vụ, gọi DB).
- **DTO**: Đóng gói dữ liệu từ request → truyền vào Service (type-safe, rõ ràng).
- **Enum**: Định nghĩa các giá trị cố định (trạng thái, vai trò...) thay vì dùng string rời rạc.
- **Model**: Chỉ định nghĩa relationships, casts, fillable. Không có business logic.

---

## 2. Cấu trúc thư mục & Đường đi file

```
app/
├── Console/Commands/          # Artisan commands (cron jobs)
│   ├── CleanupLogs.php        # Dọn log cũ
│   ├── ExpireBookings.php     # Tự động hủy booking quá hạn
│   ├── GenerateTimeSlots.php  # Tạo time slots hàng ngày
│   └── SecurityAudit.php      # Kiểm tra bảo mật
│
├── DTOs/                      # Data Transfer Objects
│   ├── CreateBookingData.php  # Dữ liệu tạo booking
│   ├── CreateBarberData.php   # Dữ liệu tạo barber
│   ├── UpdateBarberData.php   # Dữ liệu sửa barber
│   ├── StoreReviewData.php    # Dữ liệu đánh giá
│   ├── ScheduleItemData.php   # 1 ngày trong lịch
│   └── UpdateScheduleData.php # Cập nhật lịch (7 ngày)
│
├── Enums/                     # Giá trị cố định
│   ├── BookingStatus.php      # Trạng thái booking (với FSM)
│   ├── PaymentMethod.php      # Phương thức thanh toán
│   ├── PaymentStatus.php      # Trạng thái thanh toán
│   ├── TimeSlotStatus.php     # Trạng thái khung giờ
│   └── UserRole.php           # Vai trò người dùng
│
├── Events/                    # Sự kiện domain
│   ├── BookingConfirmed.php
│   ├── BookingCancelled.php
│   └── BookingCompleted.php
│
├── Exceptions/
│   └── SlotNotAvailableException.php  # Slot đã bị đặt
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/             # 8 controllers cho admin
│   │   │   ├── DashboardController.php
│   │   │   ├── BarberController.php
│   │   │   ├── ServiceController.php
│   │   │   ├── BookingController.php
│   │   │   ├── ScheduleController.php
│   │   │   ├── UserController.php
│   │   │   ├── ReportController.php
│   │   │   └── SystemLogController.php
│   │   ├── Barber/            # 3 controllers cho barber
│   │   │   ├── DashboardController.php
│   │   │   ├── BookingController.php
│   │   │   └── ScheduleController.php
│   │   ├── Client/            # 5 controllers cho khách
│   │   │   ├── BarberController.php
│   │   │   ├── BookingController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ProfileController.php
│   │   │   └── ReviewController.php
│   │   └── ProfileController.php  # Profile chung (Breeze)
│   │
│   ├── Middleware/
│   │   ├── RoleMiddleware.php      # Kiểm tra vai trò
│   │   ├── LogActivity.php         # Log thay đổi dữ liệu
│   │   └── SecurityHeaders.php     # HTTP security headers
│   │
│   └── Requests/              # Form validation
│       ├── Admin/             # Validate cho admin
│       ├── Barber/            # Validate cho barber
│       ├── Client/            # Validate cho client
│       └── Auth/              # Validate cho auth
│
├── Jobs/
│   └── SendBookingNotificationJob.php  # Gửi notification async
│
├── Listeners/                 # Xử lý events
│   ├── SendBookingConfirmedNotification.php
│   ├── SendBookingCancelledNotification.php
│   └── SendBookingCompletedNotification.php
│
├── Models/                    # 10 Eloquent models
│   ├── User.php
│   ├── Barber.php
│   ├── Service.php
│   ├── Booking.php
│   ├── BookingService.php     # Pivot table
│   ├── TimeSlot.php
│   ├── WorkingSchedule.php
│   ├── Payment.php
│   ├── Review.php
│   └── Notification.php
│
├── Policies/
│   └── BookingPolicy.php      # Phân quyền booking
│
├── Providers/
│   └── AppServiceProvider.php # Đăng ký events, rate limiting
│
└── Services/                  # Business logic layer
    ├── BookingService.php     # Nghiệp vụ đặt lịch
    ├── PaymentService.php     # Thanh toán VNPay/MoMo
    ├── BarberService.php      # CRUD barber
    ├── ScheduleService.php    # Lịch làm việc
    ├── TimeSlotService.php    # Tạo/quản lý time slots
    ├── ReviewService.php      # Đánh giá
    ├── ReportService.php      # Báo cáo thống kê
    ├── ServiceService.php     # CRUD dịch vụ
    └── CacheService.php       # Quản lý cache tập trung

routes/
├── web.php                    # Routes client + trang chủ
├── admin.php                  # Routes admin (prefix /admin)
├── barber.php                 # Routes barber (prefix /barber)
├── auth.php                   # Routes đăng nhập/đăng ký
└── console.php                # Cron schedules
```

---

## 3. Luồng xử lý một request

Lấy ví dụ cụ thể: **Khách hàng đặt lịch cắt tóc**

### Bước 1: Request đi vào từ browser

```
POST /booking  →  routes/web.php  →  Route match: client.booking.store
```

### Bước 2: Middleware xử lý

```
1. SecurityHeaders    → Thêm header bảo mật vào response
2. throttle:5,1       → Rate limit: tối đa 5 request/phút  
3. LogActivity        → Ghi log POST request
```

### Bước 3: Form Request validate dữ liệu

```php
// StoreBookingRequest tự động validate trước khi Controller nhận request
class StoreBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'barber_id'    => 'required|exists:barbers,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'service_ids'  => 'required|array',
            // ...
        ];
    }
}
```

> Nếu validate **fail** → Laravel tự redirect về + show lỗi. Controller không bao giờ nhận data sai.

### Bước 4: Controller nhận request → Tạo DTO → Gọi Service

```php
// Client\BookingController::store()
public function store(StoreBookingRequest $request)
{
    // 1. Tạo DTO từ request (đã validated)
    $dto = CreateBookingData::fromRequest($request);
    
    // 2. Gọi service với DTO
    $booking = $this->bookingService->create($dto, $request->user());
    
    // 3. Redirect sang trang thanh toán
    return redirect()->route('client.payment.show', $booking);
}
```

### Bước 5: Service xử lý business logic

```php
// BookingService::create()
public function create(CreateBookingData $data, ?User $customer = null): Booking
{
    return DB::transaction(function () use ($data, $customer) {
        // 1. Lock slot để tránh race condition
        $slot = TimeSlot::lockForUpdate()->findOrFail($data->time_slot_id);
        
        // 2. Kiểm tra slot còn available không
        if ($slot->status !== TimeSlotStatus::Available) {
            throw new SlotNotAvailableException('Slot đã được đặt');
        }
        
        // 3. Tạo booking + attach services + update slot
        $booking = Booking::create([...]);
        $booking->services()->attach([...]);
        $slot->update(['status' => TimeSlotStatus::Booked]);
        
        // 4. Ghi log
        Log::channel('booking')->info('Booking created', [...]);
        
        return $booking;
    });
}
```

### Sơ đồ tổng quát

```
Browser POST /booking
    │
    ▼
┌─────────────────┐
│   Middleware     │  SecurityHeaders → throttle → LogActivity
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  FormRequest    │  StoreBookingRequest → validate dữ liệu
│  (Validation)   │  Fail? → redirect back + lỗi
└────────┬────────┘
         │ (validated data)
         ▼
┌─────────────────┐
│   Controller    │  BookingController::store()
│  (Điều phối)    │  1. Tạo DTO ← CreateBookingData::fromRequest()
│                 │  2. Gọi Service ← bookingService->create()
└────────┬────────┘
         │ (DTO)
         ▼
┌─────────────────┐
│    Service      │  BookingService::create()
│ (Business Logic)│  DB::transaction → lock slot → tạo booking → attach services
│                 │  → update slot status → Log → return Booking
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Model / DB     │  Booking::create(), TimeSlot::update(), ...
└─────────────────┘
```

---

## 4. Hệ thống Enum

### Enum là gì?

Enum = **tập hợp các giá trị cố định được đặt tên**. Thay vì viết string `'pending'`, `'confirmed'` rải rác khắp code, ta dùng `BookingStatus::Pending`, `BookingStatus::Confirmed`.

### Tại sao cần Enum?

| Không có Enum ❌ | Có Enum ✅ |
|---|---|
| `$booking->status = 'pending'` | `$booking->status = BookingStatus::Pending` |
| Dễ sai chính tả, IDE không gợi ý | IDE autocomplete, type-safe |
| Không biết có bao nhiêu trạng thái | Xem Enum biết hết |
| Kiểm tra: `$status == 'pendding'` (typo) | Compile-time error nếu sai |

### 5 Enum trong dự án

#### 4.1. `BookingStatus` — Trạng thái booking

```php
enum BookingStatus: string
{
    case Pending    = 'pending';       // Chờ xác nhận
    case Confirmed  = 'confirmed';     // Đã xác nhận  
    case InProgress = 'in_progress';   // Đang phục vụ
    case Completed  = 'completed';     // Hoàn thành
    case Cancelled  = 'cancelled';     // Đã hủy
}
```

**Đặc biệt: Có FSM (Finite State Machine) — Máy trạng thái hữu hạn**

#### FSM là gì?

FSM = **quy tắc kiểm soát một đối tượng chỉ được chuyển từ trạng thái A sang trạng thái B theo đúng luồng cho phép**, không được nhảy loạn.

**Ví dụ đời thực — Đèn giao thông:**
```
Xanh ──→ Vàng ──→ Đỏ ──→ Xanh (lặp lại)

✅ Xanh → Vàng     (hợp lệ)
✅ Vàng → Đỏ       (hợp lệ)
❌ Xanh → Đỏ       (KHÔNG hợp lệ — phải qua Vàng trước)
❌ Đỏ → Vàng       (KHÔNG hợp lệ — đèn đỏ chỉ chuyển sang Xanh)
```

Đèn giao thông **KHÔNG THỂ** nhảy lung tung. Booking cũng vậy!

#### Sơ đồ FSM của Booking trong dự án

```
                    ┌─────────────────────────────────────────────────┐
                    │          BOOKING STATE MACHINE                   │
                    │                                                 │
                    │   ┌──────────┐    xác nhận    ┌──────────┐     │
                    │   │ PENDING  │ ──────────────→│CONFIRMED │     │
                    │   │(Chờ xác  │                │(Đã xác   │     │
                    │   │  nhận)   │                │  nhận)   │     │
                    │   └────┬─────┘                └────┬─────┘     │
                    │        │                           │           │
                    │        │ từ chối/                   │ bắt đầu  │
                    │        │ khách hủy      khách hủy  │ phục vụ  │
                    │        │                    │       │           │
                    │        ▼                    ▼       ▼           │
                    │   ┌──────────┐         ┌──────────┐            │
                    │   │CANCELLED │         │IN_PROGRESS│            │
                    │   │(Đã hủy)  │         │(Đang phục│            │
                    │   │          │         │   vụ)    │            │
                    │   └──────────┘         └────┬─────┘            │
                    │   (Trạng thái               │                  │
                    │    cuối cùng)          hoàn thành               │
                    │                             │                  │
                    │                             ▼                  │
                    │                        ┌──────────┐            │
                    │                        │COMPLETED │            │
                    │                        │(Hoàn     │            │
                    │                        │  thành)  │            │
                    │                        └──────────┘            │
                    │                        (Trạng thái             │
                    │                         cuối cùng)             │
                    └─────────────────────────────────────────────────┘
```

#### Bảng chuyển trạng thái đầy đủ

| Trạng thái hiện tại | Được chuyển sang | KHÔNG được chuyển sang |
|---------------------|-----------------|----------------------|
| **Pending** | ✅ Confirmed, ✅ Cancelled | ❌ InProgress, ❌ Completed |
| **Confirmed** | ✅ InProgress, ✅ Cancelled | ❌ Pending, ❌ Completed |
| **InProgress** | ✅ Completed | ❌ Pending, ❌ Confirmed, ❌ Cancelled |
| **Completed** | _(không chuyển được nữa)_ | ❌ Tất cả |
| **Cancelled** | _(không chuyển được nữa)_ | ❌ Tất cả |

#### Ví dụ cụ thể: Hợp lệ vs Không hợp lệ

```
✅ HỢP LỆ — Luồng bình thường:
   Pending → Confirmed → InProgress → Completed
   "Khách đặt → Barber xác nhận → Bắt đầu cắt → Cắt xong"

✅ HỢP LỆ — Khách hủy sớm:
   Pending → Cancelled
   "Khách đặt → Khách đổi ý, hủy"

✅ HỢP LỆ — Barber từ chối:
   Pending → Cancelled
   "Khách đặt → Barber bận, từ chối"

✅ HỢP LỆ — Khách hủy sau khi xác nhận:
   Pending → Confirmed → Cancelled
   "Khách đặt → Barber xác nhận → Khách hủy (trước 2 tiếng)"

❌ KHÔNG HỢP LỆ:
   Pending → Completed     ← Chưa xác nhận mà hoàn thành?!
   Pending → InProgress    ← Chưa xác nhận mà bắt đầu phục vụ?!
   Completed → Cancelled   ← Đã cắt xong rồi mà hủy?!
   InProgress → Cancelled  ← Đang cắt dở mà hủy?! (khách phải chờ xong)
   Cancelled → Pending     ← Đã hủy rồi mà mở lại?!
```

#### Code FSM trong Enum

```php
// File: app/Enums/BookingStatus.php

public function canTransitionTo(self $target): bool
{
    return match ($this) {
        //  Từ trạng thái    →  Được chuyển sang
        self::Pending    => in_array($target, [self::Confirmed, self::Cancelled]),
        self::Confirmed  => in_array($target, [self::InProgress, self::Cancelled]),
        self::InProgress => $target === self::Completed,  // Chỉ 1 hướng duy nhất
        
        // Trạng thái cuối cùng — không chuyển đi đâu được nữa
        self::Completed, self::Cancelled => false,
    };
}
```

#### Cách Service dùng FSM

```php
// BookingService::confirm()
public function confirm(Booking $booking): Booking
{
    // BƯỚC 1: Hỏi FSM — "Từ trạng thái hiện tại có được chuyển sang Confirmed không?"
    if (!$booking->status->canTransitionTo(BookingStatus::Confirmed)) {
        // VD: booking đang InProgress → canTransitionTo(Confirmed) = false
        throw new \InvalidArgumentException(
            'Không thể xác nhận booking ở trạng thái: ' . $booking->status->label()
        );
    }

    // BƯỚC 2: FSM cho phép → cập nhật trạng thái
    $booking->update(['status' => BookingStatus::Confirmed]);
    
    return $booking;
}
```

**Mọi method trong BookingService đều gọi `canTransitionTo()` trước khi đổi trạng thái:**
```
confirm()  → canTransitionTo(Confirmed)    ← chỉ Pending mới confirm được
reject()   → canTransitionTo(Cancelled)    ← chỉ Pending mới reject được
start()    → canTransitionTo(InProgress)   ← chỉ Confirmed mới start được
complete() → canTransitionTo(Completed)    ← chỉ InProgress mới complete được
cancel()   → canTransitionTo(Cancelled)    ← chỉ Pending/Confirmed mới cancel được
```

#### Nếu KHÔNG có FSM?

```php
// ❌ Không có FSM — ai cũng có thể đổi status bất kỳ lúc nào
$booking->update(['status' => 'completed']);
// → Booking đang 'pending' mà nhảy thẳng 'completed'?! Chưa ai xác nhận!
// → Booking đang 'cancelled' mà bỗng 'completed'?! Đã hủy rồi mà!

// ✅ Có FSM — chặn mọi chuyển trạng thái sai
$booking->status->canTransitionTo(BookingStatus::Completed);
// Pending → Completed = false → throw exception → KHÔNG CHO PHÉP
```

> 🎯 **Tóm lại**: FSM = "cảnh sát giao thông" cho trạng thái — đảm bảo booking đi đúng luồng, không ai hack hay lỗi code gây nhảy trạng thái lung tung.

**Helper methods cho UI:**
```php
$booking->status->label();   // "Chờ xác nhận" — hiển thị tiếng Việt
$booking->status->color();   // "yellow"        — màu badge trong Blade
```

#### 4.2. `PaymentMethod` — Phương thức thanh toán

```php
enum PaymentMethod: string
{
    case Cash  = 'cash';    // Tiền mặt tại quán
    case VNPay = 'vnpay';   // Thanh toán VNPay
    case Momo  = 'momo';    // Ví MoMo
    
    public function label(): string { ... }  // Tên hiển thị TV
    public function icon(): string { ... }   // Icon Material Symbols
}
```

#### 4.3. `PaymentStatus` — Trạng thái thanh toán

```php
enum PaymentStatus: string
{
    case Pending  = 'pending';   // Chờ thanh toán
    case Paid     = 'paid';      // Đã thanh toán
    case Failed   = 'failed';    // Thất bại
    case Refunded = 'refunded';  // Đã hoàn tiền
}
```

#### 4.4. `TimeSlotStatus` — Trạng thái khung giờ

```php
enum TimeSlotStatus: string
{
    case Available = 'available';  // Trống — còn đặt được
    case Booked    = 'booked';     // Đã đặt — không khả dụng
}
```

#### 4.5. `UserRole` — Vai trò người dùng

```php
enum UserRole: string
{
    case Admin    = 'admin';     // Quản trị viên
    case Barber   = 'barber';    // Thợ cắt tóc
    case Customer = 'customer';  // Khách hàng
}
```

### Enum hoạt động với Model thế nào?

Trong Model, ta dùng **cast** để Laravel tự động convert string ↔ Enum:

```php
// Booking Model
protected function casts(): array
{
    return [
        'status' => BookingStatus::class,   // ← cast string → Enum
    ];
}
```

Khi đó:
```php
$booking->status;                        // BookingStatus::Pending (Enum object)
$booking->status->value;                 // "pending" (string)
$booking->status->label();               // "Chờ xác nhận"
$booking->status === BookingStatus::Pending;  // true ← so sánh an toàn
```

---

## 5. DTO (Data Transfer Object)

### DTO là gì?

DTO = **một class chỉ chứa dữ liệu**, không có logic phức tạp. Nó đóng gói dữ liệu từ request trước khi truyền vào Service.

### Tại sao cần DTO?

| Không có DTO ❌ | Có DTO ✅ |
|---|---|
| `$service->create($request->all())` | `$service->create(CreateBookingData::fromRequest($request))` |
| Service nhận `array` — không biết bên trong có gì | Service nhận object có kiểu rõ ràng |
| IDE không gợi ý `$data['barber_id']` | IDE gợi ý `$data->barber_id` |
| Dễ miss field, sai tên key | Có property declaration, compile-time check |
| Nếu đổi field → phải tìm mọi nơi dùng `$data['...']` | Đổi property DTO → IDE báo lỗi hết |

### Cấu trúc một DTO

```php
<?php

// File: app/DTOs/CreateBookingData.php

readonly class CreateBookingData    // ← readonly: khởi tạo xong không thể sửa
{
    public function __construct(
        public int $barber_id,       // ← khai báo rõ kiểu
        public int $time_slot_id,
        public array $service_ids,
        public ?string $note = null,        // ← nullable, có default
        public ?string $guest_name = null,
        public ?string $guest_email = null,
        public ?string $guest_phone = null,
    ) {}

    // Factory method: Tạo DTO từ request đã validate
    public static function fromRequest(StoreBookingRequest $request): self
    {
        $data = $request->validated();

        return new self(
            barber_id: $data['barber_id'],
            time_slot_id: $data['time_slot_id'],
            service_ids: $data['service_ids'],
            note: $data['note'] ?? null,
            guest_name: $data['guest_name'] ?? null,
            // ...
        );
    }
}
```

### Luồng DTO đi qua đâu?

```
      Request        →      Controller      →     Service      →   Model/DB
(dữ liệu thô từ form)   (tạo DTO từ request)  (dùng DTO.properties)  (insert/update)

   POST /booking     →   BookingController   →  BookingService  →  Booking::create()
                          │                      │
                          │ $dto = Create         │ $dto->barber_id
                          │ BookingData::         │ $dto->time_slot_id
                          │ fromRequest($req)     │ $dto->service_ids
```

### 6 DTO trong dự án

| DTO | Dùng ở đâu | Chứa gì |
|-----|-----------|---------|
| `CreateBookingData` | Client đặt lịch | barber_id, time_slot_id, service_ids, note, thông tin guest |
| `CreateBarberData` | Admin tạo barber | name, email, password, phone, bio, experience |
| `UpdateBarberData` | Admin sửa barber | Giống Create nhưng password optional |
| `StoreReviewData` | Client đánh giá | booking_id, rating, comment |
| `ScheduleItemData` | 1 ngày trong lịch | day_of_week, is_working, start_time, end_time |
| `UpdateScheduleData` | Cập nhật lịch tuần | Mảng 7 ScheduleItemData |

### Keyword `readonly`

```php
readonly class CreateBookingData { ... }
```

Nghĩa là: sau khi tạo xong DTO, **không ai có thể sửa dữ liệu bên trong**. Điều này đảm bảo dữ liệu đi xuyên suốt hệ thống luôn nhất quán.

---

## 6. Service Layer

### Service Layer là gì?

Là **lớp trung gian giữa Controller và Model**, chứa toàn bộ business logic.

### Tại sao cần Service Layer?

```
❌ Fat Controller:                          ✅ Thin Controller + Service:
Controller::store() {                       Controller::store() {
    validate();                                 $dto = DTO::fromRequest();
    $slot = TimeSlot::find();                   $booking = $service->create($dto);
    if ($slot->status !== ...) throw;           return redirect();
    $booking = Booking::create();           }
    $booking->services()->attach();         
    $slot->update();                        Service::create($dto) {
    Log::info();                                // Tất cả logic ở đây
    return redirect();                          // Dễ test, dễ tái sử dụng
}                                           }
```

### 9 Service trong dự án

| Service | Nhiệm vụ |
|---------|----------|
| `BookingService` | Tạo/confirm/reject/start/complete/cancel booking. Quản lý FSM. |
| `PaymentService` | Tạo URL thanh toán VNPay/MoMo. Verify callback. Xử lý idempotency. |
| `BarberService` | CRUD barber (tạo User + Barber trong transaction). Quản lý avatar. |
| `ScheduleService` | Đọc/hiển thị/cập nhật lịch làm việc. Dùng upsert tối ưu. |
| `TimeSlotService` | Tạo time slots từ lịch làm việc. Dọn slots cũ. |
| `ReviewService` | Tạo/xóa đánh giá. Cập nhật rating trung bình barber. |
| `ReportService` | Thống kê: tổng booking, doanh thu, khách mới, top barbers/services. |
| `ServiceService` | CRUD dịch vụ (cắt tóc, gội đầu...). |
| `CacheService` | Quản lý cache tập trung (xem phần 10). |

### Ví dụ Service pattern

```php
class BarberService
{
    // Inject dependency qua constructor
    public function __construct(private CacheService $cacheService) {}
    
    public function create(CreateBarberData $data, ?UploadedFile $avatar = null): Barber
    {
        // 1. Wrap trong transaction để đảm bảo tính nhất quán
        $barber = DB::transaction(function () use ($data, $avatar) {
            $user = User::create([...]);      // Tạo user
            return Barber::create([...]);      // Tạo barber
        });
        
        // 2. Xóa cache (vì dữ liệu đã thay đổi)
        $this->cacheService->clearBarberCache();
        
        return $barber;
    }
}
```

---

## 7. Luồng đặt lịch (Booking Flow)

### Toàn bộ lifecycle của một booking

```
            ┌──────── Guest/Khách đặt lịch ────────┐
            │                                        │
            ▼                                        │
    ┌───────────────┐                               │
    │    PENDING     │ ← Booking vừa tạo            │
    │  (Chờ xác nhận)│                               │
    └───────┬───────┘                               │
            │                                        │
     Barber xác nhận?                         Barber từ chối / Khách hủy
            │                                        │
            ▼                                        ▼
    ┌───────────────┐                       ┌───────────────┐
    │   CONFIRMED    │                       │   CANCELLED    │
    │  (Đã xác nhận) │──── Khách hủy ──────→│   (Đã hủy)    │
    └───────┬───────┘                       └───────────────┘
            │                                   (Slot mở lại)
     Barber bắt đầu phục vụ
            │
            ▼
    ┌───────────────┐
    │  IN_PROGRESS   │
    │ (Đang phục vụ) │
    └───────┬───────┘
            │
     Barber hoàn thành
            │
            ▼
    ┌───────────────┐
    │   COMPLETED    │
    │  (Hoàn thành)  │
    └───────────────┘
```

### Chi tiết luồng code

**1. Khách mở form đặt lịch:**
```
GET /booking/create → Client\BookingController::create()
    → Load danh sách services (is_active=true)
    → Load danh sách barbers (is_active=true, with user)
    → Return view('client.booking.create')
```

**2. Khách chọn barber + ngày → AJAX lấy slots:**
```
GET /booking/slots?barber_id=1&date=2026-03-24
    → Client\BookingController::getSlots()
    → Query TimeSlot where barber_id, slot_date, status=Available
    → Filter slots đã qua giờ (nếu ngày hôm nay)
    → Return JSON: [{id, start_time, end_time, label}, ...]
```

**3. Khách submit form đặt lịch:**
```
POST /booking
    → throttle:5,1 (chống spam)
    → StoreBookingRequest (validate)
    → CreateBookingData::fromRequest() → DTO
    → BookingService::create(DTO, user)
        → DB::transaction
            → TimeSlot::lockForUpdate() (pessimistic locking)
            → Check slot status === Available
            → Tính total_price, total_duration, end_time
            → Booking::create()
            → attach services (pivot: price_snapshot, duration_snapshot)
            → Slot → status = Booked
            → Log::channel('booking')
        → Return Booking
    → Redirect to Payment page
```

**4. Thanh toán (xem phần 8)**

**5. Barber xác nhận:**
```
PATCH /barber/bookings/{booking}/confirm
    → BookingPolicy::confirm() — kiểm tra quyền
    → BookingService::confirm()
        → canTransitionTo(Confirmed) — FSM check
        → Update status = Confirmed
        → Event: BookingConfirmed
            → Listener → Job (gửi notification async)
```

**6. Barber bắt đầu phục vụ:**
```
PATCH /barber/bookings/{booking}/start
    → BookingPolicy::start() — Chỉ confirmed mới start được
    → BookingService::start()
        → canTransitionTo(InProgress) — FSM check  
        → Update status = InProgress
```

**7. Barber hoàn thành:**
```
PATCH /barber/bookings/{booking}/complete
    → BookingPolicy::complete()
    → BookingService::complete()
        → canTransitionTo(Completed) — FSM check
        → Update status = Completed
        → Event: BookingCompleted → Notification cho khách
```

**8. Khách hủy:**
```
PATCH /booking/{booking}/cancel
    → BookingPolicy::cancel()
        → Chỉ khách hàng của booking
        → Chỉ Pending/Confirmed
        → Phải trước 2 tiếng (120 phút)
    → BookingService::cancel()
        → canTransitionTo(Cancelled) — FSM check
        → Update status, cancelled_at, cancel_reason
        → Mở lại slot (TimeSlotStatus::Available)
        → Event: BookingCancelled → Notification
```

### Các kỹ thuật quan trọng

| Kỹ thuật | Mục đích | Nơi dùng |
|----------|---------|----------|
| `DB::transaction` | Đảm bảo tất cả hoặc không gì xảy ra | BookingService::create(), cancel() |
| `lockForUpdate()` | Pessimistic locking, chống 2 người đặt cùng slot | BookingService::create() |
| `price_snapshot` | Ghi nhớ giá tại thời điểm đặt (giá sau có thể đổi) | booking_services pivot |
| FSM `canTransitionTo()` | Kiểm soát chuyển trạng thái hợp lệ | Mọi method trong BookingService |

### 🔒 Xử lý Race Condition — 2 người đặt cùng slot cùng lúc

#### Race Condition là gì?

Race Condition = **2 tiến trình chạy đồng thời, tranh nhau tài nguyên chung**, dẫn đến kết quả sai nếu không có cơ chế kiểm soát.

**Ví dụ cụ thể trong dự án:**
- Slot 10:00 sáng của barber Tuấn chỉ còn **1 chỗ trống**.
- Khách A và Khách B **cùng lúc** nhấn "Đặt lịch" chọn slot này.
- Nếu không xử lý → **cả 2 đều đặt thành công** → barber có 2 lịch hẹn 10:00 → sai!

#### Cách dự án xử lý: 3 lớp bảo vệ

```php
// BookingService::create() — File: app/Services/BookingService.php

public function create(CreateBookingData $data, ?User $customer = null): Booking
{
    // ┌─ LỚP 1: DB Transaction ──────────────────────────────────┐
    // │ Đảm bảo tất cả thao tác DB thành công hoặc rollback hết │
    return DB::transaction(function () use ($data, $customer) {
    
        // ┌─ LỚP 2: Pessimistic Locking ────────────────────────┐
        // │ lockForUpdate() = khóa hàng trong DB                 │
        // │ Ai đến trước → giữ khóa, người sau phải CHỜ         │
        $slot = TimeSlot::lockForUpdate()->findOrFail($data->time_slot_id);
        //                 ^^^^^^^^^^^^^^
        //                 SQL: SELECT * FROM time_slots WHERE id=? FOR UPDATE
        //                 → Hàng bị LOCK cho đến khi transaction COMMIT/ROLLBACK
        
        // ┌─ LỚP 3: Status Check ──────────────────────────────┐
        // │ Sau khi có lock, kiểm tra slot còn available không   │
        if ($slot->status !== TimeSlotStatus::Available) {
            throw new SlotNotAvailableException(
                'Slot này vừa được đặt, vui lòng chọn lại.'
            );
        }
        // └──────────────────────────────────────────────────────┘
        
        // ... tạo booking, attach services ...
        
        $slot->update(['status' => TimeSlotStatus::Booked]);
        //              ^^^^^^^^^ Đổi sang Booked → người sau sẽ thấy Booked
        
    }); // ← COMMIT transaction → giải lock
}
```

#### Timeline chi tiết: 2 người đặt cùng slot cùng lúc

```
Thời gian │  Khách A (request trước vài ms)     │  Khách B (request sau vài ms)
──────────┼──────────────────────────────────────┼─────────────────────────────────────
T1        │  POST /booking                       │  POST /booking
T2        │  BEGIN TRANSACTION                   │  BEGIN TRANSACTION
T3        │  SELECT * FROM time_slots            │  SELECT * FROM time_slots
          │  WHERE id=5 FOR UPDATE               │  WHERE id=5 FOR UPDATE
          │  → ✅ Lấy được lock!                 │  → ⏳ BỊ CHẶN (hàng đang bị A lock)
          │                                      │     A chưa commit → B phải chờ
T4        │  status = 'available' → OK ✅        │  (vẫn đang chờ...)
T5        │  Booking::create() ✅                │  (vẫn đang chờ...)
T6        │  services()->attach() ✅             │  (vẫn đang chờ...)
T7        │  slot → status = 'booked' ✅         │  (vẫn đang chờ...)
T8        │  Log::info() ✅                      │  (vẫn đang chờ...)
T9        │  COMMIT → giải lock 🔓               │  → Lock được giải! Đọc slot
T10       │  Redirect → Payment page ✅          │  status = 'booked' → ❌ FAIL!
T11       │                                      │  throw SlotNotAvailableException
T12       │                                      │  ROLLBACK transaction
T13       │                                      │  Redirect back + lỗi:
          │                                      │  "Slot vừa được đặt, chọn lại." 
```

**Kết quả:**
- ✅ Khách A: đặt thành công, chuyển sang trang thanh toán
- ❌ Khách B: nhận thông báo lỗi, quay lại form chọn slot khác
- ✅ **Không bao giờ** có 2 booking trùng slot

#### Nếu KHÔNG có `lockForUpdate()`?

```
Thời gian │  Khách A                             │  Khách B
──────────┼──────────────────────────────────────┼────────────────────────────────
T1        │  SELECT * FROM time_slots WHERE id=5 │  SELECT * FROM time_slots WHERE id=5
          │  status = 'available' → OK ✅        │  status = 'available' → OK ✅
          │  (CẢ HAI đều thấy slot trống!)       │  (CẢ HAI đều thấy slot trống!)
T2        │  Booking::create() ← booking #1      │  Booking::create() ← booking #2
T3        │  slot → 'booked'                     │  slot → 'booked'
          │                                      │
          │  ❌ 2 BOOKING CHO CÙNG 1 SLOT!       │  ❌ RACE CONDITION XẢY RA!
```

#### Tóm tắt 3 lớp bảo vệ

```
┌──────────────────────────────────────────────────────────────┐
│ LỚP 1: DB::transaction                                      │
│ → Nếu bất kỳ bước nào fail → ROLLBACK toàn bộ              │
│ → Không bao giờ có trạng thái "nửa chừng"                   │
│                                                              │
│   ┌──────────────────────────────────────────────────────┐   │
│   │ LỚP 2: lockForUpdate()                              │   │
│   │ → Khóa hàng slot trong DB ở cấp database            │   │
│   │ → Request thứ 2 PHẢI CHỜ request thứ 1 commit       │   │
│   │ → Đây là "Pessimistic Locking" (bi quan = giả sử    │   │
│   │   sẽ có xung đột → khóa trước cho chắc)             │   │
│   │                                                      │   │
│   │   ┌──────────────────────────────────────────────┐   │   │
│   │   │ LỚP 3: Status Check                         │   │   │
│   │   │ → Sau khi có lock, kiểm tra lại status       │   │   │
│   │   │ → Nếu 'booked' → throw Exception            │   │   │
│   │   │ → User nhận thông báo "slot đã hết"          │   │   │
│   │   └──────────────────────────────────────────────┘   │   │
│   └──────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────┘
```

---

## 8. Luồng thanh toán (Payment Flow)

### Tổng quan 3 phương thức

```
┌─────────────────────────────────────────────────────────┐
│                  Chọn phương thức                        │
│                                                         │
│  ┌──────────┐   ┌──────────┐   ┌──────────┐           │
│  │  💵 Cash  │   │ 💳 VNPay │   │ 📱 MoMo  │           │
│  └────┬─────┘   └────┬─────┘   └────┬─────┘           │
│       │              │              │                   │
│       ▼              ▼              ▼                   │
│  Ghi Payment    Redirect sang    Redirect sang          │
│  status=Pending  VNPay Sandbox   MoMo Sandbox          │
│  → Confirmation  → Thanh toán    → Thanh toán           │
│                  → Callback URL  → Callback URL         │
│                  → Verify HMAC   → Verify HMAC          │
│                  → Update status → Update status        │
└─────────────────────────────────────────────────────────┘
```

### Luồng VNPay chi tiết

```
1. Khách chọn VNPay → POST /payment/{booking}
   → PaymentService::createPendingPayment() — Tạo Payment record (status=Pending)
   → PaymentService::createVNPayUrl() — Tạo URL thanh toán
       → Ký dữ liệu bằng HMAC SHA512 (vnpHashSecret)
       → Tạo vnp_TxnRef = "paymentId_timestamp"
   → Redirect khách sang VNPay Sandbox

2. Khách thanh toán trên VNPay → VNPay redirect về app
   → GET /payment/vnpay/return
   → PaymentService::verifyVNPayCallback()
       → Verify chữ ký HMAC SHA512 (chống fake request)
       → Tìm Payment từ vnp_TxnRef
       → Idempotency check: đã xử lý rồi thì return ngay
       → vnp_ResponseCode === '00' → SUCCESS
           → Payment.status = Paid, transaction_id, paid_at
       → Khác '00' → FAIL
           → Payment.status = Failed

3. VNPay cũng gọi IPN (server-to-server)
   → POST /payment/vnpay/ipn (withoutMiddleware CSRF)
   → Verify + cập nhật tương tự
```

### Luồng MoMo tương tự

```
1. Tạo signature HMAC SHA256
2. Gọi API MoMo (HTTP POST) → nhận payUrl
3. Redirect khách sang payUrl
4. MoMo callback → verify signature → update Payment status
```

### Bảo mật thanh toán

| Biện pháp | Chi tiết |
|-----------|---------|
| HMAC signature | VNPay = SHA512, MoMo = SHA256. Chống giả mạo callback. |
| Idempotency | Nếu payment đã Paid/Failed → return kết quả cũ, không xử lý lại. |
| CSRF bypass | IPN route tắt CSRF vì request từ VNPay server, không có token. |
| Transaction ref | `paymentId_timestamp` — unique mỗi giao dịch. |

---

## 9. Hệ thống Event / Listener / Job

### Tại sao cần Event system?

Thay vì viết tất cả logic trong Service:
```php
// ❌ Service phình to, khó maintain
public function confirm(Booking $booking) {
    $booking->update(['status' => 'confirmed']);
    $notification = Notification::create([...]);   // Side effect
    // Nếu thêm: gửi email, SMS, Slack... → Service phình thêm
}
```

Ta tách side effects ra Event/Listener:
```php
// ✅ Service gọn, side effects tách riêng
public function confirm(Booking $booking) {
    $booking->update(['status' => BookingStatus::Confirmed]);
    event(new BookingConfirmed($booking));   // Phát sự kiện, không quan tâm ai xử lý
}
```

### Luồng Event → Listener → Job

```
BookingService::confirm()
    │
    ├─ update status
    │
    └─ event(new BookingConfirmed($booking))
            │
            ▼
    AppServiceProvider đã đăng ký:
    Event::listen(BookingConfirmed::class, SendBookingConfirmedNotification::class)
            │
            ▼
    SendBookingConfirmedNotification::handle()
        │
        ├─ Tạo message: "Lịch hẹn #BB-... đã được xác nhận bởi ..."
        │
        └─ SendBookingNotificationJob::dispatch($customerId, $message)
                │  ← Đưa vào queue, xử lý async
                ▼
            Job::handle()
                │
                └─ Notification::create([...])  ← Ghi vào DB
```

### 3 Event trong dự án

| Event | Khi nào phát | Listener làm gì |
|-------|-------------|-----------------|
| `BookingConfirmed` | Barber xác nhận booking | Gửi notification cho khách |
| `BookingCancelled` | Barber từ chối / Khách hủy | Gửi notification cho khách |
| `BookingCompleted` | Barber hoàn thành | Gửi notification cho khách |

### Job — Xử lý bất đồng bộ

```php
class SendBookingNotificationJob implements ShouldQueue  // ← ShouldQueue = async
{
    use Queueable;
    
    public function handle(): void
    {
        Notification::create([
            'user_id' => $this->userId,
            'message' => $this->message,
        ]);
    }
}
```

> 🎯 **Tại sao dùng Job?**  
> Nếu ghi notification **đồng bộ** trong Listener → request chậm hơn.  
> Dùng Job, notification được đưa vào **queue** → xử lý sau → response nhanh hơn.

---

## 10. Caching — CacheService

### Cache là gì? Ví dụ đời thực

Hãy tưởng tượng bạn đang ở **quán cà phê**:

> **Không có cache**: Mỗi lần khách hỏi "có bao nhiêu món?", nhân viên phải chạy vào kho đếm lại từ đầu → mất 5 phút.  
> **Có cache**: Lần đầu đếm xong, ghi ra **bảng menu treo tường** → khách hỏi lại thì nhìn bảng → 1 giây.  
> **Cache hết hạn (TTL)**: Mỗi 1 tiếng xóa bảng cũ, đếm lại → đảm bảo thông tin không quá cũ.  
> **Cache invalidation**: Thêm món mới → **xóa bảng cũ ngay lập tức** → đếm lại.

Trong code cũng vậy:

```
Không cache:  Request → Query DB (chậm, ~50ms) → Response
Có cache:     Request → Đọc từ RAM/File (nhanh, ~1ms) → Response
```

### Tại sao cần Cache?

| Không cache ❌ | Có cache ✅ |
|---|---|
| Mỗi request đều query DB | Đọc từ bộ nhớ nhanh, không cần DB |
| 100 user = 100 lần query giống nhau | 100 user = **1 lần** query, 99 lần đọc cache |
| Chậm khi nhiều user đồng thời | Nhanh gấp **10-50 lần** |
| DB chịu tải cao | DB nhẹ nhàng |

### 2 method quan trọng nhất

#### `Cache::remember()` — Lấy dữ liệu (tự động cache)

```php
$result = Cache::remember('cache_key', $seconds, function () {
    // Code chỉ chạy KHI CHƯA CÓ CACHE
    return DB::table('services')->get();
});
```

**Logic bên trong:**
```
Cache::remember('active_services', 3600, fn() => query DB)
    │
    ├── Cache có key 'active_services'?
    │       │
    │       ├── CÓ (cache hit) → return dữ liệu từ cache (NHANH!)
    │       │
    │       └── KHÔNG (cache miss) → chạy fn() → query DB
    │                                   │
    │                                   ├── Lưu kết quả vào cache với key 'active_services'
    │                                   │   (tự xóa sau 3600 giây)
    │                                   │
    │                                   └── return kết quả
```

#### `Cache::forget()` — Xóa cache (khi dữ liệu thay đổi)

```php
Cache::forget('active_services');
// Lần sau gọi Cache::remember() → cache miss → query DB lại → cache mới
```

### TTL (Time To Live) là gì?

TTL = **thời gian cache tồn tại** trước khi tự hết hạn. Sau TTL, cache tự xóa → lần sau phải query DB lại.

```
TTL = 3600 giây (1 giờ)

Timeline:
0s     → Cache::remember() → cache miss → query DB → lưu cache
10s    → Cache::remember() → cache hit ✅ → return từ cache
1800s  → Cache::remember() → cache hit ✅ → return từ cache
3600s  → Cache hết hạn, tự xóa
3601s  → Cache::remember() → cache miss → query DB lại → lưu cache mới
```

**Chọn TTL thế nào?**

| Loại dữ liệu | TTL gợi ý | Lý do |
|--------------|-----------|-------|
| Dữ liệu gần như không đổi (danh mục, cấu hình) | 2-24 giờ | Ít thay đổi |
| Dữ liệu thay đổi vài lần/ngày (danh sách sản phẩm) | 30-60 phút | Cân bằng tốc độ vs độ tươi |
| Dữ liệu thay đổi thường xuyên (báo cáo) | 5-15 phút | Cần tương đối mới |
| Dữ liệu thay đổi liên tục (giỏ hàng, trạng thái) | ❌ KHÔNG CACHE | Luôn cần chính xác |

### CacheService — Quản lý tập trung

```php
class CacheService
{
    // ── BƯỚC 1: Định nghĩa keys + TTL tại 1 nơi duy nhất ──
    private const KEY_ACTIVE_SERVICES = 'active_services';
    private const KEY_ACTIVE_BARBERS = 'active_barbers';
    private const TTL_SERVICES = 3600;     // 1 giờ
    private const TTL_BARBERS = 1800;      // 30 phút

    // ── BƯỚC 2: Method lấy dữ liệu (có cache) ──
    public function getActiveServices()
    {
        return Cache::remember(
            self::KEY_ACTIVE_SERVICES,     // key
            self::TTL_SERVICES,            // TTL: 3600 giây
            function () {
                // Closure này CHỈ chạy khi cache miss
                return Service::where('is_active', true)
                    ->orderBy('name')
                    ->get();
            }
        );
    }

    // ── BƯỚC 3: Method xóa cache (gọi khi dữ liệu thay đổi) ──
    public function clearServiceCache(): void
    {
        Cache::forget(self::KEY_ACTIVE_SERVICES);
    }
}
```

### Thực tế: Cache được dùng ở đâu trong code?

**1. BarberService — Xóa cache khi tạo/sửa/xóa barber:**

```php
// File: app/Services/BarberService.php

class BarberService
{
    public function __construct(private CacheService $cacheService) {}
    //                                  ^^^^^^^^^^^^ Inject CacheService

    public function create(CreateBarberData $data): Barber
    {
        $barber = DB::transaction(function () use ($data) {
            $user = User::create([...]);
            return Barber::create([...]);
        });

        // Tạo barber mới → cache danh sách barber cũ KHÔNG CÒN ĐÚNG
        // → Xóa cache để lần sau query DB lấy danh sách mới
        $this->cacheService->clearBarberCache();

        return $barber;
    }

    public function delete(Barber $barber): void
    {
        DB::transaction(function () use ($barber) {
            $barber->user->delete();
        });

        $this->cacheService->clearBarberCache();  // Xóa cache khi xóa barber
    }
}
```

**2. Booking form — Dùng cache cho danh sách dịch vụ:**

```
Khách mở form đặt lịch → cần hiển thị danh sách dịch vụ
    │
    ▼
$cacheService->getActiveServices()
    │
    ├── Có cache? → Trả về ngay (1ms) ✅
    │
    └── Không cache? → SELECT * FROM services WHERE is_active=1 (50ms)
                       → Lưu cache → Trả về
```

### Sơ đồ tổng thể luồng cache

```
                    Lần 1 (cache miss)
Request ──→ CacheService ──→ Cache::remember()
                                  │
                              key không tồn tại
                                  │
                              Query DB: SELECT * FROM services...
                                  │
                              Lưu kết quả vào cache (TTL=3600s)
                                  │
                              Return kết quả cho user

                    Lần 2-1000 (cache hit)
Request ──→ CacheService ──→ Cache::remember()
                                  │
                              key tồn tại + chưa hết hạn
                                  │
                              Return từ cache (NHANH! không query DB)

                    Admin sửa dịch vụ
Admin POST ──→ ServiceController ──→ ServiceService::update()
                                          │
                                      Update DB
                                          │
                                      $cacheService->clearServiceCache()
                                          │
                                      Cache::forget('active_services')

                    Lần tiếp theo (cache miss do vừa xóa)
Request ──→ CacheService ──→ Cache::remember()
                                  │
                              key không tồn tại (vừa bị forget)
                                  │
                              Query DB lại → lấy dữ liệu MỚI
                                  │
                              Lưu cache mới → Return
```

### 3 loại cache trong dự án

| Cache Key | TTL | Dữ liệu | Xóa khi |
|-----------|-----|---------|---------|
| `active_services` | 1 giờ | Danh sách dịch vụ đang hoạt động | Admin tạo/sửa/xóa dịch vụ |
| `active_barbers` | 30 phút | Danh sách thợ đang hoạt động | Admin tạo/sửa/xóa thợ |
| `report_*` | 15 phút | Kết quả báo cáo thống kê | Admin xem báo cáo mới |

### Tại sao quản lý cache TẬP TRUNG trong CacheService?

```
❌ Cache rải rác (mỗi nơi tự viết key):
    Controller A: Cache::remember('services', ...)
    Controller B: Cache::forget('service_list')   ← SAI KEY! Cache cũ KHÔNG bị xóa
    → Bug: user thấy dữ liệu cũ mãi

✅ Cache tập trung (CacheService quản lý key):
    Controller A: $cacheService->getActiveServices()     ← key nằm BÊN TRONG CacheService
    Controller B: $cacheService->clearServiceCache()     ← cũng dùng key BÊN TRONG
    → Không bao giờ sai key vì DEV không cần biết key là gì
```

### Khi nào KHÔNG nên cache?

```
❌ Dữ liệu thay đổi mỗi giây       → Giỏ hàng, session
❌ Dữ liệu cần chính xác real-time  → Số dư ví, trạng thái thanh toán
❌ Dữ liệu khác nhau theo user      → Profile riêng (trừ khi cache theo user_id)
❌ Dữ liệu rất nhỏ, query rất nhanh → SELECT COUNT(*) đơn giản

✅ Danh sách ít thay đổi             → Dịch vụ, danh mục, barbers
✅ Kết quả tính toán phức tạp        → Báo cáo, thống kê
✅ Dữ liệu GIỐNG NHAU cho mọi user  → Menu, cấu hình hệ thống
```

---

## 11. Middleware — Bộ lọc request

Middleware = **code chạy TRƯỚC và/hoặc SAU** khi request đến Controller. Dùng để lọc, kiểm tra, bổ sung thông tin.

### 3 Middleware tự viết

#### 11.1. `RoleMiddleware` — Phân quyền theo vai trò

```php
// Đăng ký trong route:
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Chỉ admin mới vào được
});

Route::middleware(['auth', 'role:barber,admin'])->group(function () {
    // Barber HOẶC admin
});
```

```php
// Cách hoạt động:
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    // Convert string → Enum
    $allowedRoles = array_map(fn ($role) => UserRole::from($role), $roles);
    
    // Check: đã login VÀ role nằm trong danh sách cho phép
    if (!auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
        abort(403);   // Forbidden
    }
    
    return $next($request);   // Cho qua
}
```

#### 11.2. `LogActivity` — Ghi log thay đổi dữ liệu

```php
// Chỉ log POST/PUT/PATCH/DELETE (bỏ qua GET để giảm noise)
// Ghi: user_id, role, method, url, ip, status, duration
```

#### 11.3. `SecurityHeaders` — HTTP security headers

```php
// Thêm vào mọi response:
X-Content-Type-Options: nosniff         // Chống MIME sniffing
X-Frame-Options: DENY                   // Chống clickjacking
X-XSS-Protection: 1; mode=block        // Chống XSS (trình duyệt cũ)
Referrer-Policy: strict-origin-...      // Kiểm soát referrer
Permissions-Policy: camera=(), ...      // Tắt quyền truy cập thiết bị
```

### Sơ đồ pipeline Middleware

```
Request
    │
    ▼
┌─────────────────┐
│ SecurityHeaders  │  Thêm headers bảo mật vào response
├─────────────────┤
│   auth           │  Kiểm tra đã login chưa
├─────────────────┤
│   role:admin     │  Kiểm tra có phải admin không
├─────────────────┤
│   LogActivity    │  Ghi log hành động (chỉ POST/PUT/PATCH/DELETE)
├─────────────────┤
│   throttle:5,1   │  Rate limit: max 5 req/phút
└────────┬────────┘
         │
         ▼
    Controller
```

---

## 12. Policy — Phân quyền chi tiết

### Policy là gì?

Hãy tưởng tượng thế này:

> **Middleware** giống như **bảo vệ ở cổng tòa nhà**: "Anh là nhân viên (admin) thì vào, không phải thì đi về."  
> **Policy** giống như **khóa phòng bên trong tòa nhà**: "Anh là nhân viên, nhưng chỉ được vào phòng CỦA ANH, không được vào phòng người khác."

Policy = **quy tắc kiểm tra quyền trên từng record cụ thể** (từng booking, từng đơn hàng...).

### So sánh chi tiết: Middleware vs Policy

| | Middleware `role:admin` | Policy |
|--|------------------------|--------|
| **Câu hỏi** | "User có PHẢI role này không?" | "User có ĐƯỢC PHÉP làm hành động này VỚI record này không?" |
| **Phạm vi** | Kiểm tra **cả route/trang** | Kiểm tra **từng record** |
| **Ví dụ** | "Chỉ admin vào trang quản lý" | "Barber A chỉ xác nhận booking CỦA barber A" |
| **Vị trí** | Chạy TRƯỚC Controller | Chạy BÊN TRONG Controller |
| **Return** | Cho qua hoặc abort(403) | `true` (cho phép) hoặc `false` (cấm) |

### Ví dụ thực tế để hiểu rõ

Hệ thống có 3 barber: **Tuấn**, **Minh**, **Hùng**.

```
Middleware role:barber bảo vệ route /barber/bookings/{booking}/confirm
    → Cả 3 barber đều QUA ĐƯỢC middleware (vì đều là role barber)

NHƯNG:
    → Booking #10 là của barber Tuấn
    → Minh vào /barber/bookings/10/confirm → KHÔNG ĐƯỢC! (không phải booking của Minh)
    → Policy kiểm tra: user.barber.id === booking.barber_id → false → abort(403)
```

```
                       Middleware                          Policy
                    ┌─────────────┐                  ┌─────────────┐
Tuấn (barber) ────▶│ role:barber  │──── PASS ✅ ───▶│ confirm()   │──── booking CỦA Tuấn? ✅ PASS
                    │             │                  │             │
Minh (barber) ────▶│ role:barber  │──── PASS ✅ ───▶│ confirm()   │──── booking CỦA Minh? ❌ DENY
                    │             │                  │             │
Khách (customer) ──▶│ role:barber  │──── DENY ❌     │             │    (không bao giờ đến đây)
                    └─────────────┘                  └─────────────┘
```

### BookingPolicy — Phân tích từng method

```php
// File: app/Policies/BookingPolicy.php

class BookingPolicy
{
    /**
     * Barber có được XÁC NHẬN booking này không?
     * Điều kiện:
     *   1. User phải là barber (có bản ghi trong bảng barbers)
     *   2. Booking phải thuộc VỀ barber này (không phải barber khác)
     *   3. Booking phải đang ở trạng thái Pending (chưa ai xác nhận)
     */
    public function confirm(User $user, Booking $booking): bool
    {
        return $user->barber                                    // 1. User là barber?
            && $user->barber->id === $booking->barber_id        // 2. Booking của barber này?
            && $booking->status === BookingStatus::Pending;     // 3. Đang pending?
    }

    /**
     * Barber có được TỪ CHỐI booking này không?
     * Điều kiện giống confirm: phải là barber của booking + đang Pending
     */
    public function reject(User $user, Booking $booking): bool
    {
        return $user->barber 
            && $user->barber->id === $booking->barber_id
            && $booking->status === BookingStatus::Pending;
    }

    /**
     * Barber có được BẮT ĐẦU PHỤC VỤ booking này không?
     * Điều kiện: là barber của booking + booking đã Confirmed
     */
    public function start(User $user, Booking $booking): bool
    {
        return $user->barber 
            && $user->barber->id === $booking->barber_id
            && $booking->status === BookingStatus::Confirmed;  // Phải confirmed trước
    }

    /**
     * Barber có được HOÀN THÀNH booking này không?
     * Điều kiện: là barber của booking + đang InProgress
     */
    public function complete(User $user, Booking $booking): bool
    {
        return $user->barber 
            && $user->barber->id === $booking->barber_id
            && $booking->status === BookingStatus::InProgress;  // Phải đang phục vụ
    }

    /**
     * Khách hàng có được HỦY booking này không?
     * Điều kiện phức tạp hơn:
     *   1. Phải là khách hàng CỦA booking này
     *   2. Booking phải đang Pending hoặc Confirmed
     *   3. Phải hủy TRƯỚC giờ hẹn ít nhất 2 tiếng
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // 1. Chỉ khách hàng của booking
        if ($user->id !== $booking->customer_id) {
            return false;
        }

        // 2. Chỉ hủy khi Pending hoặc Confirmed
        if (!in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) {
            return false;
        }

        // 3. Phải trước giờ hẹn ít nhất 2 tiếng (120 phút)
        //    VD: Hẹn 10:00 → phải hủy trước 8:00
        $appointmentTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
        return now()->diffInMinutes($appointmentTime, false) >= 120;
    }
}
```

### Cách gọi Policy trong Controller

```php
// Barber\BookingController.php

class BookingController extends Controller
{
    use AuthorizesRequests;  // ← Cần trait này để dùng $this->authorize()

    public function confirm(Booking $booking): RedirectResponse
    {
        // authorize('tên_method_trong_policy', $model)
        $this->authorize('confirm', $booking);
        // ↑ Laravel tự động:
        //   1. Tìm BookingPolicy (vì truyền $booking là Booking model)
        //   2. Gọi BookingPolicy::confirm(auth()->user(), $booking)
        //   3. Nếu return false → abort(403) Forbidden
        //   4. Nếu return true → tiếp tục ↓

        $this->bookingService->confirm($booking);
        return back()->with('success', 'Đã xác nhận lịch hẹn.');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('reject', $booking);  // Policy check

        $this->bookingService->reject($booking, $request->input('cancel_reason'));
        return back()->with('success', 'Đã từ chối lịch hẹn.');
    }
}
```

### Luồng authorize hoạt động thế nào?

```
$this->authorize('confirm', $booking)
         │              │         │
         │              │         └── Model → Laravel biết tìm BookingPolicy
         │              └──────────── Tên method trong Policy
         │
         ▼
┌─────────────────────────────────────────────────────┐
│ Laravel tự động làm:                                 │
│                                                      │
│ 1. $booking là Booking model                         │
│    → Tìm file: App\Policies\BookingPolicy            │
│                                                      │
│ 2. Gọi: BookingPolicy::confirm(auth()->user(), $booking) │
│                                                      │
│ 3. Kết quả:                                          │
│    return true  → ✅ tiếp tục code phía dưới        │
│    return false → ❌ abort(403) Forbidden            │
└─────────────────────────────────────────────────────┘
```

### Laravel tìm Policy thế nào?

Laravel dùng **quy ước đặt tên** (convention) để tự động ghép Model ↔ Policy:

| Model | Policy (Laravel tự tìm) |
|-------|------------------------|
| `App\Models\Booking` | `App\Policies\BookingPolicy` |
| `App\Models\Order` | `App\Policies\OrderPolicy` |
| `App\Models\Product` | `App\Policies\ProductPolicy` |

Quy tắc: **Tên Model + "Policy"** → `Booking` + `Policy` = `BookingPolicy`.

### Nếu KHÔNG có Policy thì sao?

```php
// ❌ Không dùng Policy — kiểm tra thủ công trong Controller
public function confirm(Booking $booking)
{
    $user = auth()->user();
    
    if (!$user->barber) {
        abort(403);
    }
    if ($user->barber->id !== $booking->barber_id) {
        abort(403);
    }
    if ($booking->status !== BookingStatus::Pending) {
        abort(403);
    }
    
    // Logic xác nhận...
}

// ❌ Vấn đề: copy-paste logic này ở MỌI method (confirm, reject, start, complete)
//    → Code lặp, khó maintain, dễ quên
```

```php
// ✅ Dùng Policy — 1 dòng duy nhất, logic tập trung
public function confirm(Booking $booking)
{
    $this->authorize('confirm', $booking);  // ← Sạch, gọn, rõ ràng
    
    // Logic xác nhận...
}
```

### Tóm tắt: Khi nào dùng Policy?

```
□ Khi cần kiểm tra "user này có quyền trên RECORD CỤ THỂ này không?"
□ Khi logic phân quyền phức tạp (nhiều điều kiện: role + ownership + status + thời gian)
□ Khi cùng 1 kiểu kiểm tra lặp lại ở nhiều Controller methods
□ Khi muốn tách logic phân quyền ra khỏi Controller cho gọn
```



## 13. Console Commands — Tác vụ nền

4 Artisan commands chạy tự động (cron):

| Command | Chức năng | Schedule |
|---------|----------|----------|
| `slots:generate` | Tạo time slots cho 7 ngày tới dựa trên lịch làm việc | Hàng ngày |
| `bookings:expire` | Tự động hủy booking Pending quá 24h | Mỗi giờ |
| `logs:cleanup` | Xóa log files cũ hơn 30 ngày | Hàng tuần |
| `security:audit` | Kiểm tra bảo mật (permission, env, packages) | Hàng tuần |

### Ví dụ: `GenerateTimeSlots`

```
1. Lấy tất cả barbers có is_active=true
2. Với mỗi barber:
   - Đọc WorkingSchedule (lịch 7 ngày trong tuần)
   - Cho 7 ngày tới, nếu barber làm việc ngày đó:
     - Tạo time slots mỗi 30 phút từ start_time → end_time
     - Skip slots trùng (đã tồn tại)
```

---

## 14. Database Schema — Sơ đồ cơ sở dữ liệu

### Sơ đồ quan hệ (ER Diagram)

```mermaid
erDiagram
    users {
        bigint id PK
        varchar name
        varchar email UK
        varchar phone
        varchar avatar
        enum role "customer | barber | admin"
        varchar password
        boolean is_active
        timestamp email_verified_at
    }

    barbers {
        bigint id PK
        bigint user_id FK "→ users (cascade)"
        text bio
        tinyint experience_years
        decimal rating "0.00 - 5.00"
        boolean is_active
    }

    services {
        bigint id PK
        varchar name
        text description
        decimal price "VND"
        int duration_minutes
        varchar image
        boolean is_active
    }

    working_schedules {
        bigint id PK
        bigint barber_id FK "→ barbers (cascade)"
        tinyint day_of_week "0=CN 1=T2...6=T7"
        time start_time
        time end_time
        boolean is_day_off
    }

    time_slots {
        bigint id PK
        bigint barber_id FK "→ barbers (cascade)"
        date slot_date
        time start_time
        time end_time
        enum status "available | booked | blocked"
    }

    bookings {
        bigint id PK
        varchar booking_code UK
        bigint customer_id FK "→ users (cascade)"
        bigint barber_id FK "→ barbers (cascade)"
        bigint time_slot_id FK "→ time_slots (restrict)"
        date booking_date
        time start_time
        time end_time
        decimal total_price
        enum status "FSM: pending→confirmed→..."
        text note
        timestamp cancelled_at
        text cancel_reason
    }

    booking_services {
        bigint id PK
        bigint booking_id FK "→ bookings (cascade)"
        bigint service_id FK "→ services (restrict)"
        decimal price_snapshot "Gia tai thoi diem dat"
        int duration_snapshot "Thoi gian tai thoi diem dat"
    }

    payments {
        bigint id PK
        bigint booking_id FK_UK "→ bookings (cascade, unique)"
        decimal amount
        enum method "cash | vnpay | momo"
        enum status "pending | paid | failed | refunded"
        varchar transaction_id
        timestamp paid_at
    }

    reviews {
        bigint id PK
        bigint booking_id FK_UK "→ bookings (cascade, unique)"
        bigint customer_id FK "→ users (cascade)"
        bigint barber_id FK "→ barbers (cascade)"
        tinyint rating "1-5 sao"
        text comment
    }

    notifications {
        bigint id PK
        bigint user_id FK "→ users (cascade)"
        varchar type
        varchar title
        text message
        boolean is_read
    }

    users ||--o| barbers : "1 user co the la 1 barber"
    users ||--o{ bookings : "1 user dat nhieu booking"
    users ||--o{ notifications : "1 user nhan nhieu thong bao"
    users ||--o{ reviews : "1 user viet nhieu review"

    barbers ||--o{ working_schedules : "1 barber co 7 ngay lich"
    barbers ||--o{ time_slots : "1 barber co nhieu slot"
    barbers ||--o{ bookings : "1 barber co nhieu booking"
    barbers ||--o{ reviews : "1 barber co nhieu review"

    time_slots ||--o| bookings : "1 slot gan 1 booking"

    bookings ||--o{ booking_services : "1 booking co nhieu dich vu"
    services ||--o{ booking_services : "1 dich vu thuoc nhieu booking"

    bookings ||--o| payments : "1 booking co 1 thanh toan"
    bookings ||--o| reviews : "1 booking co 1 danh gia"
```

### Đọc sơ đồ thế nào?

| Ký hiệu | Nghĩa | Ví dụ |
|---------|-------|-------|
| `\|\|--o\|` | 1 đối 0 hoặc 1 | users ↔ barbers (1 user có thể là barber hoặc không) |
| `\|\|--o{` | 1 đối nhiều (0..N) | users ↔ bookings (1 user đặt 0 hoặc nhiều booking) |
| `PK` | Primary Key | Khóa chính |
| `FK` | Foreign Key | Khóa ngoại (liên kết bảng khác) |
| `UK` | Unique Key | Giá trị duy nhất |

### Bảng chi tiết từng table

#### `users` — Người dùng (customer, barber, admin)

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | Auto increment |
| name | varchar(255) | Tên hiển thị |
| email | varchar(255) UNIQUE | Email đăng nhập |
| phone | varchar(20) NULL | Số điện thoại |
| avatar | varchar(255) NULL | Đường dẫn ảnh đại diện |
| role | enum('customer','barber','admin') | Vai trò, default: customer |
| is_active | boolean | Tài khoản còn hoạt động? |
| password | varchar(255) | Bcrypt hash |
| email_verified_at | timestamp NULL | Xác thực email |

#### `barbers` — Thợ cắt tóc (mở rộng từ users)

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| user_id | FK → users | **1-1**: mỗi barber gắn 1 user, cascade delete |
| bio | text NULL | Giới thiệu bản thân |
| experience_years | tinyint | Số năm kinh nghiệm |
| rating | decimal(3,2) | Rating trung bình (0.00 - 5.00), tự tính |
| is_active | boolean | Barber còn làm việc? |

#### `services` — Dịch vụ (cắt tóc, gội đầu...)

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| name | varchar(100) | Tên dịch vụ |
| description | text NULL | Mô tả |
| price | decimal(10,2) | Giá (VND) |
| duration_minutes | int | Thời gian phục vụ (phút) |
| image | varchar(255) NULL | Ảnh minh họa |
| is_active | boolean | Dịch vụ còn hoạt động? |

#### `working_schedules` — Lịch làm việc tuần

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| barber_id | FK → barbers | Cascade delete |
| day_of_week | tinyint | 0=Chủ nhật, 1=Thứ 2 ... 6=Thứ 7 |
| start_time | time | Giờ bắt đầu (VD: 08:00) |
| end_time | time | Giờ kết thúc (VD: 17:00) |
| is_day_off | boolean | Ngày nghỉ? |
| | UNIQUE | (barber_id, day_of_week) — mỗi barber 1 bản ghi/ngày |

#### `time_slots` — Khung giờ đặt lịch

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| barber_id | FK → barbers | Cascade delete |
| slot_date | date | Ngày cụ thể (VD: 2026-03-24) |
| start_time | time | Bắt đầu khung (VD: 10:00) |
| end_time | time | Kết thúc khung (VD: 10:30) |
| status | enum('available','booked','blocked') | Trạng thái slot |
| | UNIQUE | (barber_id, slot_date, start_time) |
| | INDEX | (barber_id, slot_date, status) — tìm slot trống nhanh |

#### `bookings` — Lịch hẹn

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| booking_code | varchar(20) UNIQUE | Mã lịch hẹn (VD: BK20260324001) |
| customer_id | FK → users | Cascade delete |
| barber_id | FK → barbers | Cascade delete |
| time_slot_id | FK → time_slots | **Restrict** delete (không xóa slot đang có booking) |
| booking_date | date | Ngày hẹn |
| start_time | time | Giờ bắt đầu |
| end_time | time | Giờ kết thúc (tính từ tổng duration dịch vụ) |
| total_price | decimal(10,2) | Tổng tiền |
| status | enum (FSM) | pending → confirmed → in_progress → completed / cancelled |
| note | text NULL | Ghi chú của khách |
| cancelled_at | timestamp NULL | Thời điểm hủy |
| cancel_reason | text NULL | Lý do hủy |

#### `booking_services` — Bảng trung gian (Pivot)

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| booking_id | FK → bookings | Cascade delete |
| service_id | FK → services | **Restrict** delete (không xóa service đang dùng) |
| price_snapshot | decimal(10,2) | **Giá tại thời điểm đặt** (giá gốc có thể đổi sau) |
| duration_snapshot | int | **Thời gian tại thời điểm đặt** |

> 🎯 `price_snapshot` rất quan trọng: nếu admin tăng giá cắt tóc từ 50k → 70k, booking cũ vẫn hiển thị đúng 50k.

#### `payments` — Thanh toán

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| booking_id | FK → bookings UNIQUE | **1-1**: mỗi booking chỉ 1 payment |
| amount | decimal(10,2) | Số tiền thanh toán |
| method | enum('cash','vnpay','momo') | Phương thức |
| status | enum('pending','paid','failed','refunded') | Trạng thái |
| transaction_id | varchar(255) NULL | Mã giao dịch VNPay/MoMo |
| paid_at | timestamp NULL | Thời điểm thanh toán thành công |

#### `reviews` — Đánh giá

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| booking_id | FK → bookings UNIQUE | **1-1**: mỗi booking chỉ 1 review |
| customer_id | FK → users | Cascade delete |
| barber_id | FK → barbers | Cascade delete |
| rating | tinyint | 1-5 sao |
| comment | text NULL | Nhận xét |

#### `notifications` — Thông báo

| Cột | Kiểu | Ghi chú |
|-----|------|---------|
| id | bigint PK | |
| user_id | FK → users | Cascade delete |
| type | varchar(50) | Loại thông báo (booking_confirmed, ...) |
| title | varchar(255) | Tiêu đề |
| message | text | Nội dung |
| is_read | boolean | Đã đọc chưa? |

### Tổng hợp quan hệ

```
users ──1:1──→ barbers         (Mỗi barber là 1 user)
users ──1:N──→ bookings        (Khách đặt nhiều lịch)
users ──1:N──→ notifications   (Nhận nhiều thông báo)

barbers ──1:N──→ working_schedules  (Lịch 7 ngày/tuần)
barbers ──1:N──→ time_slots         (Nhiều slot mỗi ngày)
barbers ──1:N──→ bookings           (Nhiều lịch hẹn)
barbers ──1:N──→ reviews            (Nhiều đánh giá)

bookings ──N:M──→ services     (Nhiều dịch vụ, qua booking_services)
bookings ──1:1──→ payments     (1 thanh toán)
bookings ──1:1──→ reviews      (1 đánh giá)
bookings ──1:1──→ time_slots   (Gắn 1 slot cụ thể)
```

### Cascade Delete — Xóa user thì sao?

```
Xóa User (role=barber)
    │
    ├── CASCADE → barbers (xóa barber)
    │       ├── CASCADE → working_schedules (xóa lịch làm việc)
    │       ├── CASCADE → time_slots (xóa slots)
    │       ├── CASCADE → bookings (xóa bookings)
    │       │       ├── CASCADE → booking_services (xóa pivot)
    │       │       ├── CASCADE → payments (xóa thanh toán)
    │       │       └── CASCADE → reviews (xóa đánh giá)  
    │       └── CASCADE → reviews (xóa đánh giá trực tiếp)
    │
    └── CASCADE → notifications (xóa thông báo)
```

> ⚠️ **Lưu ý**: `time_slots` dùng `restrictOnDelete` với `bookings` — nghĩa là **không thể xóa time_slot đang có booking**. Phải hủy booking trước.

---

## 15. Tổng kết sơ đồ kiến trúc

```
┌─────────────────────────────────────────────────────────────┐
│                        BROWSER                               │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                  │
│  │  Client   │  │  Barber  │  │   Admin  │                  │
│  └─────┬────┘  └────┬─────┘  └────┬─────┘                  │
└────────┼─────────────┼─────────────┼────────────────────────┘
         │             │             │
         ▼             ▼             ▼
┌─────────────────────────────────────────┐
│              ROUTES                      │
│  web.php  │  barber.php  │  admin.php   │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│            MIDDLEWARE                    │
│  SecurityHeaders → auth → role →        │
│  LogActivity → throttle                 │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│          FORM REQUESTS                   │
│  Validate dữ liệu đầu vào              │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│           CONTROLLERS                    │
│  Nhận request → Tạo DTO → Gọi Service  │
│  → Trả response/redirect                │
└─────────────────┬───────────────────────┘
                  │ DTO
                  ▼
┌─────────────────────────────────────────┐
│            SERVICES                      │
│  Business logic + DB transactions       │
│  + Phát Events + Log + Cache            │
│                                          │
│  ┌─────────┐ ┌─────────┐ ┌──────────┐  │
│  │ Booking │ │ Payment │ │  Report  │  │
│  │ Service │ │ Service │ │  Service │  │
│  └────┬────┘ └────┬────┘ └────┬─────┘  │
└───────┼───────────┼───────────┼─────────┘
        │           │           │
        ▼           ▼           ▼
┌─────────────────────────────────────────┐
│         MODELS + DATABASE                │
│  Eloquent ORM → MySQL                   │
│  Relationships, Casts (Enum), Fillable  │
└─────────────────────────────────────────┘

        ┌─────── Side Effects (từ Services) ────────┐
        │                                             │
        ▼                                             ▼
┌───────────────┐                           ┌────────────────┐
│    EVENTS     │ → BookingConfirmed        │    CACHE       │
│               │ → BookingCancelled        │  CacheService  │
│               │ → BookingCompleted        │  (Redis/File)  │
└───────┬───────┘                           └────────────────┘
        │
        ▼
┌───────────────┐
│   LISTENERS   │ → Tạo message
└───────┬───────┘
        │
        ▼
┌───────────────┐
│     JOBS      │ → Queue (async)
│  (ShouldQueue)│ → Notification::create()
└───────────────┘
```

---

## Phụ lục: Bảng tham chiếu nhanh

### A. Mối quan hệ Model

```
User ─1──N─▶ Booking (customer_id)
User ─1──1─▶ Barber (user_id)
Barber ─1──N─▶ Booking
Barber ─1──N─▶ TimeSlot
Barber ─1──N─▶ WorkingSchedule
Booking ─N──M─▶ Service (qua booking_services pivot)
Booking ─1──1─▶ Payment
Booking ─1──1─▶ Review
Booking ─N──1─▶ TimeSlot
User ─1──N─▶ Notification
```

### B. Bảng Route chính

| Method | URL | Controller | Quyền |
|--------|-----|-----------|-------|
| GET | `/booking/create` | Client\BookingController@create | Public |
| POST | `/booking` | Client\BookingController@store | Public (throttle) |
| GET | `/payment/{booking}` | Client\PaymentController@show | Public |
| POST | `/payment/{booking}` | Client\PaymentController@process | Public (throttle) |
| PATCH | `/booking/{booking}/cancel` | Client\BookingController@cancel | Auth + Policy |
| PATCH | `/barber/bookings/{booking}/confirm` | Barber\BookingController@confirm | Auth + role:barber + Policy |
| PATCH | `/barber/bookings/{booking}/complete` | Barber\BookingController@complete | Auth + role:barber + Policy |
| GET | `/admin/dashboard` | Admin\DashboardController@index | Auth + role:admin |
| RESOURCE | `/admin/services` | Admin\ServiceController | Auth + role:admin |
| RESOURCE | `/admin/barbers` | Admin\BarberController | Auth + role:admin |

### C. Công thức thêm tính năng mới

Khi cần thêm tính năng (VD: tạo coupon/mã giảm giá):

```
1. Tạo Model:        app/Models/Coupon.php
2. Tạo Migration:    database/migrations/create_coupons_table.php
3. Tạo Enum (nếu cần): app/Enums/CouponStatus.php
4. Tạo DTO:          app/DTOs/CreateCouponData.php
5. Tạo Service:      app/Services/CouponService.php
6. Tạo FormRequest:  app/Http/Requests/Admin/StoreCouponRequest.php
7. Tạo Controller:   app/Http/Controllers/Admin/CouponController.php
8. Thêm Route:       routes/admin.php
9. Tạo Views:        resources/views/admin/coupons/
10. Cập nhật Cache:  CacheService nếu cần cache
```

---

> 📌 **Handbook này nên được cập nhật khi thêm tính năng mới hoặc thay đổi kiến trúc.**
