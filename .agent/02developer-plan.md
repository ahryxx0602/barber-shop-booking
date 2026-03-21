# 📋 Kế hoạch phát triển — BarberBook

> **Nguyên tắc làm việc:** Làm từng bước, xong bước này mới sang bước kia. Mỗi bước phải chạy được và test được trước khi tiếp tục.  
> Không cài thêm gì nếu chưa cần. Không viết UI trước khi có logic.  
> Stack: **Laravel 12** — dùng cú pháp mới (`bootstrap/app.php`, `routes/console.php`, `AppServiceProvider`).

---

## Tổng quan các giai đoạn

| Giai đoạn | Nội dung | Ước lượng |
|---|---|---|
| **0** | Cài đặt môi trường & khởi tạo dự án | 1 ngày |
| **1** | Database, Models, Seeders | 2–3 ngày |
| **2** | Auth & phân quyền 3 role | 1–2 ngày |
| **3** | Module thợ & dịch vụ (Admin) | 2–3 ngày |
| **4** | Quản lý lịch làm việc (Barber) | 2–3 ngày |
| **5** | Core booking — đặt lịch (Customer) | 3–4 ngày |
| **6** | Quản lý booking (Barber + Customer) | 2–3 ngày |
| **7** | Review & Notification | 2 ngày |
| **8** | Báo cáo doanh thu (Admin) | 1–2 ngày |
| **9** | Kiểm thử & hoàn thiện UI | 3–4 ngày |

---

---

## ⚙️ Giai đoạn 0 — Cài đặt môi trường

**Mục tiêu:** Có một project Laravel chạy được trên máy local.

### Bước 0.1 — Cài đặt công cụ

Kiểm tra đã có chưa, chưa có thì cài:
- PHP 8.2+
- Composer
- MySQL 8 (dùng XAMPP, Laragon, hoặc Docker)
- Node.js (để build Tailwind)
- Git

### Bước 0.2 — Tạo project Laravel

```bash
composer create-project laravel/laravel barbershop
cd barbershop
```

### Bước 0.3 — Cài Tailwind CSS

```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

Cấu hình `tailwind.config.js` để scan Blade files:
```js
content: ["./resources/**/*.blade.php", "./resources/**/*.js"]
```

### Bước 0.4 — Cài Laravel Breeze (auth cơ bản)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
```

### Bước 0.5 — Cấu hình `.env`

```env
DB_DATABASE=barbershop
DB_USERNAME=root
DB_PASSWORD=
```

Tạo database `barbershop` trong MySQL, sau đó:
```bash
php artisan migrate
```

**✅ Kiểm tra giai đoạn 0:** Mở `http://localhost:8000`, trang chủ Laravel hiển thị, đăng ký/đăng nhập được.

---

---

## 🗄️ Giai đoạn 1 — Database, Models, Seeders

**Mục tiêu:** Toàn bộ bảng được tạo đúng, có dữ liệu mẫu để làm việc.

> **Quy tắc:** Tạo migration → chạy migrate → tạo Model → tạo Factory → tạo Seeder → chạy seeder. Làm lần lượt từng bảng theo thứ tự quan hệ.

### Bước 1.1 — Thêm cột `role` vào bảng `users`

Breeze đã tạo bảng `users`, ta chỉ cần thêm cột:

```bash
php artisan make:migration add_role_avatar_phone_to_users_table
```

```php
$table->enum('role', ['customer', 'barber', 'admin'])->default('customer')->after('password');
$table->string('phone', 20)->nullable()->after('email');
$table->string('avatar', 255)->nullable()->after('phone');
```

```bash
php artisan migrate
```

### Bước 1.2 — Tạo migration cho `barbers`

```bash
php artisan make:migration create_barbers_table
```

