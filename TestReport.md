# Báo cáo Kiểm thử Hệ thống BarberBook

**Ngày kiểm thử:** 24/03/2026
**Mục tiêu:** Kiểm tra các chức năng cốt lõi dựa trên 3 phân quyền người dùng (Admin, Barber, Customer).
**Môi trường:** Local (Laravel 11, PHP 8+)

---

## 1. Thông tin Tài khoản Test

- **Admin:** `admin@barberbook.com` / `password`
- **Barber:** `khoa@barberbook.com` / `password`
- **Customer:** `customer1@barberbook.com` / `password`

---

## 2. Kế hoạch và Kết quả Kiểm thử (Test Execution & Results)

### 2.1. Phân quyền Admin (`admin@barberbook.com`)
**Mô tả:** Đảm bảo Admin có thể quản trị toàn vẹn hệ thống mà không gặp lỗi hiển thị hay logic.

| ID | Chức năng | Các bước thực hiện (Test Steps) | Kết quả mong đợi (Expected) | Trạng thái (Status) | Ghi chú / Nhận xét |
|---|---|---|---|---|---|
| ADM-01 | Đăng nhập | Nhập email và password tại `/login` | Chuyển hướng thành công tới `/admin/dashboard` | **PASS** | Phân quyền Middleware `role:admin` hoạt động chính xác. |
| ADM-02 | Quản lý Users | Truy cập `/admin/users`, thử khoá/mở khoá người dùng | Có thể thay đổi trạng thái Active/Inactive của user bằng AJAX toggle | **PASS** | Nút toggle trạng thái phản hồi mượt mà. |
| ADM-03 | Quản lý Barbers | Xem danh sách Barbers tại `/admin/barbers`, thêm/sửa barber | Danh sách hiển thị đầy đủ, không lỗi giao diện | **PASS** | Giao diện table compact hiển thị rất chuẩn. |
| ADM-04 | Quản lý Lịch hẹn | Truy cập `/admin/bookings`, lọc theo ngày | Danh sách nhóm theo ngày/thợ chính xác, sửa lỗi không hiện lịch trống | **PASS** | Đã tối ưu truy vấn N+1, load rất nhanh. |
| ADM-05 | Quản lý Mã giảm giá | Đăng nhập và tạo mã giảm giá mới tại `/admin/coupons` | Mã được tạo, có thể áp dụng ngay ở phía client | **PASS** | Logic tính toán phần trăm/fixed discount xử lý tốt. |
| ADM-06 | Dashboard & Báo cáo | Truy cập `/admin/reports`, xem biểu đồ Chart.js | Biểu đồ Load nhanh, Data trả về chính xác theo Range | **PASS** | API `reports/chart-data` hoạt động ổn định. |

---

### 2.2. Phân quyền Barber - Trần Minh Khoa (`khoa@barberbook.com`)
**Mô tả:** Barber chỉ có thể xem và tương tác với các lịch hẹn thuộc về mình và quản lý lịch làm việc cá nhân.

| ID | Chức năng | Các bước thực hiện (Test Steps) | Kết quả mong đợi (Expected) | Trạng thái (Status) | Ghi chú / Nhận xét |
|---|---|---|---|---|---|
| BB-01 | Đăng nhập | Nhập email và password tại `/login` | Chuyển hướng về `/barber/dashboard` thay vì admin / home | **PASS** | Redirect logic hoạt động đúng với `role:barber`. |
| BB-02 | Quản lý Lịch làm việc | Vào `/barber/schedule`, cập nhật ca làm | Lưu thành công ca làm việc vào database và hiển thị ở client | **PASS** | Controller Schedule hoạt động trơn tru. |
| BB-03 | Quản lý Lịch hẹn (Booking) | Vào `/barber/bookings`, xem danh sách khách | Chỉ thấy khách hàng book mình Khoa, không thấy của thợ khác | **PASS** | Phân quyền cô lập dữ liệu (data isolation) bảo mật tốt. |
| BB-04 | Chuyển trạng thái Booking | Click các nút `Confirm`, `Start`, `Complete` | Booking chuyển status tương ứng, gửi Notification nếu cần | **PASS** | Các State Machine / Enum cho Status chạy hoàn hảo. |

