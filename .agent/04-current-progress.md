# 📍 Tiến độ dự án — BarberBook

> **Cập nhật file này sau mỗi khi hoàn thành 1 bước.**
> Agent đọc file này trước để biết đang ở đâu, không làm lại những gì đã xong.

---

## Trạng thái hiện tại

```
Giai đoạn đang làm : 7 — Review & Notification
Bước đang làm      : 7.1 — Form viết review
Cập nhật lần cuối  : 22/03/2026
```

---

## Tổng quan tiến độ

| Giai đoạn | Tên | Trạng thái |
|---|---|---|
| 0 | Cài đặt môi trường & khởi tạo dự án | ✅ Hoàn thành |
| 1 | Database, Models, Seeders | ✅ Hoàn thành |
| 2 | Auth & phân quyền 3 role | ✅ Hoàn thành |
| 3 | Module Admin: Thợ & Dịch vụ | ✅ Hoàn thành |
| 4 | Quản lý lịch làm việc (Barber) | ✅ Hoàn thành |
| 5 | Core Booking — đặt lịch (Client) | ✅ Hoàn thành |
| 5+ | Giao diện Client & Profile | ✅ Hoàn thành |
| 5++ | Refactor: Dependency Injection cho Controllers | ✅ Hoàn thành |
| 6 | Quản lý Booking (Barber + Client) | ✅ Hoàn thành |
| 6+ | Enums + Events/Listeners refactor | ✅ Hoàn thành |
| 7 | Review & Notification | ⬜ Chưa bắt đầu |
| 8 | Báo cáo doanh thu (Admin) | ⬜ Chưa bắt đầu |
| 9 | Kiểm thử & Hoàn thiện | ⬜ Chưa bắt đầu |

---

## Chi tiết từng giai đoạn

### Giai đoạn 0 — Cài đặt môi trường
- [x] 0.1 Cài đặt công cụ (PHP, Composer, MySQL, Node.js)
- [x] 0.2 Tạo project Laravel 12
- [x] 0.3 Cài Tailwind CSS
- [x] 0.4 Cài Laravel Breeze (Blade stack)
- [x] 0.5 Cấu hình `.env` + tạo database + chạy migrate lần đầu

### Giai đoạn 1 — Database & Models
- [x] 1.1 Thêm cột `role`, `phone`, `avatar` vào bảng `users`
- [x] 1.2 Migration `barbers`
- [x] 1.3 Migration `services`
- [x] 1.4 Migration `working_schedules`
- [x] 1.5 Migration `time_slots`
- [x] 1.6 Migration `bookings`
- [x] 1.7 Migration `booking_services`
- [x] 1.8 Migration `payments`, `reviews`, `notifications`
- [x] 1.9 Chạy `php artisan migrate` — kiểm tra đủ 13 bảng (bao gồm cache, jobs)
- [x] 1.10 Tạo Models với đầy đủ relationship (User, Barber, Service, WorkingSchedule, TimeSlot, Booking, Payment, Review, Notification)
- [x] 1.11 Tạo Seeders + chạy seed dữ liệu mẫu (UserSeeder, BarberSeeder, ServiceSeeder)

### Giai đoạn 2 — Auth & Phân quyền
- [x] 2.1 Tạo `RoleMiddleware`
- [x] 2.2 Tổ chức routes theo role (admin.php, barber.php, web.php)
- [x] 2.3 Redirect sau đăng nhập đúng theo role (admin→admin.dashboard, barber→barber.dashboard, customer→client.profile.show)
- [x] 2.4 Tạo layouts Blade (app, barber/tailbarber, admin/tailadmin, client, guest, navigation)
- [x] 2.5 Tạo dashboard cơ bản cho admin & barber (customer dashboard đã xóa, thay bằng client profile)

### Giai đoạn 3 — Admin: Thợ & Dịch vụ
- [x] 3.1 CRUD Services (có upload ảnh, validate) — Admin\ServiceController
- [x] 3.2 CRUD Barbers (tạo user + barber record trong transaction) — Admin\BarberController
- [x] 3.3 Upload ảnh dùng `Storage::disk('public')`

### Giai đoạn 4 — Lịch làm việc (Barber)
- [x] 4.1 Giao diện cài working schedule (Barber\ScheduleController + Admin\ScheduleController)
- [x] 4.2 Tạo `TimeSlotService` với method `generateForBarber()`
- [x] 4.3 Artisan command `slots:generate` (GenerateTimeSlots, --days=7)
- [x] 4.4 Auto-generate slots khi barber lưu schedule