```php
Schema::create('barbers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->text('bio')->nullable();
    $table->tinyInteger('experience_years')->default(0);
    $table->decimal('rating', 3, 2)->default(0.00);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### Bước 1.3 — Tạo migration cho `services`

```bash
php artisan make:migration create_services_table
```

```php
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->integer('duration_minutes');
    $table->string('image', 255)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### Bước 1.4 — Tạo migration cho `working_schedules`

```bash
php artisan make:migration create_working_schedules_table
```

```php
Schema::create('working_schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
    $table->tinyInteger('day_of_week'); // 0=CN, 1=T2 ... 6=T7
    $table->time('start_time');
    $table->time('end_time');
    $table->boolean('is_day_off')->default(false);
    $table->unique(['barber_id', 'day_of_week']);
});
```

### Bước 1.5 — Tạo migration cho `time_slots`

```bash
php artisan make:migration create_time_slots_table
```

```php
Schema::create('time_slots', function (Blueprint $table) {
    $table->id();
    $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
    $table->date('slot_date');
    $table->time('start_time');
    $table->time('end_time');
    $table->enum('status', ['available', 'booked', 'blocked'])->default('available');
    $table->timestamps();
    $table->unique(['barber_id', 'slot_date', 'start_time']);
    $table->index(['barber_id', 'slot_date', 'status']);
});
```

### Bước 1.6 — Tạo migration cho `bookings`

```bash
php artisan make:migration create_bookings_table
```

```php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->string('booking_code', 20)->unique();
    $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
    $table->foreignId('time_slot_id')->constrained()->restrictOnDelete();
    $table->date('booking_date');
    $table->time('start_time');
    $table->time('end_time');
    $table->decimal('total_price', 10, 2);
    $table->enum('status', ['pending','confirmed','in_progress','completed','cancelled'])
          ->default('pending');
    $table->text('note')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->text('cancel_reason')->nullable();
    $table->timestamps();
});
```

### Bước 1.7 — Tạo migration cho `booking_services`

```bash
php artisan make:migration create_booking_services_table
```

```php
Schema::create('booking_services', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
    $table->foreignId('service_id')->constrained()->restrictOnDelete();
    $table->decimal('price_snapshot', 10, 2);
    $table->integer('duration_snapshot');
});
```

### Bước 1.8 — Tạo migration cho `payments`, `reviews`, `notifications`

```bash
php artisan make:migration create_payments_table
php artisan make:migration create_reviews_table
php artisan make:migration create_notifications_table
```

*(nội dung theo schema ở file 01_project_overview.md)*

### Bước 1.9 — Chạy toàn bộ migration

```bash
php artisan migrate
```

Kiểm tra trong MySQL: đủ 11 bảng.

### Bước 1.10 — Tạo Models với quan hệ

Tạo lần lượt từng model, thêm `$fillable` và các relationship:

```bash
php artisan make:model Barber
php artisan make:model Service
php artisan make:model WorkingSchedule
php artisan make:model TimeSlot
php artisan make:model Booking
php artisan make:model BookingService
php artisan make:model Payment
php artisan make:model Review
php artisan make:model Notification
```

Ví dụ `Booking.php`:
```php
public function customer()   { return $this->belongsTo(User::class, 'customer_id'); }
public function barber()     { return $this->belongsTo(Barber::class); }
public function timeSlot()   { return $this->belongsTo(TimeSlot::class); }
public function services()   { return $this->belongsToMany(Service::class, 'booking_services')
                                            ->withPivot('price_snapshot', 'duration_snapshot'); }
public function payment()    { return $this->hasOne(Payment::class); }
public function review()     { return $this->hasOne(Review::class); }
```

### Bước 1.11 — Tạo Seeders

```bash
php artisan make:seeder UserSeeder
php artisan make:seeder BarberSeeder
php artisan make:seeder ServiceSeeder
```

Seed tối thiểu để test:
- 1 admin
- 3 barber users + 3 barber records
- 5 services
- Working schedules cho mỗi barber

```bash
php artisan db:seed
```