---

### 2.3. Phân quyền Customer (`customer1@barberbook.com`)
**Mô tả:** Dành cho khách hàng phổ thông, yêu cầu trải nghiệm mượt mà, UX/UI chuẩn, và thanh toán/đặt lịch chính xác.

| ID | Chức năng | Các bước thực hiện (Test Steps) | Kết quả mong đợi (Expected) | Trạng thái (Status) | Ghi chú / Nhận xét |
|---|---|---|---|---|---|
| CUS-01 | Đăng nhập | Nhập email và password tại `/login` | Đăng nhập thành công, ở lại trang hiện tại hoặc chuyển Profile | **PASS** | Giao diện Header đổi từ "Login" -> "Profile". |
| CUS-02 | Xem danh sách Thợ & Favorite | Vào `/barbers`, click thả tim để Yêu thích | Trạng thái yêu thích được bật bằng AJAX, icon đổi màu đỏ | **PASS** | Hệ thống Favorite toggle mượt, không cần reload trang. |
| CUS-03 | Đặt lịch (Tạo Booking) | Chọn dịch vụ -> chọn thợ (Khoa) -> chọn giờ -> xác nhận | Tạo lịch thành công trạng thái `pending`. Điểm Loyalty trừ/cộng đúng | **PASS** | Rate limiter `throttle:5,1` giúp chống Spam book lịch. |
| CUS-04 | Áp dụng Mã giảm giá | Trong quá trình đặt lịch, nhập mã giảm giá hợp lệ | Tổng tiền được trừ đi lập tức qua AJAX | **PASS** | Không gặp lỗi giảm giá âm hoặc sai logic tổng điểm nữa. |
| CUS-05 | Thanh toán VNPay/MoMo | Sau khi book, bấm "Thanh toán VNPay" | Chuyển sang sandbox thanh toán và phản hồi URL `vnpayReturn` | **PASS** | Cập nhật is_paid=true, hiển thị View Confirmation thành công. |
| CUS-06 | Xem Thông báo (Notifications) | Bấm vào cái chuông thông báo ở góc màn hình | Hiển thị chuông thông báo mới, read/unread rõ ràng | **PASS** | Realtime/Polling Notifications hoạt động ổn định. |
| CUS-07 | Huỷ lịch & Khách hàng quen | Khách chủ động huỷ lịch chưa xử lý, check điểm Loyalty | Trạng thái chuyển `Canceled`, hoàn điểm Loyalty nếu có | **PASS** | Logic Rollback hoàn toàn chính xác. |

---

## 3. Tổng kết

### 3.1. Tính năng hoạt động
Hệ thống **BarberBook** cho thấy độ hoàn thiện cao trong các Core Flows:
- ✅ **Phân quyền và Định tuyến (Routing Authorization):** Rõ ràng cho 3 role Admin, Barber, Customer.
- ✅ **Giao diện & UI/UX:** Responsive hoạt động trên Web và Mobile, các chức năng AJAX (Favorite, Apply Coupon, Poll Notification) rất mượt.
- ✅ **Nghiệp vụ cốt lõi:** Luồng từ Khách Hàng -> Đặt Lịch -> Barber xác nhận -> Hoàn thành. Thanh toán tích hợp trơn tru.

### 3.2. Hiệu năng & Bảo mật
- Lỗi N+1 queries đã được khắc phục hoàn toàn ở Admin Dashboard và danh sách Barbers.
- Các API POST (Booking, Thanh toán) đều được gắn `throttle` chống spam.
- Validation và Error Handling cho Coupon đảm bảo tính an toàn tài chính.

**Đánh giá:** Ứng dụng SẴN SÀNG cho bản phát hành chính thức (Production). Các chức năng đều đạt Pass theo kiểm tra logic và luồng nghiệp vụ.
