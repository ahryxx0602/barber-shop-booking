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
- Xem lịch sử booking và đơn hàng E-commerce trong trang Profile (giao diện tab bằng Alpine.js)
- **Cửa hàng E-commerce:** Mua sắm sản phẩm chăm sóc tóc, thêm vào giỏ hàng, áp dụng mã giảm giá, checkout.
- **Bảo mật:** Auth Modal kích hoạt khi cố gắng dùng tính năng E-commerce lúc chưa đăng nhập.
- **Khách hàng thân thiết:** Tích lũy Loyalty Points (nếu có).

### ✂️ Thợ cắt tóc (Barber)
- Dashboard hôm nay: stats cards + danh sách booking
- Trang booking theo tuần: chuyển tuần, grouped by day
- Xác nhận / Từ chối / Bắt đầu / Hoàn thành booking
- Đăng ký lịch làm việc 7 ngày/tuần (giờ bắt đầu + kết thúc + ngày nghỉ), quản lý xin nghỉ (BarberLeave)
- TimeSlot tự động sinh khi lưu schedule (mỗi slot 30 phút)
- Thống kê hoa hồng (Commissions) dựa trên dịch vụ đã phục vụ.
- Nhận notification khi có booking mới / bị huỷ

### 🛡 Quản trị viên (Admin)
- Dashboard tổng quan: 5 stats cards, biểu đồ doanh thu 7 ngày, top 3 thợ, 5 booking mới
- **Advanced Dashboard Reporting:** Bản đồ nhiệt (Heatmaps) với ApexCharts phân tích mật độ đặt lịch và doanh thu bán hàng.
- Quản lý E-commerce: Quản lý sản phẩm (kho hàng, giá cả), Đơn hàng (cập nhật trạng thái), Mã giảm giá (Coupons).
- Quản lý dịch vụ (CRUD + upload ảnh)
- Quản lý thợ cắt (CRUD + gán user + xem/sửa schedule + quản lý Hoa hồng)
- Quản lý người dùng (danh sách, lọc role, tìm kiếm, bật/tắt tài khoản)
- Quản lý booking toàn hệ thống (theo thợ + theo tuần)
- Báo cáo chi tiết:
  - Biểu đồ doanh thu (30 ngày / theo tháng / theo năm) — Chart.js
  - Top thợ cắt theo doanh thu tháng
  - Top dịch vụ theo số lần đặt tháng
- Nhận notification khi booking bị huỷ

---

## 🛒 Cửa hàng E-commerce

Hệ thống bán lẻ các sản phẩm chăm sóc tóc (Pomade, Dầu gội,...) với đầy đủ tính năng:
- **Giỏ hàng & Đơn hàng:** Thêm/bớt sản phẩm, quản lý tồn kho (Pessimistic Locking ngăn chặn race condition).
- **Mã giảm giá (Coupons):** Giảm theo phần trăm hoặc số tiền cố định, giới hạn lượt dùng.
- **Tính phí vận chuyển:** Tự động tính phí ship dựa trên khoảng cách từ chi nhánh Barbershop đến địa chỉ khách hàng qua thuật toán **Haversine Distance**.
- **Auth Modal Protection:** Giao diện mua sắm bảo vệ các thao tác quan trọng bằng popup bắt buộc đăng nhập.


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
