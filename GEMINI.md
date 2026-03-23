# BarberBook — Project Rules

## Trước khi làm task
- Đọc `.agent/04-current-progress.md` để biết đang ở giai đoạn nào.
- Đọc phần tương ứng trong `.agent/02-developer-plan.md` để hiểu yêu cầu.
- Đọc `.agent/03-Conventions.md` để viết đúng style.

## Quy tắc code
- Mọi business logic PHẢI nằm trong `app/Services/`, Controller chỉ gọi Service.
- Dùng PHP Enums (`BookingStatus`, `TimeSlotStatus`, `UserRole`), KHÔNG dùng string literals.
- Tạo booking bắt buộc dùng `DB::transaction()` + `lockForUpdate()`.
- Blade views dùng `@use()` để import Enum ở đầu file.
- Không dùng `RouteServiceProvider`, `EventServiceProvider`, `Console/Kernel` — Laravel 12 đã xoá.

## Quy tắc giao diện
- Auth pages (login, register...) dùng `layouts.client`, KHÔNG dùng `layouts.guest`.
- Error pages (403, 404...) là standalone HTML vintage, KHÔNG dùng layout.
- Giao diện client theo phong cách Editorial Vintage (Playfair Display + Epilogue).

## Sau khi hoàn thành task
- Cập nhật `.agent/04-current-progress.md` — đánh dấu bước đã xong, cập nhật trạng thái.
