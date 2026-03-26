# 🔗 API Routes — Classic Cut

## Tổng quan Routes

Hệ thống chia routes theo 3 file, phân quyền bằng `RoleMiddleware`:

| File | Prefix | Middleware | Vai trò |
|------|--------|-----------|---------|
| `web.php` | `/` | — | Client + Guest |
| `admin.php` | `/admin` | `auth`, `role:admin` | Admin |
| `barber.php` | `/barber` | `auth`, `role:barber,admin` | Barber + Admin |
| `auth.php` | `/` | `guest` / `auth` | Đăng ký, Đăng nhập, Quên mật khẩu |

---

## Client Routes (`web.php`)

### Public (Không cần đăng nhập)

| Method | URI | Controller | Mô tả |
|--------|-----|-----------|-------|
| GET | `/` | Closure | Trang chủ |
| GET | `/barbers` | BarberController@index | Danh sách thợ |
| GET | `/barbers/{barber}` | BarberController@show | Chi tiết thợ + reviews |
| GET | `/booking/create` | BookingController@create | Form đặt lịch (wizard) |
| POST | `/booking` | BookingController@store | Tạo booking ⚡ `throttle:5,1` |
| GET | `/booking/{booking}/confirmation` | BookingController@confirmation | Trang xác nhận |
| GET | `/booking/slots` | BookingController@getSlots | API lấy slots khả dụng |

### Payment

| Method | URI | Controller | Mô tả |
|--------|-----|-----------|-------|
| GET | `/payment/{booking}` | PaymentController@show | Chọn phương thức |
| POST | `/payment/{booking}` | PaymentController@process | Xử lý thanh toán ⚡ `throttle:5,1` |
| GET | `/payment/vnpay/return` | PaymentController@vnpayReturn | VNPay redirect callback |
| POST | `/payment/vnpay/ipn` | PaymentController@vnpayIPN | VNPay IPN (server-to-server) 🔓 No CSRF |
| GET | `/payment/momo/return` | PaymentController@momoReturn | MoMo redirect callback |

### Authenticated Client

| Method | URI | Controller | Middleware | Mô tả |
|--------|-----|-----------|-----------|-------|
| GET | `/profile` | ProfileController@show | `auth` | Hồ sơ + lịch sử booking + đơn hàng |
| GET | `/profile/edit` | ProfileController@edit | `auth` | Form sửa hồ sơ |
| PUT | `/profile` | ProfileController@update | `auth` | Cập nhật hồ sơ + avatar |
| PATCH | `/booking/{booking}/cancel` | BookingController@cancel | `auth` | Huỷ booking (≥2h trước) |
| POST | `/reviews` | ReviewController@store | `auth` | Gửi đánh giá dịch vụ/thợ |

### E-commerce & Cart

| Method | URI | Controller | Mô tả |
|--------|-----|-----------|-------|
| GET | `/shop` | ShopController@index | Danh sách sản phẩm |
| GET | `/shop/{product}` | ShopController@show | Chi tiết sản phẩm |
| GET | `/cart` | CartController@index | Xem giỏ hàng |
| POST | `/cart/add` | CartController@add | Thêm vào giỏ (*Auth Modal*) |
| POST | `/cart/update` | CartController@update | Cập nhật số lượng |
| POST | `/cart/remove` | CartController@remove | Xoá khỏi giỏ |
| POST | `/cart/apply-coupon` | CartController@applyCoupon | Áp dụng mã giảm giá |
| GET | `/checkout` | CheckoutController@index | Trang Checkout (`auth`) |
| POST | `/checkout` | CheckoutController@process | Xử lý đặt hàng (`auth`) |
| GET | `/order-success/{order}` | CheckoutController@success | Đặt hàng thành công (`auth`) |


---

## Admin Routes (`admin.php`)

Tất cả đều yêu cầu `auth` + `role:admin`.

### Dashboard & Reports

| Method | URI | Controller | Mô tả |
|--------|-----|-----------|-------|
| GET | `/admin/dashboard` | DashboardController@index | Dashboard tổng quan |
| GET | `/admin/reports` | ReportController@index | Trang báo cáo |
| GET | `/admin/reports/chart-data` | ReportController@chartData | API dữ liệu biểu đồ (AJAX) |

### CRUD Resources

| Resource | URI Prefix | Controller | Actions |
|----------|-----------|-----------|---------|
| Services | `/admin/services` | ServiceController | index, create, store, edit, update, destroy |
| Barbers | `/admin/barbers` | BarberController | index, create, store, edit, update, destroy |
| Users | `/admin/users` | UserController | index, show, edit, update |
| Bookings | `/admin/bookings` | BookingController | index |
| Products | `/admin/products` | ProductController | index, create, store, edit, update, destroy |
| Orders | `/admin/orders` | OrderController | index, show, updateStatus |
| Coupons | `/admin/coupons` | CouponController | index, create, store, edit, update, destroy |


| Method | URI | Mô tả |
|--------|-----|-------|
| PATCH | `/admin/users/{user}/toggle-active` | Bật/tắt tài khoản |
| GET | `/admin/barbers/{barber}/schedule` | Xem lịch làm việc |
| POST | `/admin/barbers/{barber}/schedule` | Cập nhật lịch |

---

## Barber Routes (`barber.php`)

Tất cả yêu cầu `auth` + `role:barber,admin`.

| Method | URI | Controller | Mô tả |
|--------|-----|-----------|-------|
| GET | `/barber/dashboard` | DashboardController@index | Dashboard hôm nay |
| GET | `/barber/bookings` | BookingController@index | Booking theo tuần |
| PATCH | `/barber/bookings/{booking}/confirm` | BookingController@confirm | Xác nhận |
| PATCH | `/barber/bookings/{booking}/reject` | BookingController@reject | Từ chối |
| PATCH | `/barber/bookings/{booking}/start` | BookingController@start | Bắt đầu phục vụ |
| PATCH | `/barber/bookings/{booking}/complete` | BookingController@complete | Hoàn thành |
| GET | `/barber/schedule` | ScheduleController@edit | Xem lịch làm việc |
| POST | `/barber/schedule` | ScheduleController@update | Cập nhật lịch |

---

## Auth Routes (`auth.php`)

Routes xác thực chuẩn Laravel Breeze, include từ `web.php`.

### Guest (Chưa đăng nhập)

| Method | URI | Controller | Mô tả |
|--------|-----|-----------|-------|
| GET | `/register` | RegisteredUserController@create | Form đăng ký |
| POST | `/register` | RegisteredUserController@store | Xử lý đăng ký |
| GET | `/login` | AuthenticatedSessionController@create | Form đăng nhập |
| POST | `/login` | AuthenticatedSessionController@store | Xử lý đăng nhập |
| GET | `/forgot-password` | PasswordResetLinkController@create | Form quên mật khẩu |
| POST | `/forgot-password` | PasswordResetLinkController@store | Gửi email reset |
| GET | `/reset-password/{token}` | NewPasswordController@create | Form đặt lại mật khẩu |
| POST | `/reset-password` | NewPasswordController@store | Xử lý đặt lại |

### Authenticated (Đã đăng nhập)

| Method | URI | Controller | Mô tả |
|--------|-----|-----------|-------|
| PUT | `/password` | PasswordController@update | Đổi mật khẩu |
| POST | `/logout` | AuthenticatedSessionController@destroy | Đăng xuất |

---

## Ký hiệu

- ⚡ Rate limited (`throttle:5,1` — tối đa 5 request/phút)
- 🔓 No CSRF (server-to-server callback)
