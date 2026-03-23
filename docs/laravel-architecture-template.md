# 🏗️ Laravel Project Architecture Template

> Template chuẩn cho mọi dự án Laravel mới.  
> Đúc kết từ thực tế dự án, áp dụng ngay không cần chỉnh sửa.

---

## Mục lục

1. [Kiến trúc tổng quan](#1-kiến-trúc-tổng-quan)
2. [Cấu trúc thư mục](#2-cấu-trúc-thư-mục)
3. [Luồng request chuẩn](#3-luồng-request-chuẩn)
4. [Enum — Giá trị cố định](#4-enum--giá-trị-cố-định)
5. [DTO — Data Transfer Object](#5-dto--data-transfer-object)
6. [Form Request — Validation](#6-form-request--validation)
7. [Service Layer — Business Logic](#7-service-layer--business-logic)
8. [Model — Eloquent](#8-model--eloquent)
9. [Event / Listener / Job](#9-event--listener--job)
10. [Policy — Phân quyền record](#10-policy--phân-quyền-record)
11. [Middleware — Bộ lọc request](#11-middleware--bộ-lọc-request)
12. [Cache — Tối ưu hiệu suất](#12-cache--tối-ưu-hiệu-suất)
13. [Exception — Xử lý lỗi](#13-exception--xử-lý-lỗi)
14. [Console Command — Tác vụ nền](#14-console-command--tác-vụ-nền)
15. [Checklist khi thêm tính năng mới](#15-checklist-khi-thêm-tính-năng-mới)
16. [Quy tắc & Best Practices](#16-quy-tắc--best-practices)

---

## 1. Kiến trúc tổng quan

Sử dụng mô hình **MVC + Service Layer + DTO**:

```
Request → Middleware → FormRequest → Controller → DTO → Service → Model → DB
                                                         ↘ Event → Listener → Job
                                                         ↘ Cache
```

### Nguyên tắc phân chia trách nhiệm

| Lớp | Trách nhiệm | KHÔNG được làm |
|-----|-------------|----------------|
| **Controller** | Nhận request, tạo DTO, gọi Service, trả response | Chứa business logic, query DB trực tiếp |
| **FormRequest** | Validate dữ liệu đầu vào | Chứa logic nghiệp vụ |
| **DTO** | Đóng gói dữ liệu, type-safe | Chứa logic xử lý |
| **Service** | Business logic, DB transactions, phát Events | Trả response HTTP, nhận Request object |
| **Model** | Relationships, casts, scopes | Chứa business logic phức tạp |
| **Event/Listener** | Side effects (notification, email, log) | Chứa core business logic |
| **Policy** | Phân quyền trên từng record | Chứa logic nghiệp vụ |

---

## 2. Cấu trúc thư mục

```
app/
├── Console/Commands/       # Artisan commands (cron, batch jobs)
├── DTOs/                   # Data Transfer Objects
├── Enums/                  # PHP 8.1 Backed Enums
├── Events/                 # Domain events
├── Exceptions/             # Custom exceptions
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Controllers theo role
│   │   ├── Client/
│   │   └── Api/            # (nếu có API)
│   ├── Middleware/          # Custom middleware
│   └── Requests/           # Form Request validation
│       ├── Admin/
│       └── Client/
├── Jobs/                   # Queue jobs (async tasks)
├── Listeners/              # Event listeners
├── Models/                 # Eloquent models
├── Policies/               # Authorization policies
├── Providers/              # Service providers
├── Services/               # Business logic layer ⭐
└── View/                   # View composers (nếu cần)

routes/
├── web.php                 # Routes chính
├── admin.php               # Routes admin (require riêng)
├── api.php                 # Routes API (nếu có)
├── auth.php                # Routes auth
└── console.php             # Schedule definitions
```

### Quy tắc đặt tên file

| Loại | Đặt tên | Ví dụ |
|------|---------|-------|
| Model | Số ít, PascalCase | `Order.php`, `Product.php` |
| Controller | Số ít + Controller | `OrderController.php` |
| Service | Số ít + Service | `OrderService.php` |
| DTO | Hành động + Model + Data | `CreateOrderData.php` |
| Enum | Tên nhóm giá trị | `OrderStatus.php` |
| Event | Past tense | `OrderCreated.php` |
| Listener | Hành động tương lai | `SendOrderConfirmation.php` |
| Job | Hành động + Job | `ProcessPaymentJob.php` |
| FormRequest | Hành động + Request | `StoreOrderRequest.php` |
| Policy | Số ít + Policy | `OrderPolicy.php` |
| Exception | Mô tả lỗi + Exception | `InsufficientStockException.php` |

---

## 3. Luồng request chuẩn

### Ví dụ: Tạo đơn hàng mới

```
POST /orders
    │
    ▼
┌──────────────────┐
│   Middleware      │  auth → role:customer → throttle:10,1 → LogActivity
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│   FormRequest    │  StoreOrderRequest → validate rules
│                  │  Fail? → auto redirect + errors
└────────┬─────────┘
         │ (validated data)
         ▼
┌──────────────────┐
│   Controller     │  $dto = CreateOrderData::fromRequest($request);
│                  │  $order = $this->orderService->create($dto);
│                  │  return redirect()->route('orders.show', $order);
└────────┬─────────┘
         │ (DTO)
         ▼
┌──────────────────┐
│    Service       │  DB::transaction {
│                  │      validate business rules
│                  │      create records
│                  │      event(new OrderCreated($order))
│                  │  }
└────────┬─────────┘
         │
    ┌────┴────┐
    ▼         ▼
┌────────┐ ┌──────────────┐
│ Model  │ │ Event →      │
│ (DB)   │ │ Listener →   │
└────────┘ │ Job (async)  │
           └──────────────┘
```

---

## 4. Enum — Giá trị cố định

### Khi nào dùng Enum?

Khi một cột trong DB chỉ nhận **tập giá trị cố định**: trạng thái, vai trò, loại, phương thức...

### Template Enum cơ bản

```php
<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    // Label tiếng Việt cho UI
    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Chờ xử lý',
            self::Processing => 'Đang xử lý',
            self::Completed  => 'Hoàn thành',
            self::Cancelled  => 'Đã hủy',
        };
    }

    // Màu cho badge UI (Tailwind)
    public function color(): string
    {
        return match ($this) {
            self::Pending    => 'yellow',
            self::Processing => 'blue',
            self::Completed  => 'green',
            self::Cancelled  => 'red',
        };
    }
}
```

### Template Enum với FSM (State Machine)

Dùng khi cần **kiểm soát chuyển trạng thái hợp lệ**:

```php
enum OrderStatus: string
{
    // ... cases ...

    // Định nghĩa quy tắc chuyển trạng thái
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending    => in_array($target, [self::Processing, self::Cancelled]),
            self::Processing => in_array($target, [self::Completed, self::Cancelled]),
            self::Completed, self::Cancelled => false,
        };
    }
}
```

### Kết nối Enum với Model

```php
// Trong Model — cast string ↔ Enum tự động
protected function casts(): array
{
    return [
        'status' => OrderStatus::class,
    ];
}

// Sử dụng:
$order->status;              // OrderStatus::Pending (Enum object)
$order->status->value;       // "pending" (string)
$order->status->label();     // "Chờ xử lý"
$order->status->color();     // "yellow"
```

### Dùng Enum trong Blade

```blade
<span class="badge bg-{{ $order->status->color() }}">
    {{ $order->status->label() }}
</span>
```

---

## 5. DTO — Data Transfer Object

### Khi nào dùng DTO?

Khi Controller cần truyền **nhiều dữ liệu** vào Service (≥3 fields). Nhỏ hơn thì truyền trực tiếp.

### Template DTO

```php
<?php

namespace App\DTOs;

use App\Http\Requests\StoreOrderRequest;

readonly class CreateOrderData
{
    public function __construct(
        public int $customer_id,
        public array $product_ids,
        public string $shipping_address,
        public ?string $note = null,           // nullable + default
        public ?string $coupon_code = null,
    ) {}

    // Factory method: tạo DTO từ validated request
    public static function fromRequest(StoreOrderRequest $request): self
    {
        $data = $request->validated();

        return new self(
            customer_id: $data['customer_id'],
            product_ids: $data['product_ids'],
            shipping_address: $data['shipping_address'],
            note: $data['note'] ?? null,
            coupon_code: $data['coupon_code'] ?? null,
        );
    }
}
```

### Keyword `readonly`

```php
readonly class CreateOrderData { ... }
```

- Mọi property chỉ set **1 lần** trong constructor
- Sau đó **không thể sửa** → đảm bảo dữ liệu nhất quán xuyên suốt flow

### DTO lồng DTO

Khi dữ liệu có cấu trúc phức tạp:

```php
// DTO con: 1 item trong đơn hàng
readonly class OrderItemData
{
    public function __construct(
        public int $product_id,
        public int $quantity,
    ) {}

    public static function fromArray(array $item): self
    {
        return new self(
            product_id: $item['product_id'],
            quantity: $item['quantity'],
        );
    }
}

// DTO cha: chứa mảng DTO con
readonly class CreateOrderData
{
    /** @param OrderItemData[] $items */
    public function __construct(
        public int $customer_id,
        public array $items,          // Mảng OrderItemData
    ) {}

    public static function fromRequest(StoreOrderRequest $request): self
    {
        $data = $request->validated();
        $items = array_map(
            fn (array $item) => OrderItemData::fromArray($item),
            $data['items']
        );

        return new self(
            customer_id: $data['customer_id'],
            items: $items,
        );
    }
}
```

---

## 6. Form Request — Validation

### Khi nào dùng?

**Luôn luôn** dùng cho mọi request tạo/sửa dữ liệu. Không validate trong Controller.

### Template

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Hoặc kiểm tra quyền ở đây
    }

    public function rules(): array
    {
        return [
            'customer_id'      => 'required|exists:users,id',
            'product_ids'      => 'required|array|min:1',
            'product_ids.*'    => 'exists:products,id',
            'shipping_address' => 'required|string|max:500',
            'note'             => 'nullable|string|max:1000',
            'coupon_code'      => 'nullable|string|exists:coupons,code',
        ];
    }

    // Custom messages tiếng Việt (optional)
    public function messages(): array
    {
        return [
            'product_ids.required' => 'Vui lòng chọn ít nhất 1 sản phẩm.',
            'customer_id.exists'   => 'Khách hàng không tồn tại.',
        ];
    }
}
```

### Flow tự động

```
Request → StoreOrderRequest → validate()
                                 │
                          ┌──────┴──────┐
                          ▼             ▼
                       Pass ✅       Fail ❌
                    Controller      auto redirect back
                    nhận data       + $errors (session)
```

---

## 7. Service Layer — Business Logic

### Khi nào dùng?

**Luôn luôn**. Mọi thao tác tạo/sửa/xóa đều qua Service. Controller chỉ gọi Service.

### Template Service

```php
<?php

namespace App\Services;

use App\DTOs\CreateOrderData;
use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    // Inject dependencies qua constructor
    public function __construct(
        private CacheService $cacheService,
    ) {}

    public function create(CreateOrderData $data): Order
    {
        // Wrap trong transaction → all or nothing
        return DB::transaction(function () use ($data) {
            
            // 1. Business validation (không phải form validation)
            $this->validateStock($data->product_ids);

            // 2. Tạo record chính
            $order = Order::create([
                'customer_id' => $data->customer_id,
                'status'      => OrderStatus::Pending,
                'total_price' => $this->calculateTotal($data->product_ids),
                'note'        => $data->note,
            ]);

            // 3. Tạo records phụ (nhiều-nhiều)
            foreach ($data->product_ids as $productId) {
                $product = Product::find($productId);
                $order->products()->attach($productId, [
                    'price_snapshot' => $product->price,   // Snapshot giá
                    'quantity'       => 1,
                ]);
            }

            // 4. Side effects
            Log::channel('order')->info('Order created', [
                'order_id'    => $order->id,
                'customer_id' => $data->customer_id,
            ]);

            // 5. Phát event (listeners xử lý notification, email...)
            event(new OrderCreated($order));

            return $order;
        });
    }

    public function cancel(Order $order, ?string $reason = null): Order
    {
        // Dùng FSM kiểm tra chuyển trạng thái
        if (!$order->status->canTransitionTo(OrderStatus::Cancelled)) {
            throw new \InvalidArgumentException(
                'Không thể hủy đơn ở trạng thái: ' . $order->status->label()
            );
        }

        $order->update([
            'status'        => OrderStatus::Cancelled,
            'cancelled_at'  => now(),
            'cancel_reason' => $reason,
        ]);

        // Xóa cache liên quan
        $this->cacheService->clearOrderCache();

        return $order;
    }
}
```

### Template Controller (Thin Controller)

```php
<?php

namespace App\Http\Controllers;

use App\DTOs\CreateOrderData;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    // Inject service qua constructor
    public function __construct(
        protected OrderService $orderService,
    ) {}

    public function store(StoreOrderRequest $request)
    {
        // Controller chỉ 3 dòng: tạo DTO → gọi Service → redirect
        $order = $this->orderService->create(
            CreateOrderData::fromRequest($request)
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Đặt hàng thành công!');
    }
}
```

---

## 8. Model — Eloquent

### Template Model

```php
<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // 1. Fillable — chỉ cho phép mass assignment các cột này
    protected $fillable = [
        'customer_id', 'status', 'total_price',
        'note', 'cancelled_at', 'cancel_reason',
    ];

    // 2. Casts — tự động convert kiểu dữ liệu
    protected function casts(): array
    {
        return [
            'status'       => OrderStatus::class,  // string ↔ Enum
            'total_price'  => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    // 3. Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
                    ->withPivot('price_snapshot', 'quantity');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // 4. Scopes (query builder helpers) — optional
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            OrderStatus::Cancelled,
            OrderStatus::Completed,
        ]);
    }
}
```

### Quy tắc Relationship

| Quan hệ | Method | Ví dụ |
|---------|--------|-------|
| 1-N | `hasMany()` / `belongsTo()` | User hasMany Orders |
| 1-1 | `hasOne()` / `belongsTo()` | Order hasOne Payment |
| N-M | `belongsToMany()` | Order belongsToMany Products (qua pivot) |

### Tránh N+1 Query

```php
// ❌ N+1: mỗi order query thêm 1 lần để lấy customer
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->customer->name;  // Query mỗi vòng lặp!
}

// ✅ Eager Loading: 1 query duy nhất cho tất cả customers
$orders = Order::with(['customer', 'products'])->get();
foreach ($orders as $order) {
    echo $order->customer->name;  // Đã load sẵn, không query thêm
}
```

---

## 9. Event / Listener / Job

### Khi nào dùng?

Khi Service cần kích hoạt **side effects** (gửi notification, email, cập nhật thống kê...) mà **không muốn nhét vào Service**.

### Template Event

```php
<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}
}
```

### Template Listener

```php
<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendNotificationJob;

class SendOrderConfirmation
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $order->loadMissing(['customer', 'products']); // Tránh N+1

        $message = "Đơn hàng #{$order->id} đã được tạo thành công.";

        // Dispatch job bất đồng bộ (không block request)
        SendNotificationJob::dispatch($order->customer_id, $message);
    }
}
```

### Template Job (Queue)

```php
<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue  // ShouldQueue = async
{
    use Queueable;

    public function __construct(
        private int $userId,
        private string $message,
    ) {}

    public function handle(): void
    {
        Notification::create([
            'user_id' => $this->userId,
            'message' => $this->message,
        ]);
    }
}
```

### Đăng ký Event ↔ Listener

```php
// AppServiceProvider::boot()
Event::listen(OrderCreated::class, SendOrderConfirmation::class);
Event::listen(OrderCancelled::class, SendOrderCancellation::class);
```

### Sơ đồ luồng

```
Service::create()
    └─ event(new OrderCreated($order))
           │
           ▼ (AppServiceProvider đã đăng ký)
    SendOrderConfirmation::handle()
        └─ SendNotificationJob::dispatch()    ← Đưa vào queue
               │
               ▼ (Queue worker xử lý async)
        SendNotificationJob::handle()
            └─ Notification::create()         ← Ghi vào DB
```

---

## 10. Policy — Phân quyền record

### Khác gì Middleware?

| Middleware `role:admin` | Policy |
|------------------------|--------|
| Phân quyền **trang/route** | Phân quyền **từng record** |
| "Chỉ admin vào trang này" | "User này có được sửa order này không?" |

### Template Policy

```php
<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    // Chỉ khách hàng của đơn + đơn chưa xử lý + trước 2 tiếng
    public function cancel(User $user, Order $order): bool
    {
        if ($user->id !== $order->customer_id) {
            return false;
        }

        if ($order->status !== OrderStatus::Pending) {
            return false;
        }

        return $order->created_at->diffInHours(now()) <= 2;
    }

    // Chỉ admin hoặc nhân viên được assign
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->id === $order->assigned_to;
    }
}
```

### Dùng trong Controller

```php
public function cancel(Order $order)
{
    $this->authorize('cancel', $order);
    // Nếu Policy return false → tự động abort(403)

    $this->orderService->cancel($order);
    return back()->with('success', 'Đã hủy đơn.');
}
```

---

## 11. Middleware — Bộ lọc request

### Template RoleMiddleware

```php
<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $allowed = array_map(fn ($r) => UserRole::from($r), $roles);

        if (!auth()->check() || !in_array(auth()->user()->role, $allowed)) {
            abort(403);
        }

        return $next($request);
    }
}
```

### Template LogActivity

```php
class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);

        // Chỉ log thay đổi dữ liệu, bỏ qua GET
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            Log::info('Activity', [
                'user_id'  => $request->user()?->id,
                'method'   => $request->method(),
                'url'      => $request->fullUrl(),
                'ip'       => $request->ip(),
                'duration' => round((microtime(true) - $start) * 1000, 2) . 'ms',
            ]);
        }

        return $response;
    }
}
```

### Template SecurityHeaders

```php
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
```

### Đăng ký Middleware

```php
// bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    $middleware->append(\App\Http\Middleware\LogActivity::class);
})
```

---

## 12. Cache — Tối ưu hiệu suất

### Template CacheService (quản lý tập trung)

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Tập trung keys + TTL tại 1 nơi
    private const KEY_PRODUCTS  = 'active_products';
    private const TTL_PRODUCTS  = 3600;     // 1 giờ

    private const KEY_CATEGORIES = 'categories';
    private const TTL_CATEGORIES = 7200;    // 2 giờ

    private const KEY_REPORT_PREFIX = 'report_';
    private const TTL_REPORT = 900;         // 15 phút

    // ── Lấy dữ liệu (có cache → trả cache, không → query DB) ──

    public function getActiveProducts()
    {
        return Cache::remember(self::KEY_PRODUCTS, self::TTL_PRODUCTS, function () {
            return Product::where('is_active', true)->orderBy('name')->get();
        });
    }

    public function rememberReport(string $key, callable $callback)
    {
        return Cache::remember(self::KEY_REPORT_PREFIX . $key, self::TTL_REPORT, $callback);
    }

    // ── Xóa cache khi dữ liệu thay đổi ──

    public function clearProductCache(): void
    {
        Cache::forget(self::KEY_PRODUCTS);
    }

    public function clearReportCache(): void
    {
        Cache::forget(self::KEY_REPORT_PREFIX . 'monthly');
        Cache::forget(self::KEY_REPORT_PREFIX . 'top_products');
    }
}
```