**✅ Kiểm tra giai đoạn 1:**
```bash
php artisan tinker
>>> App\Models\Barber::with('user')->get()
>>> App\Models\Service::all()
```
Dữ liệu hiển thị đúng.

---

---

## 🔐 Giai đoạn 2 — Auth & Phân quyền

**Mục tiêu:** 3 role đăng nhập được vào đúng dashboard của mình, không vào nhầm của người khác.

### Bước 2.1 — Tạo RoleMiddleware

```bash
php artisan make:middleware RoleMiddleware
```

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (!in_array(auth()->user()->role, $roles)) {
        abort(403);
    }
    return $next($request);
}
```

Đăng ký trong `bootstrap/app.php` (Laravel 12 dùng cú pháp mới):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['role' => RoleMiddleware::class]);
})
```

### Bước 2.2 — Tổ chức routes theo role

Tạo các file route riêng biệt, include trong `web.php`:

```php
// routes/web.php
require __DIR__.'/customer.php';
require __DIR__.'/barber.php';
require __DIR__.'/admin.php';
```

```php
// routes/customer.php
Route::middleware(['auth', 'verified', 'role:customer,admin'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    });
```

### Bước 2.3 — Redirect sau đăng nhập đúng theo role

Override trong `AuthenticatedSessionController` (Laravel 12 không còn `RouteServiceProvider`, redirect xử lý thẳng trong controller hoặc qua `AppServiceProvider`):

```php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php
// Sau khi gọi Auth::login(), redirect theo role:
$role = auth()->user()->role;
return redirect(match($role) {
    'admin'  => route('admin.dashboard'),
    'barber' => route('barber.dashboard'),
    default  => route('customer.dashboard'),
});
```

### Bước 2.4 — Tạo 3 layout Blade

```
resources/views/layouts/
├── app.blade.php      ← customer (nav: Trang chủ, Đặt lịch, Lịch sử)
├── barber.blade.php   ← barber (nav: Dashboard, Lịch làm, Booking)
└── admin.blade.php    ← admin (nav: Dashboard, Thợ, Dịch vụ, Người dùng)
```

### Bước 2.5 — Tạo dashboard cơ bản cho 3 role

Mỗi dashboard chỉ cần hiển thị "Xin chào [tên]" + thông tin cơ bản trước, nội dung sẽ bổ sung ở giai đoạn sau.

**✅ Kiểm tra giai đoạn 2:**
- Đăng nhập bằng tài khoản admin → vào `/admin/dashboard`
- Đăng nhập bằng tài khoản barber → vào `/barber/dashboard`
- Thử truy cập `/admin/dashboard` bằng tài khoản customer → nhận 403

---

---

## 🛠️ Giai đoạn 3 — Module Admin: Thợ & Dịch vụ

**Mục tiêu:** Admin CRUD được thợ và dịch vụ, có validation đầy đủ.

### Bước 3.1 — CRUD Services (Dịch vụ)

Tạo Controller:
```bash
php artisan make:controller Admin/ServiceController --resource
```

Implement đủ 7 methods: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.

Tạo FormRequest để validate:
```bash
php artisan make:request Admin/StoreServiceRequest
php artisan make:request Admin/UpdateServiceRequest
```

Rules tối thiểu:
```php
'name'             => ['required', 'string', 'max:100'],
'price'            => ['required', 'numeric', 'min:0'],
'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
```

Tạo views:
```
views/admin/services/
├── index.blade.php   ← bảng danh sách + nút thêm/sửa/xoá
├── create.blade.php
└── edit.blade.php
```

### Bước 3.2 — CRUD Barbers (Thợ cắt)

Thợ = User có role `barber` + record trong bảng `barbers`.

Khi admin tạo thợ mới:
1. Tạo bản ghi trong `users` với role = `barber`
2. Tạo bản ghi trong `barbers` liên kết

```bash
php artisan make:controller Admin/BarberController --resource
```

