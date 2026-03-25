<?php

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\Branch;
use App\Models\User;
use App\Models\WorkingSchedule;
use Illuminate\Database\Seeder;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $barberUsers = User::where('role', 'barber')->get();
        if ($barberUsers->isEmpty()) return;

        $branches = Branch::all();
        if ($branches->isEmpty()) return;

        $bios = [
            'Chuyên gia cắt tóc nam với hơn 5 năm kinh nghiệm. Thành thạo các kiểu tóc Hàn Quốc, under cut.',
            'Thợ cắt tóc chuyên nghiệp, yêu nghề. Rất giỏi tư vấn kiểu tóc phù hợp khuôn mặt.',
            'Barber trẻ năng động, cập nhật xu hướng tóc mới nhất. Chuyên tóc nam phong cách.',
            'Thợ cắt kỳ cựu, chuyên cắt tóc cổ điển và cạo râu truyền thống.',
            'Barber chuyên về tóc nghệ thuật và nhuộm tóc nam sáng tạo.',
        ];

        $experiences = [5, 3, 2, 8, 4];

        foreach ($barberUsers as $index => $user) {
            // Distribute to branches evenly
            $branch = $branches[$index % $branches->count()];

            $barber = Barber::create([
                'user_id'          => $user->id,
                'bio'              => $bios[$index % count($bios)],
                'experience_years' => $experiences[$index % count($experiences)],
                'rating'           => 0.00,
                'is_active'        => true,
                'branch_id'        => $branch->id,
            ]);

            // Initialize WorkingSchedule for 30 days past and 15 days future?
            // Actually WorkingSchedule in this system is usually weekly schema: day_of_week Enum!
            // Wait, let's see BarberSeeder's old implementation, it uses week days (0-6).
            for ($day = 0; $day <= 6; $day++) {
                WorkingSchedule::create([
                    'barber_id'   => $barber->id,
                    'day_of_week' => $day,
                    'start_time'  => '08:00:00',
                    'end_time'    => '20:00:00', // Expand slightly to allow heatmap testing until 20:00
                    'is_day_off'  => $day === 0, // Sunday off
                ]);
            }
        }
    }
}
