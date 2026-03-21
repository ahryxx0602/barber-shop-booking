<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@barberbook.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '0900000001',
        ]);

        // Barbers
        User::create([
            'name'     => 'Nguyễn Văn Hùng',
            'email'    => 'hung@barberbook.com',
            'password' => Hash::make('password'),
            'role'     => 'barber',
            'phone'    => '0900000002',
        ]);

        User::create([
            'name'     => 'Trần Minh Khoa',
            'email'    => 'khoa@barberbook.com',
            'password' => Hash::make('password'),
            'role'     => 'barber',
            'phone'    => '0900000003',
        ]);

        User::create([
            'name'     => 'Lê Quốc Bảo',
            'email'    => 'bao@barberbook.com',
            'password' => Hash::make('password'),
            'role'     => 'barber',
            'phone'    => '0900000004',
        ]);

        // Customers
        User::create([
            'name'     => 'Khách Hàng A',
            'email'    => 'customer1@barberbook.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
            'phone'    => '0900000010',
        ]);

        User::create([
            'name'     => 'Khách Hàng B',
            'email'    => 'customer2@barberbook.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
            'phone'    => '0900000011',
        ]);
    }
}