Bọc 2 lệnh insert trong `DB::transaction()` để đảm bảo toàn vẹn.

### Bước 3.3 — Upload ảnh

Dùng `Storage::disk('public')` để lưu ảnh thợ và dịch vụ:

```php
if ($request->hasFile('avatar')) {
    $path = $request->file('avatar')->store('avatars', 'public');
    $user->avatar = $path;
}
```

```bash
php artisan storage:link
```

**✅ Kiểm tra giai đoạn 3:** Admin thêm, sửa, xoá dịch vụ và thợ thành công. Ảnh hiển thị đúng.

---

---

## 📅 Giai đoạn 4 — Quản lý lịch làm việc (Barber)

**Mục tiêu:** Thợ tự cài được lịch làm trong tuần. Hệ thống tự generate time slots.

### Bước 4.1 — Giao diện cài working schedule

Barber vào trang "Lịch làm việc" → chọn các ngày trong tuần và giờ bắt đầu/kết thúc.

Giao diện: 7 hàng (CN–T7), mỗi hàng có toggle bật/tắt + time picker giờ.

```bash
php artisan make:controller Barber/ScheduleController
```

### Bước 4.2 — Tạo TimeSlotService

```bash
# Tạo thủ công file: app/Services/TimeSlotService.php
```

```php
class TimeSlotService
{
    /**
     * Generate slots cho 1 barber trong 1 ngày cụ thể
     * Dựa theo working_schedule của barber đó
     */
    public function generateForBarber(int $barberId, string $date): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $schedule = WorkingSchedule::where('barber_id', $barberId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_day_off', false)
            ->first();

        if (!$schedule) return; // ngày nghỉ, không generate

        $current = Carbon::parse($date . ' ' . $schedule->start_time);
        $end     = Carbon::parse($date . ' ' . $schedule->end_time);

        while ($current->copy()->addMinutes(30)->lte($end)) {
            TimeSlot::firstOrCreate([
                'barber_id'  => $barberId,
                'slot_date'  => $date,
                'start_time' => $current->format('H:i:s'),
            ], [
                'end_time' => $current->copy()->addMinutes(30)->format('H:i:s'),
                'status'   => 'available',
            ]);
            $current->addMinutes(30);
        }
    }
}
```

### Bước 4.3 — Artisan Command tự động generate slots

```bash
php artisan make:command GenerateTimeSlots
```

```php
// Chạy hằng ngày, generate slot cho 7 ngày tới
public function handle(): void
{
    $barbers = Barber::where('is_active', true)->get();
    foreach ($barbers as $barber) {
        for ($i = 0; $i <= 7; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $this->timeSlotService->generateForBarber($barber->id, $date);
        }
    }
    $this->info('Time slots generated successfully.');
}
```

