<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        $admin = User::create([
            'name'       => 'Admin',
            'email'      => 'admin@example.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'phone'      => '0900000000',
            'is_active'  => true,
            'created_at' => Carbon::parse('2025-01-01 08:00:00'),
            'updated_at' => Carbon::parse('2025-01-01 08:00:00'),
        ]);
        $this->downloadAvatar($admin, 'Admin');

        // 2. 5 Barbers
        $barbers = [
            ['name' => 'Nguyễn Văn Nam', 'email' => 'nam@example.com', 'phone' => '0900000001'],
            ['name' => 'Trần Quang Khải', 'email' => 'khai@example.com', 'phone' => '0900000002'],
            ['name' => 'Lê Thanh Bình', 'email' => 'binh@example.com', 'phone' => '0900000003'],
            ['name' => 'Phạm Hoàng Long', 'email' => 'long@example.com', 'phone' => '0900000004'],
            ['name' => 'Vũ Minh Tuấn', 'email' => 'tuan@example.com', 'phone' => '0900000005'],
        ];

        foreach ($barbers as $barber) {
            $user = User::create([
                'name'       => $barber['name'],
                'email'      => $barber['email'],
                'password'   => Hash::make('password'),
                'role'       => 'barber',
                'phone'      => $barber['phone'],
                'is_active'  => true,
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(6),
            ]);
            $this->downloadAvatar($user, $barber['name']);
        }

        // 3. 50 Customers
        for ($i = 1; $i <= 50; $i++) {
            $date = Carbon::now()->subDays(rand(10, 180));
            $user = User::create([
                'name'       => 'Customer ' . $i,
                'email'      => 'customer' . $i . '@example.com',
                'password'   => Hash::make('password'),
                'role'       => 'customer',
                'phone'      => '091' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'is_active'  => true,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $this->downloadAvatar($user, 'Customer ' . $i);
        }
    }

    private function downloadAvatar(User $user, string $name): void
    {
        try {
            $bgColors = ['admin' => '1c1713', 'barber' => 'b08968', 'customer' => '8a7a6a'];
            $bg = $bgColors[$user->role->value] ?? '8a7a6a';
            $url = 'https://ui-avatars.com/api/?' . http_build_query([
                'name' => $name, 'size' => 200, 'background' => $bg, 'color' => 'fff', 'format' => 'png', 'bold' => 'true'
            ]);
            $imageContent = @file_get_contents($url);
            if ($imageContent) {
                $path = 'avatars/' . $user->id . '.png';
                Storage::disk('public')->put($path, $imageContent);
                $user->update(['avatar' => $path]);
            }
        } catch (\Exception $e) { }
    }
}
