<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Hair Care
            ['name' => 'Dầu gội Reuzel Daily Shampoo 350ml', 'price' => 350000, 'stock' => 50, 'is_active' => true],
            ['name' => 'Dầu xả Reuzel Daily Conditioner 350ml', 'price' => 380000, 'stock' => 45, 'is_active' => true],
            // Styling
            ['name' => 'Sáp vuốt tóc Reuzel Pink Pomade 113g', 'price' => 420000, 'stock' => 100, 'is_active' => true],
            ['name' => 'Sáp vuốt tóc Reuzel Blue Pomade 113g', 'price' => 420000, 'stock' => 85, 'is_active' => true],
            ['name' => 'Bột tạo phồng tóc Volcanic Ash', 'price' => 250000, 'stock' => 30, 'is_active' => true],
            ['name' => 'Sáp vuốt tóc Kevin Murphy Rough Rider', 'price' => 650000, 'stock' => 20, 'is_active' => true],
            // Tools
            ['name' => 'Lược sừng trâu Barbershop', 'price' => 150000, 'stock' => 20, 'is_active' => true],
            ['name' => 'Lược bán nguyệt tạo kiểu Chaoba', 'price' => 50000, 'stock' => 200, 'is_active' => true],
            ['name' => 'Bộ cạo râu cổ điển Gillette', 'price' => 550000, 'stock' => 10, 'is_active' => true],
            // Accessories
            ['name' => 'Áo choàng cắt tóc Barber VVIP', 'price' => 200000, 'stock' => 50, 'is_active' => true],
            ['name' => 'Khăn mặt Cotton 100% Barber (Set 5)', 'price' => 150000, 'stock' => 100, 'is_active' => true],
            ['name' => 'Máy sấy tóc chuyên nghiệp Dyson', 'price' => 8500000, 'stock' => 5, 'is_active' => true],
            ['name' => 'Sữa rửa mặt nam Nivea Men', 'price' => 120000, 'stock' => 50, 'is_active' => true],
        ];

        foreach ($products as $productData) {
            Product::create(array_merge($productData, [
                'description' => 'Sản phẩm cao cấp dành cho quý ông, được phân phối chính hãng bởi Barbershop của chúng tôi. Chất lượng đảm bảo, hiệu quả vượt trội. Thích hợp sử dụng hàng ngày và dễ dàng rửa sạch.',
            ]));
        }
    }
}
