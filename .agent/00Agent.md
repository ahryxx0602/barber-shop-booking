# 🤖 AGENT CONTEXT — BarberBook

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
| MySQL | 8 |
| Auth | Laravel Breeze (Blade stack) |
| Queue driver | `database` |
| Mail (dev) | Mailtrap |

---

## Các file tài liệu

| File | Nội dung |
|---|---|
| `00_AGENT.md` | File này — đọc trước |
| `01_project_overview.md` | Kiến trúc, design pattern, cấu trúc thư mục, toàn bộ schema DB |
| `02_development_plan.md` | Kế hoạch 10 giai đoạn, từng bước có code mẫu |
| `03_conventions.md` | Quy ước đặt tên, coding style, cách viết blade, route |
| `04_current_progress.md` | **Cập nhật thủ công** — giai đoạn nào xong, giai đoạn nào đang làm |

---

## 3 Role trong hệ thống

| Role | Prefix route | Layout blade |
|---|---|---|
| `customer` | `/customer/...` | `layouts.app` |
| `barber` | `/barber/...` | `layouts.barber` |
| `admin` | `/admin/...` | `layouts.admin` |

Middleware bảo vệ: `role:customer`, `role:barber`, `role:admin`

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

Chi tiết từng cột → xem `01_project_overview.md` mục 4.

---

## Design Pattern đang dùng

| Pattern | Dùng ở đâu |
|---|---|
| Repository | `app/Repositories/` — tách query DB |
| Service Layer | `app/Services/` — business logic |
| Observer/Event | `app/Events/` + `app/Listeners/` — notification, mail |
| Strategy | `app/Strategies/Payment/` — mở rộng thanh toán |
| Policy | `app/Policies/BookingPolicy.php` — phân quyền hành động |

---

## Quy tắc quan trọng khi sinh code

1. **Mọi business logic** phải nằm trong `Services/`, không viết trong Controller
2. **Mọi DB query phức tạp** phải nằm trong `Repositories/`, Controller không gọi Model trực tiếp ngoại trừ các query đơn giản
3. **Tạo booking** bắt buộc dùng `DB::transaction()` + `lockForUpdate()` để tránh race condition
4. **Snapshot giá** khi tạo booking — lưu `price_snapshot` và `duration_snapshot` vào `booking_services`, không dùng giá hiện tại của `services`
5. **Không dùng** `RouteServiceProvider`, `EventServiceProvider`, `Console/Kernel` — Laravel 12 đã xoá
6. **Event listener** đăng ký trong `AppServiceProvider::boot()`
7. **Scheduler** đăng ký trong `routes/console.php`
8. **Không cài package ngoài** trừ khi được chỉ định rõ trong task

---

## Luồng trạng thái Booking

```
pending → confirmed → in_progress → completed
        ↘ cancelled               ↗
          (bởi customer hoặc barber từ chối)
```

Khi huỷ hoặc từ chối → **bắt buộc** mở lại `time_slots.status = 'available'`

---

## Artisan Commands tự định nghĩa

```bash
php artisan slots:generate   # generate time_slots cho 7 ngày tới
php artisan bookings:expire  # huỷ booking pending quá 30 phút
```

---

## Khi được giao task, agent cần làm theo thứ tự:

1. Đọc `04_current_progress.md` để biết đang ở giai đoạn nào
2. Đọc phần tương ứng trong `02_development_plan.md`
3. Đọc `03_conventions.md` để viết đúng style
4. Sinh code theo đúng pattern đã quy định
5. Không tự ý nhảy sang giai đoạn tiếp theo