### Cách dùng trong Service

```php
class ProductService
{
    public function __construct(private CacheService $cacheService) {}

    public function create(CreateProductData $data): Product
    {
        $product = Product::create([...]);
        $this->cacheService->clearProductCache(); // ← Xóa cache cũ
        return $product;
    }
}
```

### Nguyên tắc Cache

```
1. Cache dữ liệu ÍT THAY ĐỔI (danh sách sản phẩm, danh mục, báo cáo)
2. KHÔNG cache dữ liệu thay đổi liên tục (giỏ hàng, trạng thái đơn hàng)
3. Luôn CLEAR cache khi dữ liệu gốc thay đổi (tạo/sửa/xóa)
4. Quản lý TẬP TRUNG qua CacheService (tránh sai key)
```

---

## 13. Exception — Xử lý lỗi

### Template Custom Exception

```php
<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException {}
```

### Cách dùng

```php
// Service throw exception nghiệp vụ
if ($product->stock < $quantity) {
    throw new InsufficientStockException('Sản phẩm hết hàng.');
}

// Controller bắt exception cụ thể
public function store(StoreOrderRequest $request)
{
    try {
        $order = $this->orderService->create(
            CreateOrderData::fromRequest($request)
        );
        return redirect()->route('orders.show', $order);
    } catch (InsufficientStockException $e) {
        return back()->withErrors(['product' => $e->getMessage()]);
    }
}
```