Đăng ký trong `routes/console.php` (Laravel 12 — không còn `Console/Kernel.php`):
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('slots:generate')->dailyAt('00:30');
```

### Bước 4.4 — Generate slots ngay khi thợ lưu working schedule

Sau khi barber lưu schedule → gọi `TimeSlotService::generateForBarber()` cho 7 ngày tới.

**✅ Kiểm tra giai đoạn 4:**
```bash
php artisan slots:generate
```
Kiểm tra bảng `time_slots` trong MySQL có dữ liệu với status = `available`.

---

---

## 📆 Giai đoạn 5 — Core Booking (Customer đặt lịch)

**Mục tiêu:** Khách đặt được lịch đầy đủ — chọn thợ → dịch vụ → ngày giờ → xác nhận.

> Đây là giai đoạn phức tạp nhất. Làm từng bước, test từng bước.

### Bước 5.1 — Trang danh sách thợ

```
GET /barbers → hiển thị card thợ: ảnh, tên, rating, số năm KN
```

Có thể filter theo tên (dùng Alpine.js tìm kiếm realtime đơn giản).

### Bước 5.2 — Trang chi tiết thợ

```
GET /barbers/{barber} → ảnh, bio, danh sách dịch vụ có giá
```

Có nút "Đặt lịch với thợ này" → chuyển sang bước chọn dịch vụ.

### Bước 5.3 — Form đặt lịch — Bước 1: Chọn dịch vụ

```
GET /booking/create?barber_id=X
```

Hiển thị danh sách service dạng checkbox. Khi chọn → cộng dồn tổng tiền và tổng thời gian (dùng Alpine.js).

### Bước 5.4 — Form đặt lịch — Bước 2: Chọn ngày và slot giờ

Sau khi chọn dịch vụ → chọn ngày (date picker, chỉ cho chọn trong 7 ngày tới).

Khi chọn ngày → gọi route:
```
GET /booking/slots?barber_id=X&date=Y&duration=Z
```
Controller trả về danh sách slot `available` đủ thời gian liên tiếp → hiển thị dạng button giờ.

### Bước 5.5 — Tạo BookingService

```bash
# app/Services/BookingService.php
```

Logic tạo booking phải dùng `DB::transaction()` + `lockForUpdate()` để tránh 2 người đặt cùng lúc:

```php
public function create(array $data, User $customer): Booking
{
    return DB::transaction(function () use ($data, $customer) {
        // Lock slot, kiểm tra còn available không
        $slot = TimeSlot::lockForUpdate()->findOrFail($data['time_slot_id']);
        if ($slot->status !== 'available') {
            throw new SlotNotAvailableException('Slot này vừa được đặt, vui lòng chọn lại.');
        }

        // Tính tổng tiền và end_time
        $services   = Service::whereIn('id', $data['service_ids'])->get();
        $totalPrice = $services->sum('price');
        $totalDuration = $services->sum('duration_minutes');
        $endTime    = Carbon::parse($slot->start_time)->addMinutes($totalDuration)->format('H:i:s');

        // Tạo booking
        $booking = Booking::create([
            'booking_code' => $this->generateCode(),
            'customer_id'  => $customer->id,
            'barber_id'    => $data['barber_id'],
            'time_slot_id' => $slot->id,
            'booking_date' => $slot->slot_date,
            'start_time'   => $slot->start_time,
            'end_time'     => $endTime,
            'total_price'  => $totalPrice,
            'note'         => $data['note'] ?? null,
            'status'       => 'pending',
        ]);

        // Snapshot dịch vụ
        foreach ($services as $service) {
            $booking->services()->attach($service->id, [
                'price_snapshot'    => $service->price,
                'duration_snapshot' => $service->duration_minutes,
            ]);
        }

        // Lock slot
        $slot->update(['status' => 'booked']);

        // Fire event
        event(new BookingCreated($booking));

        return $booking;
    });
}
```

### Bước 5.6 — Trang xác nhận booking

Sau khi submit form → hiển thị trang xác nhận với mã booking, thông tin chi tiết.

### Bước 5.7 — Trang lịch sử booking

```
GET /customer/bookings → danh sách booking của mình, phân trang
GET /customer/bookings/{booking} → chi tiết
```

**✅ Kiểm tra giai đoạn 5:** Đặt lịch end-to-end thành công. Kiểm tra trong DB slot đã chuyển sang `booked`.

---

---

## 📋 Giai đoạn 6 — Quản lý Booking

**Mục tiêu:** Thợ quản lý booking của mình. Khách huỷ được lịch.

### Bước 6.1 — Dashboard Barber: danh sách booking

Hiển thị booking theo ngày (mặc định hôm nay), có thể chọn ngày khác.

```
GET /barber/bookings?date=2025-01-15
```

### Bước 6.2 — Thợ xác nhận / từ chối booking

```
PATCH /barber/bookings/{booking}/confirm
PATCH /barber/bookings/{booking}/reject
```

Khi xác nhận → status = `confirmed`, fire event `BookingConfirmed`.  
Khi từ chối → status = `cancelled`, **mở lại time_slot** về `available`.

### Bước 6.3 — Thợ đánh dấu "Đang thực hiện" và "Hoàn thành"

```
PATCH /barber/bookings/{booking}/start    → in_progress
PATCH /barber/bookings/{booking}/complete → completed
```

Khi `completed` → mở khóa cho phép customer viết review.

### Bước 6.4 — Customer huỷ booking

```
PATCH /customer/bookings/{booking}/cancel
```

Chỉ cho huỷ khi status là `pending` hoặc `confirmed` và **còn ít nhất 2 tiếng** trước giờ hẹn. Khi huỷ → mở lại time_slot về `available`.

### Bước 6.5 — Tạo BookingPolicy

```bash
php artisan make:policy BookingPolicy --model=Booking
```

Ví dụ:
```php
public function cancel(User $user, Booking $booking): bool
{
    return $user->id === $booking->customer_id
        && in_array($booking->status, ['pending', 'confirmed'])
        && now()->lt(Carbon::parse($booking->booking_date . ' ' . $booking->start_time)->subHours(2));
}
```

**✅ Kiểm tra giai đoạn 6:** Toàn bộ vòng đời booking hoạt động: pending → confirmed → in_progress → completed.

---

---

## ⭐ Giai đoạn 7 — Review & Notification

**Mục tiêu:** Khách viết được review sau khi hoàn thành. Mọi người nhận được thông báo đúng lúc.

### Bước 7.1 — Viết Review

Hiển thị nút "Đánh giá" trong lịch sử booking khi status = `completed` và chưa có review.

```
POST /customer/reviews
```

Validate: `rating` bắt buộc (1-5), `comment` không bắt buộc. Mỗi booking chỉ review 1 lần.

Sau khi có review → cập nhật lại `barbers.rating` (tính trung bình).

### Bước 7.2 — Hiển thị Review trên trang thợ

Trang `/barbers/{barber}` hiển thị danh sách review mới nhất, điểm trung bình dạng sao.

### Bước 7.3 — Events & Listeners

Tạo Events:
```bash
php artisan make:event BookingCreated
php artisan make:event BookingConfirmed
php artisan make:event BookingCancelled
```

Tạo Listeners:
```bash
php artisan make:listener SendBookingNotification
```

Đăng ký trong `AppServiceProvider` (Laravel 12 — không còn `EventServiceProvider` riêng):
```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Event;

