# 📐 Quy ước code — BarberBook

> File này định nghĩa style và naming convention cho toàn dự án.  
> Agent và developer đều phải tuân theo để code nhất quán.

---

## 1. Đặt tên (Naming Convention)

### Models
- PascalCase, số ít: `Booking`, `TimeSlot`, `WorkingSchedule`
- Tên bảng: snake_case, số nhiều: `bookings`, `time_slots`, `working_schedules`

### Controllers
- PascalCase + `Controller`: `BookingController`, `TimeSlotController`
- Nhóm theo role trong thư mục: `App\Http\Controllers\Customer\BookingController`

### Services
- PascalCase + `Service`: `BookingService`, `TimeSlotService`
- Namespace: `App\Services\BookingService`

### Repositories
- Interface: `BookingRepositoryInterface`
- Implementation: `BookingRepository`
- Namespace: `App\Repositories\`

### Requests (Form Validation)
- `Store` + tên Model + `Request`: `StoreBookingRequest`
- `Update` + tên Model + `Request`: `UpdateBookingRequest`

### Events & Listeners
- Event: động từ quá khứ — `BookingCreated`, `BookingConfirmed`, `BookingCancelled`
- Listener: hành động rõ ràng — `SendBookingNotification`, `SendBookingConfirmationEmail`

### Blade Views
- Thư mục theo role: `customer/`, `barber/`, `admin/`
- Tên file: kebab-case: `booking-detail.blade.php`, `time-slot-picker.blade.php`

### Routes
- Tên route: `{role}.{resource}.{action}` — `customer.bookings.index`, `barber.bookings.confirm`
- URL: kebab-case: `/customer/bookings`, `/barber/time-slots`

### Variables & Methods
- camelCase: `$totalPrice`, `$bookingDate`, `getAvailableSlots()`
- Method trong Service: động từ + danh từ: `createBooking()`, `cancelBooking()`, `generateSlots()`

---

## 2. Cấu trúc Controller

Controller **chỉ được làm**:
- Validate request (qua FormRequest)
- Gọi Service
- Trả về view hoặc redirect

Controller **không được**:
- Chứa business logic
- Gọi thẳng Model (trừ query đơn giản `find`, `findOrFail`)
- Tính toán giá, thời gian, trạng thái

```php
// ✅ Đúng
class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->create($request->validated(), auth()->user());
        return redirect()->route('customer.bookings.show', $booking)
                         ->with('success', 'Đặt lịch thành công!');
    }
}

// ❌ Sai — logic trong controller
public function store(Request $request)
{
    $slot = TimeSlot::find($request->time_slot_id);
    $slot->status = 'booked';
    $slot->save();
    // ...
}
```

---

## 3. Cấu trúc Service

```php
class BookingService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepo,
        private TimeSlotService $timeSlotService,
    ) {}

    /**
     * Tạo booking mới.
     * Dùng transaction + lockForUpdate để tránh race condition.
     *
     * @throws SlotNotAvailableException
     */
    public function create(array $data, User $customer): Booking
    {
        return DB::transaction(function () use ($data, $customer) {
            // logic ở đây
        });
    }
}
```

- Mỗi method public phải có docblock ngắn
- Throw exception có tên rõ ràng thay vì return null khi lỗi
- Inject dependency qua constructor, không dùng `app()` hay `resolve()`

---

## 4. Exceptions tự định nghĩa

Đặt trong `app/Exceptions/`:

```
SlotNotAvailableException.php   — slot đã bị đặt
BookingCancelException.php      — không đủ điều kiện huỷ
UnauthorizedBookingException.php
```

```php
// Throw rõ ràng
throw new SlotNotAvailableException('Slot này vừa được đặt, vui lòng chọn lại.');

// Bắt trong Controller nếu cần show message cụ thể
} catch (SlotNotAvailableException $e) {
    return back()->withErrors(['slot' => $e->getMessage()]);
}
```

---

## 5. Blade Views

### Layout

Mỗi view bắt đầu bằng:
```blade
@extends('layouts.app')         {{-- customer --}}
@extends('layouts.barber')      {{-- barber --}}
@extends('layouts.admin')       {{-- admin --}}
```

### Section chuẩn

```blade
@section('title', 'Đặt lịch cắt tóc')

