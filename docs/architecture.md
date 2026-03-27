# 🏗 Kiến trúc hệ thống — Classic Cut Barbershop

> Tài liệu này giải thích **TẠI SAO** mỗi quyết định kiến trúc được đưa ra,
> **VẤN ĐỀ GÌ** sẽ xảy ra nếu không làm, và cách source code thực tế đang giải quyết nó.

---

## Tổng quan kiến trúc

Classic Cut sử dụng mô hình **MVC + Service Layer + DTO**, phân tầng rõ ràng:

```
Request → Middleware → FormRequest → Controller → DTO → Service → Model/DB
                                                          ↘ Event → Listener → Job
                                                          ↘ Cache
```

---

## Tư duy Thiết kế — 8 Quyết định Kiến trúc Cốt lõi

### 1. Service Layer Pattern

**🎯 WHAT:** Tầng kẹp giữa Controller và Database, chứa 100% business logic.

**❓ WHY — Nỗi đau Fat Controller:**
Nếu nhồi tính toán giá tiền, kiểm tra slot, áp voucher, gửi notification vào Controller, nó sẽ phình to 1000 dòng. Ngày mai sếp bảo *"Tạo Console Command tự động hủy booking quá hạn"* — bạn không thể gọi Controller từ Console, phải copy-paste 1000 dòng code → **Rác Code** kinh hoàng.

**🛠 HOW:**
```php
// ❌ Fat Controller (1000 dòng):        ✅ Thin Controller + Service:
Controller::store() {                    Controller::store() {
    validate(); lock(); check();             $dto = DTO::fromRequest($req);
    create(); attach(); update();            $booking = $service->create($dto);
    log(); notify(); redirect();             return redirect();
}                                        }

// Console Command cũng gọi được:
$bookingService->create($dto);  // ← Tái sử dụng logic!
```

**Files:** `app/Services/Booking/BookingService.php`, `app/Services/Shop/OrderService.php`, + 14 services khác.

---

### 2. DTO (Data Transfer Object)

**🎯 WHAT:** Class "tí hon" chỉ chứa dữ liệu, readonly, type-safe. Chở dữ liệu từ Controller sang Service.

**❓ WHY — Nỗi đau Array vô danh:**
Truyền `$request->all()` vào Service = truyền mảng vô danh. Service không biết bên trong có key `barber_id` hay `barberId`. Frontend đổi tên field → Backend sập ngầm lúc runtime.

**🛠 HOW:**
```php
// File: app/DTOs/CreateBookingData.php
readonly class CreateBookingData
{
    public function __construct(
        public int $barber_id,       // ← IDE gợi ý, type-safe
        public int $time_slot_id,
        public array $service_ids,
        public ?string $note = null,
    ) {}

    public static function fromRequest(StoreBookingRequest $request): self
    {
        $data = $request->validated();  // ← Chỉ lấy dữ liệu đã validated
        return new self(
            barber_id: $data['barber_id'],
            time_slot_id: $data['time_slot_id'],
            service_ids: $data['service_ids'],
            note: $data['note'] ?? null,
        );
    }
}
```

**Files:** `app/DTOs/CreateBookingData.php`, `CreateBarberData.php`, `CreateOrderData.php`, + 4 DTOs khác.

---

### 3. FSM (Finite State Machine)

**🎯 WHAT:** Quy tắc kiểm soát chuyển trạng thái hợp lệ. `pending → confirmed → in_progress → completed`, không được nhảy loạn.

**❓ WHY — Nỗi đau trạng thái hỗn loạn:**
Không có FSM, admin bấm "Hoàn thành" cho booking đang `Cancelled`. Barber bấm "Bắt đầu cắt" khi khách chưa `Confirmed`. Hacker đổi payload status thành `completed` bypass quy trình → **tiền đã thu, tóc chưa cắt**.

**🛠 HOW:**
```php
// File: app/Enums/BookingStatus.php
public function canTransitionTo(self $target): bool
{
    return match ($this) {
        self::Pending    => in_array($target, [self::Confirmed, self::Cancelled]),
        self::Confirmed  => in_array($target, [self::InProgress, self::Cancelled]),
        self::InProgress => $target === self::Completed,
        self::Completed, self::Cancelled => false,  // Trạng thái cuối — kết thúc
    };
}
```

**Sơ đồ:**
```
pending ──→ confirmed ──→ in_progress ──→ completed
   │             │
   └──→ cancelled ←──┘
```

**Files:** `app/Enums/BookingStatus.php`, `app/Enums/OrderStatus.php`.

---