public function boot(): void
{
    Event::listen(BookingCreated::class, SendBookingNotification::class);
    Event::listen(BookingConfirmed::class, SendBookingNotification::class);
}
```

### Bước 7.4 — In-app Notification

Listener ghi vào bảng `notifications`:

```php
public function handle(BookingCreated $event): void
{
    $booking = $event->booking;

    // Thông báo cho barber
    Notification::create([
        'user_id' => $booking->barber->user_id,
        'type'    => 'booking_created',
        'title'   => 'Có lịch hẹn mới',
        'message' => "Khách {$booking->customer->name} đặt lịch ngày {$booking->booking_date}",
    ]);
}
```

Hiển thị số thông báo chưa đọc trên nav bar.

### Bước 7.5 — (Tuỳ chọn) Gửi Email xác nhận

```bash
php artisan make:mail BookingConfirmedMail --markdown=emails.booking-confirmed
```

Cấu hình Mailtrap trong `.env` để test email khi dev.

**✅ Kiểm tra giai đoạn 7:** Sau khi đặt lịch, thợ thấy thông báo mới. Sau khi hoàn thành, khách viết được review.

---

---

## 📊 Giai đoạn 8 — Báo cáo doanh thu (Admin)

**Mục tiêu:** Admin xem được doanh thu theo ngày/tháng, top thợ, top dịch vụ.

### Bước 8.1 — Trang báo cáo tổng quan

```
GET /admin/reports
```

Thống kê:
- Tổng booking trong tháng
- Doanh thu dự kiến (các booking confirmed + completed)
- So sánh với tháng trước

### Bước 8.2 — Biểu đồ doanh thu theo ngày

Dùng **Chart.js** (CDN) để vẽ biểu đồ đường, dữ liệu lấy từ Controller:

```php
$data = Booking::where('status', 'completed')
    ->whereBetween('booking_date', [$start, $end])
    ->selectRaw('booking_date, SUM(total_price) as revenue')
    ->groupBy('booking_date')
    ->orderBy('booking_date')
    ->get();
