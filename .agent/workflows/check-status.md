---
description: Kiểm tra nhanh trạng thái dự án BarberBook (routes, migrations, errors)
---

## Kiểm tra trạng thái dự án

// turbo-all

1. Kiểm tra trạng thái migration:
```bash
cd /home/vanthanh/Ind-Project/Laravel/barbershop && php artisan migrate:status
```

2. Kiểm tra danh sách routes:
```bash
cd /home/vanthanh/Ind-Project/Laravel/barbershop && php artisan route:list --compact
```

3. Đọc file `.agent/04-current-progress.md` để xem tiến độ.

4. Tóm tắt cho user:
   - Giai đoạn hiện tại đang làm.
   - Có pending migration nào không.
   - Tổng số routes đã đăng ký.
