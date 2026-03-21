# Fix List - Phase 1 & 2 Review

## 🔴 Critical

### 1. `role` trong `$fillable` — Mass Assignment Risk
**File:** `app/Models/User.php:26`

Bỏ `role` khỏi `$fillable` để tránh user tự assign role qua request.

```php
// Xóa 'role' khỏi $fillable
protected $fillable = ['name', 'email', 'password', 'phone', 'avatar'];
```

---

### 2. Register không set role mặc định
**File:** `app/Http/Controllers/Auth/RegisteredUserController.php:39`

Sau khi `User::create()`, role là `null` → dashboard redirect và RoleMiddleware bị lỗi.

**Option A:** Set sau khi tạo user
```php
$user = User::create([...]);
$user->role = 'customer';
$user->save();
```

**Option B:** Thêm default trong migration
```php
$table->string('role')->default('customer');
```

---

## 🟡 Medium

### 3. `MustVerifyEmail` bị comment out
**File:** `app/Models/User.php:5`

Route dashboard dùng middleware `verified` nhưng User không implement interface → middleware không hoạt động đúng.

```php
// Bỏ comment:
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
```

Hoặc nếu không cần email verification thì bỏ `verified` khỏi các route middleware.

---

### 4. Null check trong `RoleMiddleware`
**File:** `app/Http/Middleware/RoleMiddleware.php:13`

`auth()->user()` có thể null trong một số edge case → `TypeError`.

```php
if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
    abort(403);
}
```

---

### 5. Thiếu `phone` validation khi register
**File:** `app/Http/Controllers/Auth/RegisteredUserController.php`

`phone` có trong `$fillable` nhưng không được validate hay gán khi register. Quyết định: bỏ khỏi register flow hoặc thêm validation optional.

---

## ✅ Không cần sửa

- Rate limiting login: OK
- Session regenerate sau login: OK
- Password hashed qua cast: OK
- Role-based routing sau login: OK
- Middleware `role:admin` bảo vệ đúng route: OK

# Đã fix | PHASE 1,2