### Giai đoạn 5 — Core Booking
- [x] 5.1 Trang danh sách thợ (client/barbers/index.blade.php)
- [x] 5.2 Trang chi tiết thợ (client/barbers/show.blade.php)
- [x] 5.3 Form đặt lịch — Bước 1: chọn dịch vụ + Bước 2: chọn thợ (Alpine.js wizard)
- [x] 5.4 Form đặt lịch — Bước 3: chọn ngày & slot giờ (async fetch API)
- [x] 5.5 Tạo `BookingService` với `create()` dùng transaction + lockForUpdate
- [x] 5.6 Trang xác nhận booking (confirmation.blade.php)
- [x] 5.7 Trang lịch sử booking (tích hợp trong client profile)

### Giai đoạn 5+ — Giao diện Client & Profile (bổ sung)
- [x] 5+.1 Thiết kế giao diện Editorial Vintage (Tailwind config: theme colors, fonts Playfair Display + Epilogue)
- [x] 5+.2 Client layout (layouts/client.blade.php) — Nav cố định, footer 3 cột, responsive
- [x] 5+.3 Trang chủ (welcome.blade.php) — Hero, Services, Story, Barbers Preview, CTA
- [x] 5+.4 Trang thông tin cá nhân (Client\ProfileController — show, edit, update)
- [x] 5+.5 Profile views (client/profile/show.blade.php, edit.blade.php) — thông tin cá nhân + lịch sử booking
- [x] 5+.6 Xóa customer dashboard cũ, chuyển sang client profile
- [x] 5+.7 Booking cho khách vãng lai (guest) — điền SĐT + email, tự tạo user không cần mật khẩu
- [x] 5+.8 Navigation: "Tài khoản" cho user đã đăng nhập, "Đăng nhập" cho guest

### Giai đoạn 5++ — Refactor: Dependency Injection cho Controllers
- [x] 5++.1 Tạo `BarberService` — extract store/update/destroy logic từ Admin\BarberController
- [x] 5++.2 Tạo `ServiceService` — extract store/update/destroy logic từ Admin\ServiceController
- [x] 5++.3 Tạo `ScheduleService` — extract shared logic + DAY_LABELS từ Admin & Barber ScheduleControllers
- [x] 5++.4 Refactor `Admin\BarberController` → constructor injection `BarberService`
- [x] 5++.5 Refactor `Admin\ServiceController` → constructor injection `ServiceService`
- [x] 5++.6 Refactor `Admin\ScheduleController` → constructor injection `ScheduleService`
- [x] 5++.7 Refactor `Barber\ScheduleController` → constructor injection `ScheduleService`

### Giai đoạn 6 — Quản lý Booking
- [x] 6.1 Dashboard Barber: danh sách booking theo ngày (date picker, stats cards, booking list)
- [x] 6.2 Thợ xác nhận / từ chối booking (Barber\BookingController + BookingService)
- [x] 6.3 Thợ đánh dấu in_progress / completed (status transition + reopen slot khi reject/cancel)
- [x] 6.4 Client huỷ booking (kiểm tra ≥2 tiếng trước giờ hẹn, form nhập lý do)
- [x] 6.5 Tạo `BookingPolicy` (confirm, reject, start, complete, cancel)
- [x] 6.6 Trang Booking theo tuần cho Barber (`/barber/bookings` — chuyển tuần, 7 ngày, stats tuần)
- [x] 6.7 Trang Booking Admin (`/admin/bookings` — chọn thợ qua tabs, xem booking theo tuần)
- [x] 6.8 Extract `barber/partials/booking-card.blade.php` — dùng chung cho dashboard + trang booking tuần
- [x] 6.9 Sidebar: thêm mục Booking cho cả Admin và Barber

### Giai đoạn 6+ — Enums + Events/Listeners Refactor
- [x] 6+.1 Tạo Enums: `BookingStatus`, `TimeSlotStatus`, `UserRole` (app/Enums/)
- [x] 6+.2 Cập nhật Models (Booking, TimeSlot, User) — thêm Enum casts
- [x] 6+.3 Cập nhật BookingPolicy — dùng Enum thay string literals
- [x] 6+.4 Cập nhật BookingService — dùng Enum + dispatch Events (BookingConfirmed, BookingCancelled, BookingCompleted)
- [x] 6+.5 Cập nhật RoleMiddleware — dùng `UserRole::from()`
- [x] 6+.6 Tạo Events: `BookingConfirmed`, `BookingCancelled`, `BookingCompleted`
- [x] 6+.7 Tạo Listeners: `SendBookingConfirmedNotification`, `SendBookingCancelledNotification`, `SendBookingCompletedNotification`
- [x] 6+.8 Đăng ký Events trong `AppServiceProvider::boot()`
- [x] 6+.9 Cập nhật tất cả Controllers — dùng Enum thay string
- [x] 6+.10 Cập nhật Blade views — dùng `@use()` + Enum + `->label()`
- [x] 6+.11 Cập nhật TimeSlotService — dùng `TimeSlotStatus::Available`
- [x] 6+.12 Xóa file thừa: `routes/customer.php`, `Customer/DashboardController.php`, `views/customer/`
- [x] 6+.13 Sửa `RegisteredUserController` — route `customer.dashboard` → `client.profile.show`
- [x] 6+.14 Sửa `web.php` — dùng `UserRole::Admin`, `UserRole::Barber` trong match

