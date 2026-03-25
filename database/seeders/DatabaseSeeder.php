<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BranchSeeder::class,   // Phải chạy trước BarberSeeder
            BarberSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
            PaymentSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
