<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Enums\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Hair Care - 5 items
            ['name' => 'Dầu gội Reuzel Daily Shampoo 350ml', 'category' => ProductCategory::HairCare, 'price' => 350000, 'stock' => 50, 'seed' => 'shampoo'],
            ['name' => 'Dầu xả phục hồi tóc 18.21 Man Made', 'category' => ProductCategory::HairCare, 'price' => 600000, 'stock' => 30, 'seed' => 'conditioner'],
            ['name' => 'Thuốc mọc tóc Minoxidil 5%', 'category' => ProductCategory::HairCare, 'price' => 250000, 'stock' => 80, 'seed' => 'hair,serum'],
            ['name' => 'Tinh dầu dưỡng tóc Moroccanoil 100ml', 'category' => ProductCategory::HairCare, 'price' => 800000, 'stock' => 25, 'seed' => 'hair,oil'],
            ['name' => 'Xịt dưỡng tóc muối biển Sea Salt', 'category' => ProductCategory::HairCare, 'price' => 150000, 'stock' => 100, 'seed' => 'sea,salt,spray'],

            // Styling - 8 items
            ['name' => 'Sáp Reuzel Pink Pomade 113g', 'category' => ProductCategory::Styling, 'price' => 420000, 'stock' => 60, 'seed' => 'pomade,pink'],
            ['name' => 'Sáp Reuzel Blue Pomade 113g', 'category' => ProductCategory::Styling, 'price' => 420000, 'stock' => 55, 'seed' => 'pomade,blue'],
            ['name' => 'Bột tạo phồng tóc Volcanic Ash', 'category' => ProductCategory::Styling, 'price' => 250000, 'stock' => 40, 'seed' => 'hair,powder'],
            ['name' => 'Sáp Kevin Murphy Rough Rider', 'category' => ProductCategory::Styling, 'price' => 650000, 'stock' => 20, 'seed' => 'hair,wax'],
            ['name' => 'Sáp Hanz de Fuko Quicksand', 'category' => ProductCategory::Styling, 'price' => 550000, 'stock' => 35, 'seed' => 'clay,hair'],
            ['name' => 'Apestomen Volcanic Clay', 'category' => ProductCategory::Styling, 'price' => 320000, 'stock' => 85, 'seed' => 'volcanic,clay'],
            ['name' => 'Gôm xịt tóc Silhouette Super Hold', 'category' => ProductCategory::Styling, 'price' => 220000, 'stock' => 70, 'seed' => 'hairspray'],
            ['name' => 'Gel tạo kiểu tóc Gatsby', 'category' => ProductCategory::Styling, 'price' => 150000, 'stock' => 90, 'seed' => 'hairgel'],

            // Tools - 5 items
            ['name' => 'Lược sừng trâu Barbershop', 'category' => ProductCategory::Tools, 'price' => 200000, 'stock' => 45, 'seed' => 'comb,horn'],
            ['name' => 'Lược bán nguyệt Chaoba', 'category' => ProductCategory::Tools, 'price' => 150000, 'stock' => 100, 'seed' => 'brush,hair'],
            ['name' => 'Bộ cạo râu cổ điển Gillette', 'category' => ProductCategory::Tools, 'price' => 550000, 'stock' => 15, 'seed' => 'razor,shave'],
            ['name' => 'Tông đơ Wahl Magic Clip', 'category' => ProductCategory::Tools, 'price' => 800000, 'stock' => 10, 'seed' => 'clipper,barber'],
            ['name' => 'Chổi quét phấn cổ mềm', 'category' => ProductCategory::Tools, 'price' => 180000, 'stock' => 60, 'seed' => 'brush,neck'],

            // Accessories & Other - 2 items
            ['name' => 'Áo choàng cắt tóc Barber VVIP', 'category' => ProductCategory::Accessories, 'price' => 250000, 'stock' => 50, 'seed' => 'cape,barber'],
            ['name' => 'Sữa rửa mặt nam Nivea Men', 'category' => ProductCategory::Other, 'price' => 160000, 'stock' => 75, 'seed' => 'facewash,men'],
        ];

        foreach ($products as $index => $prod) {
            $keyword = $prod['seed'];
            $stock = $prod['stock'];
            unset($prod['seed'], $prod['stock']);

            $product = Product::create(array_merge($prod, [
                'slug' => Str::slug($prod['name']),
                'sku' => 'PRD-' . strtoupper(Str::random(6)),
                'stock_quantity' => $stock,
                'is_active' => true,
                'description' => 'Sản phẩm cao cấp dành cho nam giới. ' . $prod['name'] . ' mang lại hiệu quả vượt trội, hàng chính hãng 100%.',
            ]));

            $this->downloadProductImage($product, $keyword, $index);
        }
    }

    private function downloadProductImage(Product $product, string $keyword, int $seedId): void
    {
        try {
            $url = "https://source.unsplash.com/600x600/?{$keyword},product,sig={$seedId}";
            $url = "https://picsum.photos/seed/product_{$seedId}_{$keyword}/600/600";
            
            $context = stream_context_create([
                'http' => ['follow_location' => true, 'max_redirects' => 5, 'timeout' => 10],
            ]);

            $imageContent = @file_get_contents($url, false, $context);
            if ($imageContent) {
                $path = 'products/' . $product->id . '.jpg';
                Storage::disk('public')->put($path, $imageContent);
                $product->update(['image' => $path]);
            }
        } catch (\Exception $e) { }
    }
}