@section('content')
    {{-- nội dung trang --}}
@endsection
```

### Component nhỏ dùng lại

Đặt trong `resources/views/components/`:
```
alert.blade.php          — thông báo success/error
booking-card.blade.php   — card hiển thị booking
service-badge.blade.php  — badge tên dịch vụ
star-rating.blade.php    — hiển thị sao
```

Gọi bằng: `<x-booking-card :booking="$booking" />`

### Hiển thị lỗi validation

```blade
@error('field_name')
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
```

### Flash message

```blade
@if(session('success'))
    <x-alert type="success" :message="session('success')" />
@endif
```

---

## 6. Routes

### Cấu trúc file

```php
// routes/customer.php
Route::middleware(['auth', 'verified', 'role:customer,admin'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('bookings', BookingController::class)
            ->only(['index', 'show', 'store', 'create']);

        Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])
            ->name('bookings.cancel');
    });
```

### Quy tắc routes

- Dùng `Route::resource()` khi có đủ CRUD, dùng `->only()` để giới hạn
- Action ngoài CRUD (confirm, cancel, complete) thêm route riêng bằng `PATCH`
- Luôn đặt tên route (`->name()`), không hardcode URL trong blade

---

## 7. Validation — Ngôn ngữ tiếng Việt

Tạo file `lang/vi/validation.php` với các message phổ biến:

```php
'required' => ':Attribute không được để trống.',
'email'    => ':Attribute không đúng định dạng email.',
'min'      => [
    'numeric' => ':Attribute phải lớn hơn hoặc bằng :min.',
    'string'  => ':Attribute phải có ít nhất :min ký tự.',
],
'unique'   => ':Attribute đã tồn tại.',
```

Trong `FormRequest`:
```php
public function messages(): array
{
    return [
        'service_ids.required' => 'Vui lòng chọn ít nhất một dịch vụ.',
        'time_slot_id.required' => 'Vui lòng chọn khung giờ.',
    ];
}

public function attributes(): array
{
    return [
        'email'    => 'email',
        'phone'    => 'số điện thoại',
        'rating'   => 'đánh giá',
    ];
}
```

---

## 8. Database — Quy ước query

### Dùng Eager Loading, tránh N+1

```php
// ✅ Đúng
Booking::with(['customer', 'barber.user', 'services', 'timeSlot'])->get();

// ❌ Sai — N+1
foreach ($bookings as $booking) {
    echo $booking->customer->name; // query mỗi lần lặp
}
```

### Scope trong Model

Các query dùng nhiều lần → viết thành scope:

```php
// Trong Booking.php
public function scopePending($query)    { return $query->where('status', 'pending'); }
public function scopeCompleted($query)  { return $query->where('status', 'completed'); }
public function scopeToday($query)      { return $query->where('booking_date', today()); }

// Dùng:
Booking::pending()->today()->with('customer')->get();
```

### Accessor & Cast thường dùng

```php
// Trong Booking.php
protected $casts = [
    'booking_date' => 'date',
    'total_price'  => 'decimal:2',
];

// Hiển thị ngày tiếng Việt
public function getFormattedDateAttribute(): string
{
    return $this->booking_date->format('d/m/Y');
}
```

---

## 9. Bảo mật cần nhớ

- **CSRF**: Blade tự động có `@csrf` trong form, không bỏ sót
- **Authorization**: Luôn dùng `$this->authorize()` trong Controller hoặc `Policy` trước khi xử lý
- **Mass assignment**: Khai báo `$fillable` đầy đủ trong Model, không dùng `$guarded = []`
- **SQL Injection**: Luôn dùng Eloquent hoặc Query Builder với binding, không nối chuỗi SQL
- **File upload**: Validate `mimes`, `max` size; lưu bằng `Storage::disk('public')`, không lưu thẳng vào `public/`

---

## 10. Một số helper hay dùng trong project

```php
// Format tiền VNĐ
number_format($price, 0, ',', '.') . ' ₫'

// Generate mã booking
'BB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4))

// Kiểm tra còn có thể huỷ không (trước 2 tiếng)
$canCancel = now()->lt(
    Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time)
          ->subHours(2)
);
```