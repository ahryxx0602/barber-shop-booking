# AGENT CONTEXT — BarberBook

> Đọc file này trước. Sau đó đọc file được chỉ định tuỳ task.
> Không đoán mò. Không tự thêm package. Hỏi nếu chưa rõ yêu cầu.

---

## Dự án là gì

Hệ thống đặt lịch cắt tóc trực tuyến. Tên: **BarberBook**.
Đồ án tốt nghiệp của sinh viên đại học — ưu tiên **rõ ràng, đơn giản, dễ giải thích** hơn là over-engineer.

---

## Stack & phiên bản chính xác

| | |
|---|---|
| PHP | 8.2+ |
| Laravel | **12** |
| Blade | template engine chính, không dùng Vue/React |
| Tailwind CSS | v3 |
| Alpine.js | v3 (chỉ cho UI nhỏ: toggle, realtime tính tiền) |
| SQLite | database (có thể chuyển MySQL cho production) |
| Auth | Laravel Breeze (Blade stack) |
| Queue driver | `database` |
| Mail (dev) | Mailtrap |

---

## Các file tài liệu

| File | Nội dung |
|---|---|
| `00Agent.md` | File này — đọc trước |
| `01project-overview.md` | Kiến trúc, design pattern, cấu trúc thư mục, toàn bộ schema DB |
| `02developer-plan.md` | Kế hoạch 10 giai đoạn, từng bước có code mẫu |
| `03-Conventions.md` | Quy ước đặt tên, coding style, cách viết blade, route |
| `04-current-progress.md` | **Cập nhật thủ công** — giai đoạn nào xong, giai đoạn nào đang làm |

---

## 3 Role trong hệ thống

| Role | Prefix route | Layout blade | Enum |
|---|---|---|---|
| `customer` (client) | `/` (public) + `/profile` (auth) | `layouts.client` | `UserRole::Customer` |
| `barber` | `/barber/...` | `layouts.tailbarber` | `UserRole::Barber` |
| `admin` | `/admin/...` | `layouts.tailadmin` | `UserRole::Admin` |

Middleware bảo vệ: `role:admin`, `role:barber,admin`

---

## Enums (PHP 8.1 backed enums)

| Enum | File | Giá trị |
|---|---|---|
| `BookingStatus` | `app/Enums/BookingStatus.php` | `Pending`, `Confirmed`, `InProgress`, `Completed`, `Cancelled` |
| `TimeSlotStatus` | `app/Enums/TimeSlotStatus.php` | `Available`, `Booked` |
| `UserRole` | `app/Enums/UserRole.php` | `Admin`, `Barber`, `Customer` |

Tất cả Models có `casts()` trả về Enum tương ứng. **Không dùng string literals** khi so sánh status/role.

Mỗi Enum có method `label()` trả về tên tiếng Việt — dùng trong Blade: `$booking->status->label()`

---

## Các bảng DB (tóm tắt)

```
users               — auth + role (customer/barber/admin)
barbers             — profile thợ, FK → users
services            — dịch vụ: tên, giá, duration
working_schedules   — lịch mặc định mỗi tuần của thợ
time_slots          — slot giờ cụ thể từng ngày (auto-generate)
bookings            — đơn đặt lịch
booking_services    — pivot: booking ↔ service (có snapshot giá)
payments            — thanh toán cho booking
reviews             — đánh giá sau khi completed
notifications       — thông báo in-app
```

Chi tiết từng cột → xem `01project-overview.md` mục 4.

---

## Design Pattern đang dùng

| Pattern | Dùng ở đâu |
|---|---|
| Service Layer | `app/Services/` — business logic (BookingService, BarberService, ScheduleService, ServiceService, TimeSlotService) |
| Event/Listener | `app/Events/` + `app/Listeners/` — notification khi booking thay đổi trạng thái |
| Enum | `app/Enums/` — type-safe status/role, thay thế string literals |
| Policy | `app/Policies/BookingPolicy.php` — phân quyền hành động trên booking |

---

## Events & Listeners

| Event | Listener | Khi nào |
|---|---|---|
| `BookingConfirmed` | `SendBookingConfirmedNotification` | Barber xác nhận booking |
| `BookingCancelled` | `SendBookingCancelledNotification` | Booking bị hủy (khách hoặc barber) |
| `BookingCompleted` | `SendBookingCompletedNotification` | Barber đánh dấu hoàn thành |

Đăng ký trong `AppServiceProvider::boot()` bằng `Event::listen()`.

---

## Quy tắc quan trọng khi sinh code

1. **Mọi business logic** phải nằm trong `Services/`, không viết trong Controller
2. **Dùng Enum**, không hardcode string cho status/role — dùng `BookingStatus::Pending`, `UserRole::Admin`, etc.
3. **Tạo booking** bắt buộc dùng `DB::transaction()` + `lockForUpdate()` để tránh race condition
4. **Snapshot giá** khi tạo booking — lưu `price_snapshot` và `duration_snapshot` vào `booking_services`
5. **Không dùng** `RouteServiceProvider`, `EventServiceProvider`, `Console/Kernel` — Laravel 12 đã xoá
6. **Event listener** đăng ký trong `AppServiceProvider::boot()`
7. **Scheduler** đăng ký trong `routes/console.php`
8. **Không cài package ngoài** trừ khi được chỉ định rõ trong task
9. **Blade views** dùng `@use('App\Enums\BookingStatus')` ở đầu file để import Enum

---

## Luồng trạng thái Booking

```
pending → confirmed → in_progress → completed
        ↘ cancelled               ↗
          (bởi customer hoặc barber từ chối)
```

Dùng `BookingStatus::canTransitionTo()` để kiểm tra transition hợp lệ.
Khi huỷ hoặc từ chối → **bắt buộc** mở lại `time_slots.status = TimeSlotStatus::Available`

---

## Artisan Commands tự định nghĩa

```bash
php artisan slots:generate   # generate time_slots cho 7 ngày tới
php artisan bookings:expire  # huỷ booking pending quá 30 phút
```

---

## Khi được giao task, agent cần làm theo thứ tự:

1. Đọc `04-current-progress.md` để biết đang ở giai đoạn nào
2. Đọc phần tương ứng trong `02developer-plan.md`
3. Đọc `03-Conventions.md` để viết đúng style
4. Sinh code theo đúng pattern đã quy định
5. Không tự ý nhảy sang giai đoạn tiếp theo