---

## 14. Console Command — Tác vụ nền

### Template Command

```php
<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;

class ExpireOrders extends Command
{
    protected $signature = 'orders:expire';
    protected $description = 'Tự động hủy đơn hàng Pending quá 24 giờ';

    public function handle(): void
    {
        $expired = Order::where('status', OrderStatus::Pending)
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($expired as $order) {
            $order->update([
                'status'        => OrderStatus::Cancelled,
                'cancel_reason' => 'Tự động hủy do quá hạn',
            ]);
        }

        $this->info("Đã hủy {$expired->count()} đơn hàng quá hạn.");
    }
}
```

### Đăng ký Schedule

```php
// routes/console.php
Schedule::command('orders:expire')->hourly();
Schedule::command('logs:cleanup')->weekly();
```

---

## 15. Checklist khi thêm tính năng mới

Khi cần thêm tính năng (VD: hệ thống coupon), làm theo thứ tự:

```
□  1. Migration     → database/migrations/create_coupons_table.php
□  2. Model         → app/Models/Coupon.php (fillable, casts, relationships)
□  3. Enum          → app/Enums/CouponStatus.php (nếu có trạng thái)
□  4. DTO           → app/DTOs/CreateCouponData.php
□  5. FormRequest   → app/Http/Requests/StoreCouponRequest.php
□  6. Service       → app/Services/CouponService.php (business logic)
□  7. Controller    → app/Http/Controllers/Admin/CouponController.php
□  8. Policy        → app/Policies/CouponPolicy.php (nếu cần phân quyền)
□  9. Event         → app/Events/CouponUsed.php (nếu cần side effects)
□ 10. Listener      → app/Listeners/UpdateCouponUsage.php
□ 11. Route         → routes/admin.php (thêm resource route)
□ 12. Views         → resources/views/admin/coupons/ (CRUD views)
□ 13. Cache         → CacheService (nếu cần cache dữ liệu coupon)
□ 14. Test          → tests/Feature/CouponTest.php
```

