---
description: Reset database và seed lại dữ liệu demo cho BarberBook
---

## Reset DB + Seed data

// turbo-all

1. Chạy migrate fresh với seed:
```bash
cd /home/vanthanh/Ind-Project/Laravel/barbershop && php artisan migrate:fresh --seed
```

2. Generate time slots cho 7 ngày tới:
```bash
cd /home/vanthanh/Ind-Project/Laravel/barbershop && php artisan slots:generate
```

3. Xác nhận data đã được tạo, thông báo cho user:
   - Số lượng users, barbers, services đã seed.
   - Số time slots đã generate.
