# ✨ Tính năng chi tiết — Classic Cut

## 4 vai trò người dùng

### 👤 Khách vãng lai (Guest)
- Đặt lịch **không cần đăng ký** — chỉ cần Tên, SĐT, Email
- Hệ thống tự tạo tài khoản ngầm (`findOrCreateGuest`) với mật khẩu random
- Có thể đăng nhập sau bằng email + "Quên mật khẩu" để đặt lại

### 👨‍💼 Khách hàng đã đăng ký (Client)
- Booking Wizard 4 bước: Chọn dịch vụ → Chọn thợ → Chọn giờ → Xác nhận
- Thanh toán: Tiền mặt / VNPay / MoMo (Sandbox)
- Huỷ lịch (trước ≥2 giờ), kèm lý do
- Đánh giá dịch vụ (1-5 sao + comment) sau khi hoàn thành
- Quản lý hồ sơ cá nhân, upload avatar
- Xem lịch sử booking trong trang Profile

### ✂️ Thợ cắt tóc (Barber)
- Dashboard hôm nay: stats cards + danh sách booking
- Trang booking theo tuần: chuyển tuần, grouped by day
- Xác nhận / Từ chối / Bắt đầu / Hoàn thành booking
- Đăng ký lịch làm việc 7 ngày/tuần (giờ bắt đầu + kết thúc + ngày nghỉ)
- TimeSlot tự động sinh khi lưu schedule (mỗi slot 30 phút)
- Nhận notification khi có booking mới / bị huỷ

### 🛡 Quản trị viên (Admin)
- Dashboard tổng quan: 5 stats cards, biểu đồ doanh thu 7 ngày, top 3 thợ, 5 booking mới
- Quản lý dịch vụ (CRUD + upload ảnh)
- Quản lý thợ cắt (CRUD + gán user + xem/sửa schedule)
- Quản lý người dùng (danh sách, lọc role, tìm kiếm, bật/tắt tài khoản)
- Quản lý booking toàn hệ thống (theo thợ + theo tuần)
- Báo cáo chi tiết:
  - Biểu đồ doanh thu (30 ngày / theo tháng / theo năm) — Chart.js
  - Top thợ cắt theo doanh thu tháng
  - Top dịch vụ theo số lần đặt tháng
- Nhận notification khi booking bị huỷ

---

## Thanh toán (Payment)

| Phương thức | Loại | Mô tả |
|-------------|------|-------|
| Tiền mặt | — | Đánh dấu "Đã thanh toán" thủ công |
| VNPay | Sandbox | Redirect → VNPay → Return URL + IPN callback |
| MoMo | Sandbox | Redirect → MoMo → Return URL |

**Bảo mật thanh toán:**
- Idempotency check — chống xử lý callback trùng lặp
- HMAC Signature Verify — chống giả mạo webhook
- VNPay IPN — server-to-server đảm bảo cập nhật
- `PaymentStatus::Failed` — tracking giao dịch thất bại

---

## Tự động hoá

| Tính năng | Cơ chế | Lệnh |
|-----------|--------|------|
| Sinh TimeSlot hàng ngày | Artisan Command + Cronjob | `php artisan slots:generate` |
| Sinh slot khi sửa schedule | Trigger trong ScheduleService | Tự động |
| Notification khi booking thay đổi | Event/Listener pattern | Tự động |
| Cache barbers/services listing | CacheService (TTL-based) | Tự động |

---

## Thiết kế giao diện

- **Phong cách:** Classic/Vintage Barbershop
- **Màu sắc:** Cream (`--v-cream`), Copper (`--v-copper`), Ink (`--v-ink`)
- **Typography:** Playfair Display (serif headings) + Inter (sans body)
- **Responsive:** Mobile-first, 3 layouts riêng biệt (Client, Barber, Admin)
- **Trang lỗi:** 6 trang (403, 404, 419, 429, 500, 503) với vintage standalone design