---

## 16. Quy tắc & Best Practices

### ✅ NÊN làm

| Quy tắc | Lý do |
|---------|-------|
| Controller mỏng, Service dày | Dễ test, tái sử dụng logic |
| Luôn dùng `DB::transaction` khi tạo/sửa nhiều bảng | Đảm bảo consistency |
| Dùng `lockForUpdate()` khi có race condition | Chống trùng dữ liệu |
| Dùng Enum thay vì string cho trạng thái | Type-safe, IDE support |
| Dùng DTO thay vì array cho Service input | Rõ ràng, autocomplete |
| Dùng FormRequest cho validation | Tách biệt, tái sử dụng |
| Dùng Eager Loading (`with()`) | Tránh N+1 query |
| Lưu `price_snapshot` trong pivot | Giữ giá tại thời điểm mua |
| Ghi log cho hành động quan trọng | Debug, audit trail |
| Dùng Event/Listener cho side effects | Service gọn, mở rộng dễ |
| Cache dữ liệu ít thay đổi tập trung | Tăng tốc, dễ quản lý |
| Rate limiting cho API/form submit | Chống spam, DDoS |

### ❌ KHÔNG nên làm

| Anti-pattern | Hậu quả |
|-------------|---------|
| Business logic trong Controller | Controller phình, khó test |
| Query DB trong vòng lặp | N+1 → chậm kinh khủng |
| String rải rác thay Enum | Typo → bug khó tìm |
| Truyền `$request->all()` vào Service | Không type-safe, dễ lọt field xấu |
| Validate trong Controller | Code lặp, khó maintain |
| Cache rải rác (mỗi nơi 1 key) | Sai key → stale data |
| Side effects trong Service chính | Service phình, khó test |
| Bỏ qua transaction khi thao tác nhiều bảng | Data inconsistency |

---

> 📌 **Copy file này vào thư mục `docs/` của dự án mới và tuân thủ.**
