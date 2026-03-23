<?php

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\User;
use App\Models\WorkingSchedule;
use Illuminate\Database\Seeder;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $barberUsers = User::where('role', 'barber')->get();

        $bios = [
            'Chuyên gia cắt tóc nam với hơn 5 năm kinh nghiệm. Thành thạo các kiểu tóc Hàn Quốc, undercut, fade.',
            'Thợ cắt tóc chuyên nghiệp, yêu nghề. Giỏi tư vấn kiểu tóc phù hợp khuôn mặt.',
            'Barber trẻ năng động, cập nhật xu hướng tóc mới nhất. Chuyên tóc nam phong cách.',
            'Thợ cắt kỳ cựu, chuyên cắt tóc cổ điển và cạo râu truyền thống. Tỉ mỉ từng chi tiết.',
            'Barber chuyên về tóc nghệ thuật và nhuộm tóc nam. Sáng tạo, luôn đổi mới phong cách.',
        ];

        $experiences = [5, 3, 2, 8, 1];

        foreach ($barberUsers as $index => $user) {
            $barber = Barber::create([
                'user_id'          => $user->id,
                'bio'              => $bios[$index % count($bios)],
                'experience_years' => $experiences[$index % count($experiences)],
                'rating'           => 0.00,
                'is_active'        => true,
            ]);

            // Working schedule: T2-T7 (1-6), nghỉ CN (0)
            for ($day = 0; $day <= 6; $day++) {
                WorkingSchedule::create([
                    'barber_id'   => $barber->id,
                    'day_of_week' => $day,
                    'start_time'  => '08:00:00',
                    'end_time'    => '18:00:00',
                    'is_day_off'  => $day === 0, // Nghỉ Chủ Nhật
                ]);
            }
        }
    }
}
