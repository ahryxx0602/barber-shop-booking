# 📍 Tiến độ dự án — BarberBook

> **Cập nhật file này sau mỗi khi hoàn thành 1 bước.**
> Agent đọc file này trước để biết đang ở đâu, không làm lại những gì đã xong.

---

## Trạng thái hiện tại

```
Giai đoạn đang làm : 9 — Kiểm thử & Hoàn thiện
Bước đang làm      : 9.1 — Kiểm tra thủ công toàn bộ luồng
Cập nhật lần cuối  : 23/03/2026
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
| 7 | Review & Notification | ✅ Hoàn thành |
| 7+ | Auth Pages & Error Pages Vintage Redesign | ✅ Hoàn thành |
| 8 | Báo cáo doanh thu (Admin) | ✅ Hoàn thành |
| 8+ | Quản lý tài khoản (Admin) | ✅ Hoàn thành |
| 8++ | DTO Refactor + Fix Barber Bookings | ✅ Hoàn thành |
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
- [x] 7.1 Form viết review (chỉ khi completed, chưa review) — ReviewService + ReviewController + star rating form trong profile
- [x] 7.2 Hiển thị review + rating trên trang thợ — rating summary bar + review list với show all toggle
- [x] 7.3 Tạo Events: `BookingConfirmed`, `BookingCancelled`, `BookingCompleted` (đã làm ở 6+)
- [x] 7.4 In-app notification — Listeners ghi vào bảng `notifications` (đã làm ở 6+)
- [x] 7.5 Notification bell trên header Barber & Admin — dropdown hiển thị thông báo chưa đọc + đánh dấu đã đọc
- [ ] 7.6 (Tuỳ chọn) Email xác nhận booking — bỏ qua, không cần cho demo

### Giai đoạn 7+ — Auth Pages & Error Pages Vintage Redesign (bổ sung)
- [x] 7+.1 Redesign trang Login (`auth/login.blade.php`) — dùng `layouts.client`, vintage card style, show/hide password toggle (Alpine.js)
- [x] 7+.2 Redesign trang Register (`auth/register.blade.php`) — vintage card, validate fields, link đăng nhập
- [x] 7+.3 Redesign trang Forgot Password (`auth/forgot-password.blade.php`) — vintage card, gửi link reset
- [x] 7+.4 Redesign trang Reset Password (`auth/reset-password.blade.php`) — vintage card, xác nhận mật khẩu mới
- [x] 7+.5 Redesign trang Verify Email (`auth/verify-email.blade.php`) — vintage card, gửi lại email xác thực
- [x] 7+.6 Redesign trang Confirm Password (`auth/confirm-password.blade.php`) — vintage card
- [x] 7+.7 Xóa chức năng tự xóa tài khoản — bỏ `destroy()` khỏi ProfileController, xóa route `profile.destroy`, giữ lại `delete-user-form.blade.php` (không sử dụng)
- [x] 7+.8 Thêm chức năng upload avatar cho Client — `Client\ProfileController::update()` hỗ trợ upload ảnh avatar, validate `image|mimes:jpg,jpeg,png,webp|max:2048`
- [x] 7+.9 Hiển thị avatar trong Client nav — `layouts/client.blade.php` hiện avatar user trong navigation (desktop + mobile)
- [x] 7+.10 Thêm upload avatar cho Breeze ProfileController — `ProfileController::update()` cũng hỗ trợ avatar upload
- [x] 7+.11 Hiển thị avatar trong Admin & Barber header — `partials/tailadmin-header.blade.php` và `partials/tailbarber-header.blade.php` hiện avatar user
- [x] 7+.12 Tạo 6 trang lỗi tuỳ chỉnh vintage style — `errors/403.blade.php`, `404.blade.php`, `419.blade.php`, `429.blade.php`, `500.blade.php`, `503.blade.php` — standalone HTML (không dùng layout), custom CSS với theme vintage

### Giai đoạn 8 — Báo cáo (Admin)
- [x] 8.1 Trang báo cáo tổng quan — ReportService + Admin\ReportController + view admin/reports/index + sidebar
- [x] 8.2 Biểu đồ doanh thu theo ngày (Chart.js) — line chart 30 ngày + bộ lọc theo tháng/năm (AJAX) + bar chart năm
- [x] 8.3 Bảng top thợ (theo doanh thu) & top dịch vụ (theo số lần đặt) — grid 2 cột, rank badges, avatar, format VNĐ

### Giai đoạn 8+ — Quản lý tài khoản (Admin) (bổ sung)
- [x] 8+.1 Tạo `Admin\UserController` (index, show, edit, update, toggleActive) + routes resource
- [x] 8+.2 View `admin/users/index.blade.php` — danh sách user phân trang, lọc theo role, tìm kiếm theo tên/email/SĐT
- [x] 8+.3 View `admin/users/show.blade.php` — chi tiết user (thông tin + lịch sử booking nếu là customer)
- [x] 8+.4 View `admin/users/edit.blade.php` — sửa thông tin cơ bản (tên, email, SĐT, role) + toggle kích hoạt/vô hiệu hoá
- [x] 8+.5 Thêm mục "Người dùng" vào sidebar admin (icon Users, giữa "Booking" và "Báo cáo")
- [x] 8+.6 Bảo vệ admin tự khoá — guard backend `toggleActive()` + loại trừ admin hiện tại khỏi danh sách users + ẩn nút toggle trên index/show/edit
- [x] 8+.7 Trang Dashboard admin — 5 stats cards (doanh thu tháng, booking hôm nay, chờ xác nhận, KH, thợ cắt), biểu đồ doanh thu 7 ngày (Chart.js), top 3 thợ cắt, 5 booking gần nhất, 4 quick links
- [x] 8+.8 Thu gọn biểu đồ Chart.js — fix canvas tự kéo dài (wrap fixed-height container), giảm chiều cao chart trên Dashboard (200px) và Reports (220px)

### Giai đoạn 9 — Kiểm thử & Hoàn thiện
- [x] 9.5 Seed dữ liệu demo đầy đủ cho buổi bảo vệ
  - [x] `UserSeeder` — 1 admin, 5 barbers, 50 customers (rải 01/2025 → 03/2026)
  - [x] `BarberSeeder` — cập nhật 5 bio + experience, dùng modulo
  - [x] `BookingSeeder` — ~200+ bookings (14 tháng lịch sử + T3/2026 + 14 booking hôm nay: 8 pending, 4 confirmed, 2 in_progress), firstOrCreate cho TimeSlot
  - [x] `ReviewSeeder` — 70% completed có review, rating phân bố thực tế (45% 5⭐), cập nhật barber rating
  - [x] `PaymentSeeder` — payment cho mọi booking non-cancelled (50% cash, 30% momo, 20% vnpay)
- [x] 9.7 Fix trang Quản lý Booking — sửa lỗi filter ngày (Carbon object vs string), tối ưu giao diện compact (ngày trống 1 dòng, booking cards dạng table rows, hôm nay viền xanh)
- [ ] 9.1 Kiểm tra thủ công toàn bộ luồng
- [ ] 9.2 Xử lý edge cases
- [ ] 9.3 Responsive mobile
- [ ] 9.4 Validation message tiếng Việt
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
- **22/03/2026**: Giai đoạn 7 hoàn thành — (1) ReviewService + StoreReviewRequest + ReviewController: client viết review cho booking completed, tự cập nhật barber rating trung bình; (2) Trang thợ có rating summary bar (biểu đồ phân bố sao) + danh sách review có toggle "xem tất cả"; (3) Notification bell trên header Barber & Admin — dropdown hiển thị 10 thông báo chưa đọc + nút "đánh dấu tất cả đã đọc"; (4) Sửa Listeners thiếu trường `type`/`title` khi tạo notification.
- **23/03/2026**: Giai đoạn 7+ hoàn thành — (1) Redesign toàn bộ 6 trang auth (login, register, forgot-password, reset-password, verify-email, confirm-password) sang vintage barbershop style, dùng `layouts.client` thay vì `layouts.guest`; (2) Xóa chức năng tự xóa tài khoản (destroy) khỏi ProfileController và routes; (3) Thêm upload avatar cho client profile (`Client\ProfileController`) và Breeze profile (`ProfileController`); (4) Hiển thị avatar user trong navigation client, admin header, barber header; (5) Tạo 6 trang lỗi tuỳ chỉnh (403, 404, 419, 429, 500, 503) với vintage standalone design — mỗi trang có icon riêng, animation CSS, nội dung tiếng Việt phù hợp.
- **23/03/2026**: Bước 8.1 hoàn thành — Tạo `ReportService` (getMonthlyOverview), `Admin\ReportController`, view `admin/reports/index.blade.php` với 3 stat cards (tổng booking, doanh thu, khách mới + % so sánh tháng trước), thêm mục "Báo cáo" vào sidebar admin.
- **23/03/2026**: Bước 8.2 hoàn thành + mở rộng — (1) Biểu đồ doanh thu 30 ngày gần nhất dùng Chart.js CDN (line chart, gradient fill, tooltip VNĐ, trục Y rút gọn k/tr); (2) Mở rộng thêm bộ lọc thời gian: 3 tabs (30 ngày / Theo tháng / Theo năm) + dropdowns tháng/năm; (3) AJAX fetch API cập nhật chart không reload trang; (4) Chế độ "Theo năm" dùng bar chart 12 tháng; (5) Thêm `ReportService::getDailyRevenue(?month, ?year)`, `getMonthlyRevenue(year)`, `getAvailableYears()`; (6) Thêm route API `admin/reports/chart-data` + method `ReportController::chartData()`.
- **23/03/2026**: Bước 8.3 hoàn thành — Thêm 2 bảng xếp hạng: (1) Top thợ cắt theo doanh thu tháng (left join bookings, SUM total_price) với avatar, rank badge vàng/bạc/đồng, số booking + rating; (2) Top dịch vụ theo số lần đặt tháng (join booking_services + bookings) với hình ảnh, giá, doanh thu. Cả 2 dùng grid 2 cột trên desktop. Giai đoạn 8 hoàn thành 100%.
- **23/03/2026**: Giai đoạn 8+ hoàn thành — (1) Migration thêm cột `is_active` boolean (default true) vào bảng `users`; (2) Cập nhật `User` model (fillable + cast boolean); (3) Tạo `Admin\UserController` (index/show/edit/update/toggleActive) — index có lọc role + tìm kiếm tên/email/SĐT + stats cards đếm theo role; show hiển thị chi tiết user + lịch sử 10 booking gần nhất (customer); (4) Tạo `UpdateUserRequest` với validate email unique ignore current + role enum; (5) 3 views admin/users: index (bảng + filter bar + pagination), show (card thông tin + bảng booking), edit (form name/email/phone/role + toggle active); (6) Thêm route resource + PATCH toggleActive; (7) Thêm mục "Người dùng" vào sidebar admin (icon Users, giữa Booking và Báo cáo).
- **23/03/2026**: Giai đoạn 8++ hoàn thành — DTO Refactor + Fix Barber Bookings: (1) Tạo 6 DTO classes trong `app/DTOs/` (`CreateBookingData`, `CreateBarberData`, `UpdateBarberData`, `StoreReviewData`, `ScheduleItemData`, `UpdateScheduleData`) — dùng PHP 8.2 readonly class + named arguments; (2) Refactor 4 Services (`BookingService`, `BarberService`, `ReviewService`, `ScheduleService`) — thay `array $data` bằng typed DTO; (3) Refactor 5 Controllers dùng `DTO::fromRequest()` / `DTO::fromArray()`; (4) Fix giao diện `barber/bookings/index.blade.php` — chuyển từ card-based sang compact table style giống admin (grouped by day, inline action icon buttons); (5) Fix lỗi filter `booking_date` (Carbon vs string) trong `Barber\BookingController` — dùng `filter()` với `format('Y-m-d')` giống admin; (6) Cập nhật `03-Conventions.md` thêm quy ước DTO.

---

## Hướng dẫn cập nhật file này

Khi hoàn thành 1 bước, đổi `- [ ]` thành `- [x]` và cập nhật phần "Trạng thái hiện tại":

```
Giai đoạn đang làm : 6 — Quản lý Booking
Bước đang làm      : 6.1 — Dashboard Barber
Cập nhật lần cuối  : 22/03/2026
```

Khi hoàn thành cả giai đoạn, đổi `⬜ Chưa bắt đầu` thành `✅ Hoàn thành` trong bảng tổng quan.