### Giai đoạn 7 — Review & Notification
- [ ] 7.1 Form viết review (chỉ khi completed, chưa review)
- [ ] 7.2 Hiển thị review + rating trên trang thợ
- [x] 7.3 Tạo Events: `BookingConfirmed`, `BookingCancelled`, `BookingCompleted` (đã làm ở 6+)
- [x] 7.4 In-app notification — Listeners ghi vào bảng `notifications` (đã làm ở 6+)
- [ ] 7.5 (Tuỳ chọn) Email xác nhận booking

### Giai đoạn 8 — Báo cáo (Admin)
- [ ] 8.1 Trang báo cáo tổng quan
- [ ] 8.2 Biểu đồ doanh thu theo ngày (Chart.js)
- [ ] 8.3 Bảng top thợ, top dịch vụ

### Giai đoạn 9 — Kiểm thử & Hoàn thiện
- [ ] 9.1 Kiểm tra thủ công toàn bộ luồng
- [ ] 9.2 Xử lý edge cases
- [ ] 9.3 Responsive mobile
- [ ] 9.4 Validation message tiếng Việt
- [ ] 9.5 Seed dữ liệu demo đầy đủ cho buổi bảo vệ
- [ ] 9.6 Viết README

---

## Ghi chú / Vấn đề cần giải quyết

> Thêm vào đây khi gặp vấn đề hoặc quyết định thay đổi gì đó so với kế hoạch ban đầu.

- **22/03/2026**: Đổi tên "Customer" → "Client" trong toàn bộ giao diện người dùng. Customer dashboard cũ (dùng `x-app-layout`) đã xóa, thay bằng Client Profile (dùng `layouts.client` vintage). Route `/customer/dashboard` không còn, thay bằng `/profile` (client.profile.show).
- **22/03/2026**: Booking không còn yêu cầu đăng nhập. Khách vãng lai có thể đặt lịch bằng cách điền tên + SĐT + email. Hệ thống tự `firstOrCreate` user theo email với mật khẩu ngẫu nhiên.
- **22/03/2026**: Routes Breeze profile (`/profile`) đã đổi thành `/profile/breeze` để tránh xung đột với client profile route.
- **22/03/2026**: Refactor Dependency Injection — tách business logic ra Service layer (BarberService, ServiceService, ScheduleService). Controllers chỉ còn nhận request + gọi service + trả response. Xóa duplicate DAY_LABELS giữa Admin & Barber ScheduleController.
- **22/03/2026**: Giai đoạn 6 mở rộng thêm so với kế hoạch: (1) Trang Booking theo tuần cho Barber — tách riêng với Dashboard theo ngày; (2) Trang Booking Admin — chọn thợ qua tabs + xem booking theo tuần; (3) Buttons action dùng style outlined (border + text color) thay vì filled để dễ đọc hơn.
- **22/03/2026**: Refactor lớn — thêm PHP Enums (`BookingStatus`, `TimeSlotStatus`, `UserRole`) thay thế toàn bộ string literals. Thêm Events/Listeners cho booking lifecycle (Confirmed, Cancelled, Completed) → tự động ghi notification vào DB. Xóa file thừa từ module Customer cũ (`routes/customer.php`, `Customer/DashboardController`, `views/customer/`). Sửa broken route `customer.dashboard` → `client.profile.show`.

---

## Hướng dẫn cập nhật file này

Khi hoàn thành 1 bước, đổi `- [ ]` thành `- [x]` và cập nhật phần "Trạng thái hiện tại":

```
Giai đoạn đang làm : 6 — Quản lý Booking
Bước đang làm      : 6.1 — Dashboard Barber
Cập nhật lần cuối  : 22/03/2026
```

Khi hoàn thành cả giai đoạn, đổi `⬜ Chưa bắt đầu` thành `✅ Hoàn thành` trong bảng tổng quan.
