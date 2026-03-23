---
description: Quy trình thêm tính năng mới vào BarberBook theo đúng architecture
---

## Thêm tính năng mới

1. Đọc `.agent/04-current-progress.md` để biết giai đoạn hiện tại.
2. Đọc phần tương ứng trong `.agent/02-developer-plan.md` để hiểu yêu cầu chi tiết.
3. Đọc `.agent/03-Conventions.md` để viết đúng style.
4. Nếu cần thêm bảng/cột → tạo Migration.
5. Tạo/cập nhật Model (relationships, casts, fillable).
6. Tạo Service class trong `app/Services/` cho business logic.
7. Tạo FormRequest trong `app/Http/Requests/` cho validation.
8. Tạo Controller, inject Service qua constructor. Controller chỉ gọi Service, không có logic.
9. Thêm Routes vào file tương ứng:
   - Client routes → `routes/web.php`
   - Admin routes → `routes/admin.php`
   - Barber routes → `routes/barber.php`
10. Tạo Blade views theo đúng thư mục:
    - Client: `resources/views/client/`
    - Admin: `resources/views/admin/`
    - Barber: `resources/views/barber/`
11. Test thủ công trên trình duyệt, kiểm tra các luồng chính.
12. Cập nhật `.agent/04-current-progress.md` — đánh dấu bước hoàn thành + thêm ghi chú nếu có thay đổi so với kế hoạch.