```

### Bước 8.3 — Bảng top thợ & top dịch vụ

Top thợ theo số booking completed, top dịch vụ theo số lần được đặt.

**✅ Kiểm tra giai đoạn 8:** Số liệu khớp với dữ liệu trong DB.

---

---

## 🧪 Giai đoạn 9 — Kiểm thử & Hoàn thiện

**Mục tiêu:** Hệ thống không có lỗi cơ bản, UI nhất quán, đủ điều kiện bảo vệ.

### Bước 9.1 — Kiểm tra thủ công toàn bộ luồng

Checklist:
- [ ] Đăng ký → đăng nhập → đặt lịch → thợ xác nhận → hoàn thành → review
- [ ] Huỷ lịch → slot tự động mở lại
- [ ] Admin tạo thợ → thợ đăng nhập → cài lịch → slots được generate
- [ ] Thử vào URL của role khác → nhận 403
- [ ] Thử đặt slot vừa được người khác đặt → nhận thông báo lỗi

### Bước 9.2 — Xử lý edge cases

- Thợ không có working schedule → trang đặt lịch hiển thị "Thợ chưa cài lịch làm"
- Dịch vụ bị inactive → không hiển thị trong form đặt lịch
- Booking quá hạn pending → Artisan command tự huỷ

### Bước 9.3 — Responsive UI

Kiểm tra trên mobile (Chrome DevTools). Ưu tiên fix các trang: trang chọn slot giờ, trang lịch sử booking.

### Bước 9.4 — Validation & Error Handling

- Mọi form đều có validation message tiếng Việt
- Tạo file `lang/vi/validation.php`
- Trang 403, 404, 500 có giao diện đẹp riêng

### Bước 9.5 — Seed dữ liệu demo đầy đủ

Chuẩn bị dữ liệu demo cho buổi bảo vệ:
- 3 thợ, mỗi thợ có lịch làm + vài chục booking ở các trạng thái khác nhau
- Đủ review để hiển thị rating
- Dữ liệu doanh thu nhiều ngày để biểu đồ có hình

### Bước 9.6 — Viết README

```markdown
# BarberBook

## Yêu cầu
- PHP 8.2+, Composer, MySQL 8, Node.js

## Cài đặt
git clone ...
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

**✅ Kiểm tra giai đoạn 9:** Demo end-to-end thành công, không có lỗi console, không có lỗi PHP.

---

---

## 📌 Quy tắc trong suốt quá trình làm

1. **Commit thường xuyên** — mỗi khi xong 1 bước nhỏ là commit, đừng để cuối giai đoạn mới commit 1 cục.
2. **Đặt tên commit rõ ràng** — `feat: add booking service with slot locking`, không phải `update`.
3. **Không viết UI trước khi có logic** — phải chạy được trong tinker/postman trước, mới làm giao diện.
4. **Mỗi bước phải test được** — không làm bước tiếp nếu bước hiện tại chưa chạy đúng.
5. **Không cài package nếu không cần** — tránh bloat, đồ án cần hiểu được tất cả những gì mình dùng.
6. **Ghi chú lại những quyết định quan trọng** — ví dụ tại sao dùng `lockForUpdate()`, tại sao snapshot giá.

---

*Xem file `01_project_overview.md` để biết chi tiết về kiến trúc và database schema.*