<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Cắt tóc nam cơ bản',   'price' => 50000,  'duration_minutes' => 30, 'description' => 'Cắt tóc nam theo yêu cầu, bao gồm gội đầu.'],
            ['name' => 'Cắt tóc Undercut',      'price' => 80000,  'duration_minutes' => 45, 'description' => 'Kiểu tóc undercut thời thượng, cạo viền sắc nét.'],
            ['name' => 'Cắt tóc Fade',          'price' => 100000, 'duration_minutes' => 60, 'description' => 'Fade tóc chuyên nghiệp, tạo hiệu ứng chuyển màu mượt mà.'],
            ['name' => 'Uốn tóc nam',            'price' => 200000, 'duration_minutes' => 90, 'description' => 'Uốn tóc xoăn nhẹ hoặc xoăn lớn theo phong cách Hàn Quốc.'],
            ['name' => 'Nhuộm tóc',              'price' => 300000, 'duration_minutes' => 120, 'description' => 'Nhuộm màu tóc theo yêu cầu, dùng thuốc nhuộm chất lượng cao.'],
            ['name' => 'Gội đầu massage',        'price' => 40000,  'duration_minutes' => 30, 'description' => 'Gội đầu kết hợp massage thư giãn đầu cổ vai.'],
            ['name' => 'Cạo râu tạo kiểu',      'price' => 60000,  'duration_minutes' => 30, 'description' => 'Cạo râu sạch hoặc tạo hình râu theo phong cách.'],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, ['is_active' => true]));
        }
    }
}