### 4. Pessimistic Locking (`lockForUpdate`)

**🎯 WHAT:** Khoá bi quan — khi Request A đọc record, DB lập tức khoá hàng đó. Request B phải đứng chờ.

**❓ WHY — Nỗi đau Race Condition:**
Slot 10:00 chỉ còn 1 chỗ. 2 khách bấm "Đặt lịch" cùng lúc 12:00:00.001. Cả hai đều thấy `status=available` → cả hai đặt thành công → **2 booking cho 1 slot!** Tương tự: Pomade chỉ còn 1 hộp, 2 người mua → `stock` bị âm.

**🛠 HOW:**
```php
// File: app/Services/BookingService.php
return DB::transaction(function () use ($data) {
    $slot = TimeSlot::lockForUpdate()->findOrFail($data->time_slot_id);
    //               ^^^^^^^^^^^^^^ SQL: SELECT ... FOR UPDATE
    //               → Hàng bị LOCK cho đến khi transaction COMMIT

    if ($slot->status !== TimeSlotStatus::Available) {
        throw new SlotNotAvailableException('Slot đã được đặt');
    }

    // ... tạo booking ...
    $slot->update(['status' => TimeSlotStatus::Booked]);
});
```

**Dùng tại:** `BookingService::create()`, `ProductService::decreaseStock()`, `OrderService::create()`, `CouponService::markUsed()`.

---

### 5. Event / Listener / Job

**🎯 WHAT:** Hệ thống side effects tách rời. Service phát Event → Listener bắt → dispatch Job chạy ngầm.

**❓ WHY — Nỗi đau Coupling & Tốc độ:**
Nếu nhét gửi Email (3 giây), SMS (2 giây), tạo Notification vào `BookingService::confirm()`:
- Service dính chặt với Mailer/SMS → lỗi Mailer sập làm vỡ tung Booking
- User bấm "Xác nhận" chờ 5 giây mới thấy "Thành công" → trải nghiệm thảm hoạ

**🛠 HOW:**
```php
// Service chỉ 1 dòng — phát event mất 0.01 giây
public function confirm(Booking $booking): Booking {
    $booking->update(['status' => BookingStatus::Confirmed]);
    event(new BookingConfirmed($booking));  // ← 0.01s, không block
}

// Listener bắt event → dispatch Job vào queue
// File: app/Listeners/SendBookingConfirmedNotification.php
public function handle(BookingConfirmed $event): void {
    SendBookingNotificationJob::dispatch($customerId, $message);  // Async
}
```

**Events:** `BookingConfirmed`, `BookingCancelled`, `BookingCompleted`.
**Listeners:** 6 listeners xử lý Notification, Commission, Loyalty Points, Waitlist.

---

### 6. FormRequest Validation

**🎯 WHAT:** Lớp chuyên trách validate dữ liệu đầu vào, chạy TỰ ĐỘNG trước Controller.

**❓ WHY — Nỗi đau Controller làm bảo vệ:**
Validate trong Controller = 50 dòng `if/else` chỉ để báo lỗi. 50 Controller × 50 dòng = bãi rác.
Truyền `$request->all()` → hacker F12 nhét `"role": "admin"` → **Mass Assignment Attack**.

**🛠 HOW:**
```php
// File: app/Http/Requests/Client/StoreBookingRequest.php
public function rules(): array {
    return [
        'service_ids'   => 'required|array|min:1',
        'service_ids.*' => 'exists:services,id',
        'barber_id'     => 'required|exists:barbers,id',
        'time_slot_id'  => 'required|exists:time_slots,id',
    ];
}
// → Fail? Laravel tự redirect back + $errors. Controller KHÔNG BAO GIỜ nhận data sai.
```

---

### 7. Policy (Row-Level Authorization)

**🎯 WHAT:** Phân quyền trên TỪNG DÒNG dữ liệu: "User này có được phép làm hành động này với Record này không?"

**❓ WHY — Nỗi đau IDOR:**
Khách A gõ URL `/booking/15/cancel`. Middleware chỉ check "A đã login chưa?" → Có. Nhưng booking #15 là của Khách B! Không có Policy → A huỷ booking của B → **sập hệ thống**.

**🛠 HOW:**
```php
// File: app/Policies/BookingPolicy.php
public function cancel(User $user, Booking $booking): bool {
    if ($user->id !== $booking->customer_id) return false;  // Chỉ chủ sở hữu
    if (!in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) return false;
    $appointmentTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
    return now()->diffInMinutes($appointmentTime, false) >= 120;  // Trước 2 tiếng
}

// Controller — 1 dòng duy nhất:
$this->authorize('cancel', $booking);  // false → abort(403)
```

