<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────
        // Admin (giữ nguyên, không thay đổi)
        // ──────────────────────────────────────
        User::create([
            'name'       => 'Admin',
            'email'      => 'admin@barberbook.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'phone'      => '0900000001',
            'is_active'  => true,
            'created_at' => Carbon::parse('2025-01-01 08:00:00'),
            'updated_at' => Carbon::parse('2025-01-01 08:00:00'),
        ]);

        // ──────────────────────────────────────
        // Barbers — 5 thợ cắt, ngày tạo rải rác
        // ──────────────────────────────────────
        $barbers = [
            ['name' => 'Nguyễn Văn Hùng',  'email' => 'hung@barberbook.com',  'phone' => '0900000002', 'date' => '2025-01-15'],
            ['name' => 'Trần Minh Khoa',   'email' => 'khoa@barberbook.com',  'phone' => '0900000003', 'date' => '2025-02-10'],
            ['name' => 'Lê Quốc Bảo',     'email' => 'bao@barberbook.com',   'phone' => '0900000004', 'date' => '2025-03-05'],
            ['name' => 'Phạm Hoàng Dũng', 'email' => 'dung@barberbook.com',  'phone' => '0900000005', 'date' => '2025-06-20'],
            ['name' => 'Đỗ Thanh Tùng',   'email' => 'tung@barberbook.com',  'phone' => '0900000006', 'date' => '2025-09-01'],
        ];

        foreach ($barbers as $barber) {
            $date = Carbon::parse($barber['date'] . ' 09:00:00');
            User::create([
                'name'       => $barber['name'],
                'email'      => $barber['email'],
                'password'   => Hash::make('password'),
                'role'       => 'barber',
                'phone'      => $barber['phone'],
                'is_active'  => true,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        // ──────────────────────────────────────
        // Customers — 50 khách hàng, rải từ 01/2025 đến 03/2026
        // ──────────────────────────────────────
        $customers = [
            // ── 2025 Tháng 1 ──
            ['name' => 'Nguyễn Thị Mai',      'phone' => '0911000001', 'date' => '2025-01-03'],
            ['name' => 'Trần Văn Đức',        'phone' => '0911000002', 'date' => '2025-01-10'],
            ['name' => 'Lê Hoàng Nam',        'phone' => '0911000003', 'date' => '2025-01-18'],
            ['name' => 'Phạm Minh Tuấn',      'phone' => '0911000004', 'date' => '2025-01-27'],
            // ── 2025 Tháng 2 ──
            ['name' => 'Hoàng Thị Lan',       'phone' => '0911000005', 'date' => '2025-02-05'],
            ['name' => 'Vũ Đình Khôi',        'phone' => '0911000006', 'date' => '2025-02-14'],
            ['name' => 'Đặng Quốc Việt',      'phone' => '0911000007', 'date' => '2025-02-22'],
            // ── 2025 Tháng 3 ──
            ['name' => 'Bùi Thị Hương',       'phone' => '0911000008', 'date' => '2025-03-02'],
            ['name' => 'Ngô Minh Quân',       'phone' => '0911000009', 'date' => '2025-03-12'],
            ['name' => 'Đinh Văn Long',       'phone' => '0911000010', 'date' => '2025-03-25'],
            // ── 2025 Tháng 4 ──
            ['name' => 'Trương Thị Ngọc',     'phone' => '0911000011', 'date' => '2025-04-04'],
            ['name' => 'Lý Hoàng Phúc',       'phone' => '0911000012', 'date' => '2025-04-15'],
            ['name' => 'Phan Văn Tài',        'phone' => '0911000013', 'date' => '2025-04-28'],
            // ── 2025 Tháng 5 ──
            ['name' => 'Dương Thị Thu',       'phone' => '0911000014', 'date' => '2025-05-06'],
            ['name' => 'Hồ Minh Trí',        'phone' => '0911000015', 'date' => '2025-05-17'],
            ['name' => 'Tô Văn Hải',         'phone' => '0911000016', 'date' => '2025-05-29'],
            // ── 2025 Tháng 6 ──
            ['name' => 'Mai Thị Hồng',       'phone' => '0911000017', 'date' => '2025-06-03'],
            ['name' => 'Cao Đức Anh',        'phone' => '0911000018', 'date' => '2025-06-14'],
            ['name' => 'Thái Văn Sơn',       'phone' => '0911000019', 'date' => '2025-06-25'],
            // ── 2025 Tháng 7 ──
            ['name' => 'Châu Minh Hiếu',     'phone' => '0911000020', 'date' => '2025-07-02'],
            ['name' => 'La Thị Yến',         'phone' => '0911000021', 'date' => '2025-07-11'],
            ['name' => 'Kiều Văn Phong',     'phone' => '0911000022', 'date' => '2025-07-21'],
            ['name' => 'Trịnh Thị Tuyết',    'phone' => '0911000023', 'date' => '2025-07-30'],
            // ── 2025 Tháng 8 ──
            ['name' => 'Tạ Minh Đạt',        'phone' => '0911000024', 'date' => '2025-08-05'],
            ['name' => 'Quách Thị Diễm',     'phone' => '0911000025', 'date' => '2025-08-16'],
            ['name' => 'Lương Hoàng Bách',   'phone' => '0911000026', 'date' => '2025-08-27'],
            // ── 2025 Tháng 9 ──
            ['name' => 'Từ Văn Khánh',       'phone' => '0911000027', 'date' => '2025-09-03'],
            ['name' => 'Mạc Thị Thanh',      'phone' => '0911000028', 'date' => '2025-09-14'],
            ['name' => 'Triệu Minh Nhật',    'phone' => '0911000029', 'date' => '2025-09-26'],
            // ── 2025 Tháng 10 ──
            ['name' => 'Ôn Thị Bích',        'phone' => '0911000030', 'date' => '2025-10-04'],
            ['name' => 'Doãn Văn Thắng',     'phone' => '0911000031', 'date' => '2025-10-15'],
            ['name' => 'Nghiêm Đức Trung',   'phone' => '0911000032', 'date' => '2025-10-28'],
            // ── 2025 Tháng 11 ──
            ['name' => 'Cù Thị Ngân',        'phone' => '0911000033', 'date' => '2025-11-02'],
            ['name' => 'Đoàn Văn Hưng',      'phone' => '0911000034', 'date' => '2025-11-12'],
            ['name' => 'Trương Minh Khải',    'phone' => '0911000035', 'date' => '2025-11-23'],
            // ── 2025 Tháng 12 ──
            ['name' => 'Lâm Thị Phượng',     'phone' => '0911000036', 'date' => '2025-12-01'],
            ['name' => 'Huỳnh Công Danh',    'phone' => '0911000037', 'date' => '2025-12-10'],
            ['name' => 'Võ Thanh Hà',        'phone' => '0911000038', 'date' => '2025-12-22'],
            // ── 2026 Tháng 1 ──
            ['name' => 'Nguyễn Hữu Tín',     'phone' => '0911000039', 'date' => '2026-01-04'],
            ['name' => 'Trần Thị Kim Ngân',  'phone' => '0911000040', 'date' => '2026-01-13'],
            ['name' => 'Lê Bảo Quốc',       'phone' => '0911000041', 'date' => '2026-01-25'],
            ['name' => 'Phạm Thị Hạnh',      'phone' => '0911000042', 'date' => '2026-01-30'],
            // ── 2026 Tháng 2 ──
            ['name' => 'Hoàng Xuân Trường',  'phone' => '0911000043', 'date' => '2026-02-03'],
            ['name' => 'Vũ Thị Diệu Linh',  'phone' => '0911000044', 'date' => '2026-02-11'],
            ['name' => 'Đặng Hải Đăng',      'phone' => '0911000045', 'date' => '2026-02-20'],
            ['name' => 'Bùi Minh Châu',      'phone' => '0911000046', 'date' => '2026-02-28'],
            // ── 2026 Tháng 3 ──
            ['name' => 'Ngô Quốc Huy',       'phone' => '0911000047', 'date' => '2026-03-02'],
            ['name' => 'Đinh Thị Ánh Tuyết', 'phone' => '0911000048', 'date' => '2026-03-09'],
            ['name' => 'Trương Văn Lợi',     'phone' => '0911000049', 'date' => '2026-03-16'],
            ['name' => 'Lý Minh Phát',       'phone' => '0911000050', 'date' => '2026-03-22'],
        ];

        foreach ($customers as $index => $customer) {
            $date = Carbon::parse($customer['date'] . ' ' . rand(7, 20) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00');
            User::create([
                'name'       => $customer['name'],
                'email'      => 'customer' . ($index + 1) . '@barberbook.com',
                'password'   => Hash::make('password'),
                'role'       => 'customer',
                'phone'      => $customer['phone'],
                'is_active'  => true,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
