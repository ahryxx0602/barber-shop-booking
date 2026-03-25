<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Cắt tóc nam tiêu chuẩn', 'price' => 100000, 'duration_minutes' => 30, 'description' => 'Tư vấn kiểu tóc, cắt gọn gàng và cạo viền sắc nét.', 'seed' => 'haircut'],
            ['name' => 'Cắt tóc Fade cao cấp', 'price' => 150000, 'duration_minutes' => 45, 'description' => 'Hiệu ứng mờ dần hoàn hảo, tạo cảm giác nam tính mạnh mẽ.', 'seed' => 'barber,fade'],
            ['name' => 'Combo Cắt & Gội thư giãn', 'price' => 200000, 'duration_minutes' => 60, 'description' => 'Cắt tạo kiểu kết hợp gội đầu massage êm ái.', 'seed' => 'hairwash'],
            ['name' => 'Uốn tóc Hàn Quốc', 'price' => 350000, 'duration_minutes' => 90, 'description' => 'Uốn xoăn lơi nếp tự nhiên, bồng bềnh chuẩn soái ca.', 'seed' => 'perm'],
            ['name' => 'Uốn Premlock / Ruffled', 'price' => 500000, 'duration_minutes' => 120, 'description' => 'Kiểu uốn cá tính, mạnh mẽ dành cho các chàng trai năng động.', 'seed' => 'curly,hair'],
            ['name' => 'Nhuộm tóc thời trang', 'price' => 400000, 'duration_minutes' => 90, 'description' => 'Lên màu chuẩn, dưỡng tóc mềm mượt không hư tổn.', 'seed' => 'hairdye'],
            ['name' => 'Tẩy tóc & Nhuộm khói', 'price' => 500000, 'duration_minutes' => 150, 'description' => 'Trọn gói tẩy và nhuộm các gam màu sáng, xám khói, bạch kim.', 'seed' => 'blonde,hair'],
            ['name' => 'Phục hồi tóc hư tổn', 'price' => 300000, 'duration_minutes' => 45, 'description' => 'Hấp dầu siêu dưỡng, phục hồi tóc xơ do hóa chất.', 'seed' => 'keratin'],
            ['name' => 'Cạo râu khăn nóng', 'price' => 100000, 'duration_minutes' => 30, 'description' => 'Thư giãn với khăn nóng, cạo râu tạo kiểu cổ điển.', 'seed' => 'shaving,beard'],
            ['name' => 'Gội đầu dưỡng sinh', 'price' => 250000, 'duration_minutes' => 60, 'description' => 'Gội đầu thảo dược, massage bấm huyệt cổ vai gáy.', 'seed' => 'headmassage'],
        ];

        foreach ($services as $key => $service) {
            $keyword = $service['seed'];
            unset($service['seed']);

            $serv = Service::create(array_merge($service, ['is_active' => true]));

            $this->downloadServiceImage($serv, $keyword, $key);
        }
    }

    private function downloadServiceImage(Service $service, string $keyword, int $seedId): void
    {
        try {
            // Using a realistic unsplash search URL or picsum with a custom seed
            $url = "https://source.unsplash.com/600x600/?{$keyword},sig={$seedId}";
            
            // Fallback for more reliable images
            $url = "https://picsum.photos/seed/service_{$seedId}_{$keyword}/600/600";
            
            $context = stream_context_create([
                'http' => ['follow_location' => true, 'max_redirects' => 5, 'timeout' => 10],
            ]);

            $imageContent = @file_get_contents($url, false, $context);
            if ($imageContent) {
                $path = 'services/' . $service->id . '.jpg';
                Storage::disk('public')->put($path, $imageContent);
                $service->update(['image' => $path]);
            }
        } catch (\Exception $e) { }
    }
}