---

### 8. Middleware Pipeline (Bộ lọc Request toàn cục)

**🎯 WHAT:** Middleware là các "trạm kiểm soát" nằm giữa Request và Controller. Mỗi Request phải đi qua **tất cả** middleware trước khi chạm vào logic nghiệp vụ. Hệ thống Classic Cut có 3 middleware tự viết, mỗi cái giải quyết một vấn đề riêng biệt.

**❓ WHY — Nỗi đau "copy-paste kiểm tra" ở 50 Controller:**
Nếu không có Middleware, bạn phải nhét `if (auth()->user()->role !== 'admin') abort(403)` vào **từng controller** admin. 10 controller × 20 action = 200 lần copy-paste. Quên 1 chỗ → Barber truy cập trang xoá sản phẩm. Tệ hơn: không ai log ai đã làm gì → khi xảy ra sự cố, không có dấu vết để điều tra.

**🛠 HOW — 3 Middleware của hệ thống:**

#### a) RoleMiddleware — Bảo vệ cổng theo vai trò

Barber gõ URL `/admin/revenue` xem doanh thu? Middleware chặn ngay từ vòng gửi xe, Controller không bao giờ biết request đó tồn tại.

```php
// File: app/Http/Middleware/RoleMiddleware.php
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_filter(
            array_map(fn (string $role) => UserRole::tryFrom($role), $roles)
        );

        if (empty($allowedRoles) || !auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
            abort(403);
        }

        return $next($request);
    }
}

// Đăng ký alias trong bootstrap/app.php:
$middleware->alias(['role' => RoleMiddleware::class]);

// Sử dụng tại routes/admin.php — 1 dòng bảo vệ toàn bộ khu admin:
Route::middleware(['auth', 'role:admin'])->group(function () { /* 10+ controllers */ });

// routes/barber.php — Barber VÀ Admin đều vào được:
Route::middleware(['auth', 'role:barber,admin'])->group(function () { ... });
```

#### b) SecurityHeaders — Đóng chặt cổng trình duyệt

Hacker chèn `<script>` vào mục Đánh giá. Khi Admin xem, script đánh cắp Session Cookie. CSP (Content Security Policy) chỉ cho phép Javascript từ domain đã duyệt → script lạ bị chặn.

```php
// File: app/Http/Middleware/SecurityHeaders.php
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    $response->headers->set('X-Content-Type-Options', 'nosniff');     // Chống MIME sniffing
    $response->headers->set('X-Frame-Options', 'DENY');               // Chống Clickjacking
    $response->headers->set('X-XSS-Protection', '1; mode=block');    // XSS filter trình duyệt cũ
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

    // CSP — dynamic theo environment (local cho phép Vite HMR)
    $scriptSrc = "'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net ...";
    $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src {$scriptSrc}; ...");

    return $response;
}
```

#### c) LogActivity — Nhật ký hành động (Audit Trail)

Sáng thứ Hai, 50 sản phẩm biến mất khỏi kho. Ai xoá? Lúc nào? Từ IP nào? Không có log → **không thể điều tra**. LogActivity ghi lại mọi request thay đổi dữ liệu (POST/PUT/PATCH/DELETE), bỏ qua GET để giảm noise.

```php
// File: app/Http/Middleware/LogActivity.php
public function handle(Request $request, Closure $next): Response
{
    $start = microtime(true);
    $response = $next($request);

    // Chỉ log request thay đổi dữ liệu — GET không ghi
    if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        $duration = round((microtime(true) - $start) * 1000, 2);

        Log::info('Activity', [
            'user_id'  => $request->user()?->id,
            'role'     => $request->user()?->role?->value ?? 'guest',
            'method'   => $request->method(),
            'url'      => $request->fullUrl(),
            'ip'       => $request->ip(),
            'status'   => $response->getStatusCode(),
            'duration' => "{$duration}ms",
            // Loại bỏ fields nhạy cảm (password, token)
            'payload'  => $request->except(['password', 'password_confirmation', '_token']),
        ]);
    }

    return $response;
}
```

**Đăng ký toàn cục** trong `bootstrap/app.php`:
```php
$middleware->web(append: [
    LogActivity::class,       // Ghi log mọi POST/PUT/PATCH/DELETE
    SecurityHeaders::class,   // Headers bảo mật cho mọi response
]);
```

**Pipeline minh hoạ — Request đi qua 3 tầng:**
```
Request ──→ SecurityHeaders ──→ LogActivity ──→ RoleMiddleware ──→ Controller
              (gắn headers)     (ghi log)       (check role)       (xử lý)
```

