# 📍 Tiến độ dự án — BarberBook

> **Cập nhật file này sau mỗi khi hoàn thành 1 bước.**
> Agent đọc file này trước để biết đang ở đâu, không làm lại những gì đã xong.

---

## Trạng thái hiện tại

```
Giai đoạn đang làm : 13 — Mở rộng quản trị Admin (Exp P3)
Bước đang làm      : 13.3 — Quản lý sản phẩm bán kèm (Phase 1 ✅, Phase 2 ✅, Phase 3 ✅, Phase 4 ✅, Phase 5 ✅, Coupon ✅, Phase 6 ✅)
Cập nhật lần cuối  : 25/03/2026
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
| 9 | Kiểm thử & Hoàn thiện | ✅ Hoàn thành |
| 10 | Post-Review Improvements | ✅ Hoàn thành |
| 11 | Nâng cao trải nghiệm khách hàng (Exp P1) | ✅ Hoàn thành |
| 12 | Tối ưu vận hành cho Barber (Exp P2) | ✅ Hoàn thành |
| 13 | Mở rộng quản trị Admin (Exp P3) | 🔄 Đang làm |

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
- [x] 9.8 Audit & Tối ưu hiệu năng N+1 Queries (7 issues trong Controller & Service, dùng Eager Loading / Upsert / SelectRaw)
- [x] 9.1 Kiểm tra thủ công toàn bộ luồng
- [x] 9.2 Xử lý edge cases
- [x] 9.3 Responsive mobile
- [x] 9.4 Validation message tiếng Việt
- [x] 9.6 Viết README

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
- [x] 23/03/2026: Giai đoạn 8++ hoàn thành — DTO Refactor + Fix Barber Bookings: (1) Tạo 6 DTO classes trong `app/DTOs/` (`CreateBookingData`, `CreateBarberData`, `UpdateBarberData`, `StoreReviewData`, `ScheduleItemData`, `UpdateScheduleData`) — dùng PHP 8.2 readonly class + named arguments; (2) Refactor 4 Services (`BookingService`, `BarberService`, `ReviewService`, `ScheduleService`) — thay `array $data` bằng typed DTO; (3) Refactor 5 Controllers dùng `DTO::fromRequest()` / `DTO::fromArray()`; (4) Fix giao diện `barber/bookings/index.blade.php` — chuyển từ card-based sang compact table style giống admin (grouped by day, inline action icon buttons); (5) Fix lỗi filter `booking_date` (Carbon vs string) trong `Barber\BookingController` — dùng `filter()` với `format('Y-m-d')` giống admin; (6) Cập nhật `03-Conventions.md` thêm quy ước DTO.
- **23/03/2026**: Bước 9.8 hoàn thành — Audit & Tối ưu 7 lỗi N+1 Queries: (1) `ScheduleService` dùng `upsert` gom 14 queries thành 1; (2) `TimeSlotService` pre-load schedule 7 ngày trước loop; (3) `BarberService` và `ReviewService` eager load models tránh queries ẩn; (4) Thêm `load('user')` trong `Admin\ScheduleController`; (5) Dùng `withCount('reviews')` cho danh sách thợ cắt tóc của client; (6) Gom 5 truy vấn `count()` riêng rẽ trên dashboard admin thành 2 câu `selectRaw` tổng hợp kết hợp `CASE WHEN`. Thống kê SQL queries giảm đáng kể tại các trang tải nặng.
- **23/03/2026**: Tích hợp thanh toán Sandbox — (1) Chuyển toàn bộ VNPay Sandbox timestamp sang `Asia/Ho_Chi_Minh` để chặn báo lỗi Session Timeout ngay lúc redirect do Server/App chạy múi giờ default; (2) Redesign trang Chọn Phương Thức Thanh Toán thành `Layout 2 Cột` (Summary đơn hàng + Nút radio thanh toán) cho Tablet/Desktop nhằm loại bỏ action cuộn chuột làm gãy UI luồng thanh toán; (3) Sử dụng custom `v-btn-primary` thay vì btn viền mỏng nhỏ cho form confirm thanh toán để đồng bộ hệ thống nút lớn toàn site.
- **23/03/2026**: Giai đoạn 10 hoàn thành — Post-Review Improvements (9 items): (1) Idempotency check VNPay/MoMo callback; (2) MoMo HMAC SHA256 signature verification; (3) VNPay IPN server-to-server endpoint + CSRF excluded route; (4) FSM `canTransitionTo()` guard trong BookingService (5 transitions); (5) Rate limiting `throttle:5,1` cho booking/payment POST routes; (6) Batch `upsert()` thay `firstOrCreate` loop trong TimeSlotService; (7) `Http::post()` thay raw cURL cho MoMo; (8) Thêm log cho `start()` method; (9) Thêm `PaymentStatus::Failed` enum + migration.

### Giai đoạn 10 — Post-Review Improvements
- [x] 10.1 Idempotency check cho VNPay/MoMo callback (PaymentService)
- [x] 10.2 MoMo callback signature verification — HMAC SHA256 (PaymentService)
- [x] 10.3 VNPay IPN endpoint — server-to-server (PaymentController + web.php)
- [x] 10.4 Enforce `canTransitionTo()` FSM guard — 5 transitions (BookingService)
- [x] 10.5 Rate limiting `throttle:5,1` cho POST /booking và POST /payment/{booking}
- [x] 10.6 Batch `upsert()` thay `firstOrCreate` loop (TimeSlotService)
- [x] 10.7 `Http::post()` thay raw cURL cho MoMo API (PaymentService)
- [x] 10.8 Thêm log cho `start()` method + xoá dead code (BookingService)
- [x] 10.9 Thêm `PaymentStatus::Failed` enum case + migration bổ sung DB enum

### Giai đoạn 11 — Nâng cao trải nghiệm khách hàng (Exp P1)
- [x] 11.1 Hệ thống Loyalty / Tích điểm (Model, Service, Observer)
- [x] 11.2 Mã giảm giá / Coupon (Model, Service, UI trong Booking)
- [x] 11.3 Đặt lịch lặp lại (Recurring Booking)
- [x] 11.4 Danh sách thợ yêu thích (Favorite Barbers)
- [x] 11.5 Hệ thống Waitlist (Chờ slot trống)
- [x] 11.6 Bugfixes & UI Polish (Real-time notifications, Graceful invalid coupon fallback, Vintage Emails HTML, AJAX Favorite toggles, Double Email events fix)

### Giai đoạn 12 — Tối ưu vận hành cho Barber (Exp P2)
- [x] 12.1 Lịch trực quan (Calendar View với FullCalendar.js)
- [x] 12.2 Quản lý ngày nghỉ đột xuất (BarberLeave) — có luồng duyệt Admin
- ~~12.3 Ghi chú khách hàng riêng (BarberNote)~~ — Bỏ
- [x] 12.4 Thống kê cá nhân hiển thị trên Dashboard Barber

### Giai đoạn 13 — Mở rộng quản trị Admin (Exp P3)
- [ ] 13.1 Quản lý tỷ lệ hoa hồng (Commission) và tự động tính toán

#### Chi tiết subtasks 13.1:

**Cách tính hoa hồng:**
- Công thức: `Hoa hồng = Giá trị booking (total_price) × Tỷ lệ hoa hồng (%) của thợ`
- Hoa hồng được tự động tính khi booking chuyển sang trạng thái **Hoàn thành** (event `BookingCompleted`)
- Mỗi booking chỉ tính hoa hồng **1 lần** (idempotent check qua `booking_id` unique)
- Tỷ lệ mặc định: 20% (đặt trong migration, admin có thể thay đổi từng thợ hoặc hàng loạt)

**Backend — đã xong:**
- [x] Migration tạo bảng `commissions` (barber_id, booking_id, booking_amount, commission_rate, commission_amount)
- [x] Migration thêm cột `commission_rate` vào bảng `barbers` (default 20%)
- [x] Model `Commission` với relationships (belongsTo Barber, Booking) + casts
- [x] Model `Barber` thêm `commission_rate` vào fillable + relationship `commissions()`
- [x] `CommissionService` — business logic:
  - `calculateForBooking()`: tự động tính, có idempotent check
  - `updateRate()` / `bulkUpdateRate()`: cập nhật tỷ lệ từng thợ hoặc hàng loạt
  - `getSummaryByBarber()` / `getHistory()` / `getMonthlyOverview()`: thống kê & báo cáo
- [x] Listener `CalculateCommissionOnCompleted` — trigger tự động khi `BookingCompleted`

**Admin UI — đã xong:**
- [x] `Admin\CommissionController` (index, updateRate AJAX, bulkUpdateRate AJAX)
- [x] Routes admin: GET commissions, PATCH rate/{barber}, PATCH bulk-rate
- [x] View `admin/commissions/index.blade.php`:
  - 4 stat cards tổng quan (tổng hoa hồng, doanh thu, số booking, tỷ lệ TB)
  - Form cập nhật hàng loạt (chọn tất cả hoặc từng thợ)
  - Bảng tỷ lệ theo thợ (inline edit AJAX với nút save)
  - Bảng lịch sử hoa hồng chi tiết (có bộ lọc barber, ngày)
  - Modal confirm đẹp (backdrop blur, animation) thay thế confirm() native
  - Toast notification cho kết quả thành công/lỗi
- [x] Menu "Hoa hồng" trên sidebar admin (icon dollar sign)

**Barber Dashboard — đã xong:**
- [x] Thêm card "Hoa hồng tháng" trên dashboard barber (tổng hoa hồng, tỷ lệ, số booking)
- [x] Query thống kê hoa hồng tháng hiện tại trong `Barber\DashboardController`

**Barber — đã xong:**
- [x] Trang xem lịch sử hoa hồng cá nhân (barber xem chi tiết từng lần được tính)
- [x] Biểu đồ hoa hồng theo thời gian (bar chart 6 tháng gần nhất)
- [x] 13.2 Quản lý chi nhánh (Multi-Branch) & gán Barber

#### Chi tiết subtasks 13.2:

**Admin — Quản lý chi nhánh:**
- [x] Trang danh sách chi nhánh: stats cards (tổng, active, barbers, doanh thu tháng)
- [x] CRUD chi nhánh (tạo/sửa/xóa) với upload ảnh
- [x] Cột doanh thu tháng per branch (JOIN barbers→bookings)
- [x] Filter theo tháng (input[type=month]) với fallback khi rỗng
- [x] Fix layout vỡ bảng chi nhánh

**Admin — Dashboard Integration:**
- [x] Section "Hiệu suất chi nhánh" trên dashboard (cards + progress bars gradient)
- [x] Hiển thị: ảnh, tên, số thợ, bookings, doanh thu + progress bar so sánh

**Admin — Sidebar tối ưu:**
- [x] Bỏ mục "Thêm" thừa trong dropdowns (Thợ cắt, Dịch vụ)
- [x] Chuyển "Dịch vụ" từ dropdown thành link trực tiếp

**Admin — Filter chi nhánh:**
- [x] Filter theo chi nhánh ở trang Danh sách thợ
- [x] Filter theo chi nhánh ở trang Lịch làm việc
- [x] Filter theo chi nhánh ở trang Booking (hiển thị thợ theo nhóm chi nhánh)

**Client UI:**
- [x] Fix dropdown chi nhánh bị mất chữ ở trang /barbers (padding/height conflict)

**Bug Fix — TimeSlotService:**
- [x] `slots:generate` giờ check `BarberLeave` approved:
  - Nghỉ full_day → skip cả ngày
  - Nghỉ partial → skip slot trùng giờ nghỉ
  - Pre-load leaves 1 lần để tránh N+1

**README:**
- [x] Thêm bước 7: Queue Worker (`php artisan queue:work`) + hướng dẫn cấu hình email
- [x] Bảng Schedule Commands (slots:generate, bookings:expire, logs:cleanup)
- [x] Lệnh `php artisan schedule:work` cho dev chạy realtime

- [/] 13.3 Quản lý sản phẩm bán kèm (Module E-commerce)

#### Chi tiết Phase 1 — Database & Models (13.3):

**Migrations — đã xong:**
- [x] Migration `create_products_table` (name, slug, description, price, image, stock_quantity, sku, category, is_active)
- [x] Migration `create_shipping_addresses_table` (user_id FK, recipient_name, phone, address, ward, district, city, lat/lng, is_default)
- [x] Migration `create_orders_table` (order_code, customer_id FK, shipping_address_id FK, subtotal, tax, shipping_fee, total_amount, status, note, cancel)
- [x] Migration `create_order_items_table` (order_id FK, product_id FK, quantity, unit_price, total_price)
- [x] Migration `create_order_payments_table` (order_id FK, amount, method, status, transaction_id, paid_at)

**Enums — đã xong:**
- [x] `ProductCategory` (HairCare, Styling, Tools, Accessories, Other) + `label()`
- [x] `OrderStatus` (Pending, Confirmed, Shipping, Delivered, Cancelled) + `label()`, `color()`, `canTransitionTo()`
- [x] `OrderPaymentMethod` (Cod, VNPay, Momo) + `label()`

**Models — đã xong:**
- [x] `Product` model (fillable, casts, scope `active()`, `byCategory()`, rel `orderItems()`)
- [x] `Order` model (fillable, casts OrderStatus, rel `customer()`, `items()`, `shippingAddress()`, `payment()`)
- [x] `OrderItem` model (fillable, casts, rel `order()`, `product()`)
- [x] `ShippingAddress` model (fillable, casts, rel `user()`, `orders()`)
- [x] `OrderPayment` model (fillable, casts OrderPaymentMethod + PaymentStatus, rel `order()`)
- [x] Cập nhật `User` model: thêm `orders()` hasMany + `shippingAddresses()` hasMany

**Chạy migrate:** ✅ Thành công (5 bảng mới đã tạo)

#### Chi tiết Phase 2 — Services & DTOs (13.3):

**DTOs — đã xong:**
- [x] `CreateProductData` (readonly class, fromRequest factory) — tạo sản phẩm
- [x] `UpdateProductData` (readonly class, fromRequest factory) — cập nhật sản phẩm
- [x] `CreateOrderData` (readonly class, fromRequest + fromArray) — đặt đơn hàng
- [x] `ShippingAddressData` (readonly class, fromRequest + fromArray) — địa chỉ giao hàng

**Services — đã xong:**
- [x] `ProductService` — CRUD + auto slug + image upload + stock management (lockForUpdate)
- [x] `ShippingService` — Haversine formula (miễn phí) + fallback Google Maps Distance Matrix + feeFromDistance (free ≤20km, base 10k + 2k/km, cap 50k)
- [x] `OrderService` — full transaction flow: validate stock → tính subtotal/tax/shipping → tạo Order+Items+Payment → giảm stock → FSM guards (confirm/ship/deliver/cancel)

**Config — đã xong:**
- [x] Thêm `shipping` config (free_within_km, base_fee, per_km_fee, max_fee, free_above, shop coordinates)

**Phase 5 — Phí vận chuyển Haversine + Nominatim (đã xong):**
- [x] `.env` + `.env.example` config: `SHOP_LATITUDE/LONGITUDE`, `SHIPPING_FREE_WITHIN_KM`, `SHIPPING_BASE_FEE`, `SHIPPING_PER_KM_FEE`, `SHIPPING_MAX_FEE`, `SHIPPING_FREE_ABOVE`
- [x] `config/services.php`: section `shipping` (shop coordinates, free_within_km, fee tiers)
- [x] `ShippingService`: `haversineDistance()` (công thức Haversine miễn phí), `getDistance()` (Haversine mặc định, Google Maps fallback), `feeFromDistance()` (free ≤20km), `calculateFee()`, `getShopCoordinates()`
- [x] `ShopController::getShippingFee()` AJAX endpoint
- [x] Geocoding dùng Nominatim (OpenStreetMap) + dropdown Tỉnh/Quận/Phường từ provinces.open-api.vn
- [x] Tài liệu `.agent/Haversine_Distance_Guide.md`
- ⚠️ **Không dùng Google Maps API** — hoàn toàn miễn phí

**Coupon System (đã xong):**
- [x] Migration thêm `applies_to` (product/shipping/booking) vào `coupons` + discount tracking vào `orders`
- [x] `CouponAppliesTo` enum, `CouponService::validate()` hỗ trợ type-aware validation
- [x] API `ShopController::applyCoupon()` (AJAX real-time) + coupon-aware `placeOrder()`
- [x] Checkout UI: 2 ô nhập mã (Sản phẩm + Ship), Alpine.js validate, discount display
- [x] Trang xem mã giảm giá `/coupons` (public): grid cards, badge loại, copy mã, HSD
- [x] Admin: dropdown "Áp dụng cho" trong form coupon + validation rules

#### Chi tiết Phase 3 — Admin CRUD sản phẩm (13.3):

**Form Requests — đã xong:**
- [x] `StoreProductRequest` (name, price, stock_quantity, sku unique, category enum, image, is_active)
- [x] `UpdateProductRequest` (tương tự Store, SKU ignore current product)

**Controller — đã xong:**
- [x] `Admin\ProductController` (index, create, store, edit, update, destroy, toggleActive)
- [x] Routes: `Route::resource('products')` + `PATCH products/{product}/toggle`

**Views — đã xong:**
- [x] `admin/products/index.blade.php` (stats cards, filter category/search, bảng SP, pagination)
- [x] `admin/products/create.blade.php` (form tạo SP: drag-drop image, category select, SKU auto, toggle active)
- [x] `admin/products/edit.blade.php` (form sửa SP: pre-fill data, hiện ảnh cũ)
- [x] Sidebar admin: thêm menu "Sản phẩm" (icon Package) giữa Dịch vụ và Thợ cắt

#### Chi tiết Phase 4 — Client: Shop, Cart, Checkout (13.3):

**Form Requests — đã xong:**
- [x] `StoreAddressRequest` (recipient_name, phone, address, ward, district, city, lat/lng, is_default)

**Controllers — đã xong:**
- [x] `Client\ShopController` (index, show, cart, addToCart, updateCart, removeFromCart, checkout, getShippingFee, placeOrder, orderSuccess, orders, orderShow, cancelOrder)
- [x] `Client\ShippingAddressController` (store, setDefault, destroy — AJAX)
- [x] Routes: shop (public), cart (session), checkout/orders/addresses (auth)

**Views — đã xong:**
- [x] `client/shop/index.blade.php` — Grid sản phẩm, filter category/search, AJAX add to cart, toast, pagination
- [x] `client/shop/show.blade.php` — Chi tiết SP, chọn SL, AJAX add, SP liên quan cùng category
- [x] `client/shop/cart.blade.php` — Bảng responsive (desktop table + mobile cards), +/- SL, xóa
- [x] `client/shop/checkout.blade.php` — 2 cột: chọn/thêm địa chỉ + order summary (VAT + phí ship AJAX) + PTTT
- [x] `client/shop/order-success.blade.php` — Checkmark SVG animation, info đơn, link chi tiết
- [x] `client/orders/index.blade.php` — Danh sách đơn hàng, status badge, tổng tiền, pagination
- [x] `client/orders/show.blade.php` — Chi tiết: SP, timeline trạng thái, breakdown, thanh toán, địa chỉ, hủy đơn
- [x] Cập nhật `layouts/client.blade.php` — Link Cửa hàng + Cart icon badge (desktop/mobile) + Footer link

**Phase 5 — Google Maps API (đã xong):**
- [x] `.env` + `.env.example` config: `GOOGLE_MAPS_API_KEY`, `SHOP_*`, `SHIPPING_*`
- [x] `config/services.php`: section `google_maps` + `shipping` (đã có từ Phase 2)
- [x] `ShippingService`: `calculateFee()`, `getDistance()` (Distance Matrix API), `feeFromDistance()`, `getShopCoordinates()` (đã có từ Phase 2)
- [x] `ShopController::getShippingFee()` AJAX endpoint (đã có từ Phase 4)
- [x] Google Places Autocomplete trên `checkout.blade.php` form thêm địa chỉ — auto-fill ward/district/city/lat/lng
- [x] Tài liệu `.agent/Google_Maps_API_Guide.md`

**Phase 6 — Thanh toán đơn hàng (đã xong):**
- [x] Tạo `PlaceOrderRequest` để validate tạo đơn.
- [x] Chuyển các logic liên quan đơn hàng từ `ShopController` sang `OrderController`.
- [x] Tạo `OrderPaymentService` cho VNPay, MoMo redirect và callback.
- [x] Tạo `OrderPaymentController` để xử lý các callback thanh toán.
- [x] Cập nhật `routes/web.php`.

**Phase 7–8:** Chưa bắt đầu
- [ ] 13.4 Audit Log nâng cao (Ghi lại mọi thay đổi Model)
- [ ] 13.5 Dashboard Analytics nâng cao (Heatmap, So sánh kỳ)

---

## Hướng dẫn cập nhật file này

Khi hoàn thành 1 bước, đổi `- [ ]` thành `- [x]` và cập nhật phần "Trạng thái hiện tại":

```
Giai đoạn đang làm : Hoàn thành
Bước đang làm      : Hoàn thành toàn bộ dự án
Cập nhật lần cuối  : 23/03/2026
```

- **24/03/2026**: 13.3 Phase 1 hoàn thành — Tạo 5 migrations (`products`, `shipping_addresses`, `orders`, `order_items`, `order_payments`), 3 enums (`ProductCategory`, `OrderStatus`, `OrderPaymentMethod`), 5 models (`Product`, `Order`, `OrderItem`, `ShippingAddress`, `OrderPayment`). Cập nhật `User` model thêm 2 relationships (`orders()`, `shippingAddresses()`). `OrderPayment` reuse `PaymentStatus` enum từ booking, dùng `OrderPaymentMethod` riêng (thêm COD). `OrderStatus` có FSM guard `canTransitionTo()` giống `BookingStatus`.

Khi hoàn thành cả giai đoạn, đổi `⬜ Chưa bắt đầu` thành `✅ Hoàn thành` trong bảng tổng quan.

- **24/03/2026**: 13.3 Phase 3 hoàn thành — Admin CRUD Sản phẩm: (1) Tạo `StoreProductRequest` + `UpdateProductRequest` với validation đầy đủ (name, price, stock, SKU unique, category enum, image max 2MB); (2) Tạo `Admin\ProductController` (index/create/store/edit/update/destroy/toggleActive) dùng ProductService + DTOs theo pattern chuẩn; (3) Routes resource + PATCH toggle; (4) View `index.blade.php` có 3 stats cards (tổng SP, đang bán, hết hàng), filter bar (category + search), bảng đầy đủ (ảnh, tên, SKU, giá, tồn kho warning, danh mục, status, actions toggle/edit/delete); (5) View `create.blade.php` + `edit.blade.php` với form đầy đủ (name, description, price, stock, SKU auto-generate, category select, image drag-drop preview, is_active toggle); (6) Sidebar admin thêm menu "Sản phẩm" (icon Package 3D box) giữa Dịch vụ và Thợ cắt.

- **24/03/2026**: 13.3 Phase 4 hoàn thành — Client: Shop, Cart, Checkout: (1) `StoreAddressRequest` validation; (2) `ShopController` 13 methods (shop listing, product detail, session cart AJAX, checkout, phí ship AJAX via ShippingService, đặt hàng COD via OrderService, order history, cancel); (3) `ShippingAddressController` 3 methods AJAX (store/setDefault/destroy); (4) Routes: shop+cart public, checkout+orders+addresses auth; (5) 7 views Blade vintage: shop index (grid, filter, toast), show (chi tiết, SL, SP liên quan), cart (responsive table/cards), checkout (2 cột Alpine.js), order-success (SVG animation), orders index/show (timeline, breakdown, hủy đơn); (6) Nav client thêm link Cửa hàng + Cart badge (desktop/mobile) + footer.

- **25/03/2026**: 13.3 Phase 5 đã chuyển sang Haversine + Nominatim — Không dùng Google Maps API nữa. (1) `ShippingService::haversineDistance()` tính khoảng cách đường chim bay bằng công thức Haversine (miễn phí hoàn toàn); (2) `getDistance()` dùng Haversine mặc định, Google Maps là fallback optional; (3) `feeFromDistance()` sửa lại: ≤20km miễn phí, >20km tính base 10k + 2k/km, cap 50k; (4) Geocoding dùng Nominatim (OpenStreetMap) + dropdown Tỉnh/Quận/Phường từ provinces.open-api.vn; (5) Viết tài liệu `.agent/Haversine_Distance_Guide.md`.

- **25/03/2026**: Coupon System cho Checkout — (1) Migration thêm `applies_to` (product/shipping/booking) vào `coupons` + discount tracking columns vào `orders`; (2) `CouponAppliesTo` enum + `CouponService::validate()` type-aware; (3) API `ShopController::applyCoupon()` AJAX + coupon-aware `placeOrder()`; (4) Checkout UI 2 ô mã (Sản phẩm + Ship) với Alpine.js; (5) Trang `/coupons` (public) hiển danh sách mã giảm giá + link trên header nav; (6) Admin form thêm dropdown "Áp dụng cho" + validation.

- **25/03/2026**: 13.3 Phase 6 hoàn thành — Thanh toán đơn hàng: (1) Tạo `PlaceOrderRequest` validate giỏ hàng bằng hook; (2) Tách method `placeOrder`, `orders`, `orderShow`, `cancelOrder`, `orderSuccess` từ `ShopController` ra `OrderController`; (3) Tạo `OrderPaymentService` kế thừa logic thanh toán VNPay Sandbox, MoMo Sandbox nhưng sửa dụng cho model `OrderPayment`; (4) Xử lý URL callback qua `OrderPaymentController` để redirect trạng thái trả về thành công/thất bại cho Client.