---

## Cấu trúc thư mục chính

```
app/
├── DTOs/                    # 7 Data Transfer Objects (type-safe)
├── Enums/                   # 9 PHP 8.1 Backed Enums (BookingStatus, OrderStatus, ...)
├── Events/                  # 3 Booking lifecycle events
├── Exceptions/              # SlotNotAvailableException
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # 10+ controllers (Dashboard, Barber, Service, Product, Order, ...)
│   │   ├── Barber/          # 3 controllers (Dashboard, Booking, Schedule)
│   │   └── Client/          # 8+ controllers (Booking, Payment, Shop, Cart, Checkout, ...)
│   ├── Middleware/           # RoleMiddleware, SecurityHeaders, LogActivity
│   └── Requests/            # FormRequest cho Admin, Barber, Client
├── Jobs/                    # SendBookingNotificationJob (async)
├── Listeners/               # 6 listeners (Notification, Commission, Loyalty, Waitlist)
├── Models/                  # 21 Eloquent models
├── Policies/                # BookingPolicy (confirm, reject, start, complete, cancel)
├── Traits/                  # PaymentGatewayTrait (shared VNPay/MoMo logic)
└── Services/                # Business services (Domain-driven)

routes/
├── web.php                  # Client + Guest + E-commerce
├── admin.php                # Admin (prefix /admin, role:admin)
├── barber.php               # Barber (prefix /barber, role:barber,admin)
└── console.php              # Cron schedules
```

---

## Luồng nghiệp vụ chính

### Đặt lịch (Booking Flow)

```
1. Client chọn dịch vụ + thợ + slot giờ (Alpine.js wizard 4 bước)
2. StoreBookingRequest validate input (FormRequest)
3. CreateBookingData::fromRequest() → DTO type-safe
4. BookingService::create(DTO, user)
   ├── DB::transaction + lockForUpdate() (chống double-booking)
   ├── Kiểm tra slot available
   ├── Tính tổng giá + duration từ services
   ├── Tạo Booking + attach services (pivot với price/duration snapshot)
   ├── Cập nhật TimeSlot → booked
   └── Dispatch SendBookingNotificationJob (async)
5. Redirect → Payment page
6. Client chọn phương thức (Cash / VNPay / MoMo)
7. PaymentService tạo URL → redirect sang gateway
8. Gateway callback → verifyCallback() (signature + idempotency)
9. Hiển thị confirmation page
```

### Đặt hàng (Order Flow — E-commerce)

```
1. Khách duyệt shop → thêm sản phẩm vào giỏ (CartService, session-based)
2. Checkout → nhập địa chỉ giao hàng
3. OrderService::create(DTO)
   ├── DB::transaction + lockForUpdate() (chống oversell)
   ├── Trừ kho sản phẩm
   ├── Tính tiền: subtotal + tax 10% + shipping (Haversine)
   ├── Tạo Order + OrderItems (price_snapshot)
   └── Áp mã giảm giá nếu có
4. Thanh toán: COD / VNPay / MoMo
```

---

## Bảo mật — 9 cơ chế phòng thủ

| Cơ chế | Mục đích | File/Vị trí |
|--------|---------|-------------|
| **Pessimistic Locking** | Chống double-booking, oversell | `BookingService`, `ProductService`, `OrderService` |
| **FSM Guard** | Chặn chuyển trạng thái bất hợp lệ | `BookingStatus::canTransitionTo()`, `OrderStatus` |
| **HMAC Signature** | Chống giả mạo callback thanh toán | `PaymentService` (SHA512 VNPay, SHA256 MoMo) |
| **Idempotency** | Chống xử lý callback trùng lặp | `verifyVNPayCallback()` — check PaymentStatus |
| **VNPay IPN** | Server-to-server backup, exclude CSRF | `routes/web.php` — `withoutMiddleware` |
| **Rate Limiting** | Chống spam bot | `throttle:5,1` trên booking/payment POST |
| **Security Headers** | XSS, Clickjacking, MIME protection | `SecurityHeaders` middleware |
| **Role Middleware** | Phân quyền theo UserRole enum | `RoleMiddleware` — admin, barber, customer |
| **BookingPolicy** | Row-level authorization | `BookingPolicy` — confirm, reject, start, complete, cancel |

> 👉 **Chi tiết 6 Tầng Phòng thủ:** xem [SECURITY.md](SECURITY.md)
> 👉 **Handbook đào tạo đầy đủ:** xem [handbook.md](handbook.md)
