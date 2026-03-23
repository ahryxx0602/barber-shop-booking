<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $barbers   = Barber::all();
        $services  = Service::all();
        $customers = User::where('role', 'customer')->get();

        if ($barbers->isEmpty() || $services->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('Cần chạy UserSeeder, BarberSeeder, ServiceSeeder trước.');
            return;
        }

        $barberIds    = $barbers->pluck('id')->toArray();
        $customerIds  = $customers->pluck('id')->toArray();
        $serviceList  = $services->toArray();
        $timeSlots    = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00',
                         '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'];

        $notes = [
            null,
            'Cắt ngắn gọn, không cần kiểu',
            'Muốn kiểu Hàn Quốc',
            'Đến sớm 5 phút',
            'Lần đầu đến, nhờ tư vấn kiểu tóc',
            'Cắt giống lần trước',
            null,
            'Cạo sạch 2 bên',
            null,
        ];

        // ────────────────────────────────────────────────────
        // Bookings lịch sử — rải từ tháng 1/2025 đến 22/03/2026
        // Trạng thái: completed, cancelled (lịch sử)
        // ────────────────────────────────────────────────────
        $historicalMonths = [];
        // 2025: T1 → T12
        for ($m = 1; $m <= 12; $m++) {
            $historicalMonths[] = ['year' => 2025, 'month' => $m];
        }
        // 2026: T1, T2
        $historicalMonths[] = ['year' => 2026, 'month' => 1];
        $historicalMonths[] = ['year' => 2026, 'month' => 2];

        foreach ($historicalMonths as $period) {
            // 8-12 bookings mỗi tháng
            $bookingCount = rand(8, 12);

            for ($i = 0; $i < $bookingCount; $i++) {
                $day = rand(1, 28);
                $date = Carbon::create($period['year'], $period['month'], $day);

                // Chỉ ngày làm việc (T2-T7)
                if ($date->isSunday()) {
                    $date->addDay();
                }

                $barberId   = $barberIds[array_rand($barberIds)];
                $customerId = $customerIds[array_rand($customerIds)];
                $startTime  = $timeSlots[array_rand($timeSlots)];

                // Random 1-3 dịch vụ
                $numServices = rand(1, 3);
                $selectedServices = collect($serviceList)->random($numServices);
                $totalPrice    = $selectedServices->sum('price');
                $totalDuration = $selectedServices->sum('duration_minutes');
                $endTime = Carbon::parse($startTime)->addMinutes($totalDuration)->format('H:i');

                // Tạo time slot
                $timeSlot = TimeSlot::firstOrCreate(
                    [
                        'barber_id'  => $barberId,
                        'slot_date'  => $date->format('Y-m-d'),
                        'start_time' => $startTime . ':00',
                    ],
                    [
                        'end_time' => $endTime . ':00',
                        'status'   => 'booked',
                    ]
                );

                // Nếu slot đã booked rồi thì bỏ qua
                if (!$timeSlot->wasRecentlyCreated) {
                    continue;
                }

                // 80% completed, 20% cancelled
                $status = rand(1, 10) <= 8 ? BookingStatus::Completed : BookingStatus::Cancelled;

                $booking = Booking::create([
                    'booking_code' => 'BK' . strtoupper(Str::random(8)),
                    'customer_id'  => $customerId,
                    'barber_id'    => $barberId,
                    'time_slot_id' => $timeSlot->id,
                    'booking_date' => $date->format('Y-m-d'),
                    'start_time'   => $startTime . ':00',
                    'end_time'     => $endTime . ':00',
                    'total_price'  => $totalPrice,
                    'status'       => $status,
                    'note'         => $notes[array_rand($notes)],
                    'cancelled_at' => $status === BookingStatus::Cancelled ? $date->copy()->addHours(rand(1, 4)) : null,
                    'cancel_reason'=> $status === BookingStatus::Cancelled ? 'Khách hủy vì lý do cá nhân' : null,
                    'created_at'   => $date->copy()->subDays(rand(0, 2))->setHour(rand(7, 21)),
                    'updated_at'   => $date,
                ]);

                // Attach services
                foreach ($selectedServices as $svc) {
                    $booking->services()->attach($svc['id'], [
                        'price_snapshot'    => $svc['price'],
                        'duration_snapshot' => $svc['duration_minutes'],
                    ]);
                }
            }
        }

        // ────────────────────────────────────────────────────
        // Bookings tháng 3/2026 (tháng hiện tại, trước hôm nay)
        // Trạng thái: completed, confirmed, in_progress, cancelled
        // ────────────────────────────────────────────────────
        for ($day = 1; $day <= 22; $day++) {
            $date = Carbon::create(2026, 3, $day);
            if ($date->isSunday()) continue;
            if ($date->isToday()) continue; // Hôm nay xử lý riêng

            // 2-4 bookings mỗi ngày
            $bookingCount = rand(2, 4);
            for ($i = 0; $i < $bookingCount; $i++) {
                $barberId   = $barberIds[array_rand($barberIds)];
                $customerId = $customerIds[array_rand($customerIds)];
                $startTime  = $timeSlots[array_rand($timeSlots)];

                $numServices = rand(1, 3);
                $selectedServices = collect($serviceList)->random($numServices);
                $totalPrice    = $selectedServices->sum('price');
                $totalDuration = $selectedServices->sum('duration_minutes');
                $endTime = Carbon::parse($startTime)->addMinutes($totalDuration)->format('H:i');

                $timeSlot = TimeSlot::firstOrCreate(
                    [
                        'barber_id'  => $barberId,
                        'slot_date'  => $date->format('Y-m-d'),
                        'start_time' => $startTime . ':00',
                    ],
                    [
                        'end_time' => $endTime . ':00',
                        'status'   => 'booked',
                    ]
                );

                // Nếu slot đã booked rồi thì bỏ qua
                if (!$timeSlot->wasRecentlyCreated) {
                    continue;
                }

                // Trạng thái đa dạng: 60% completed, 15% confirmed, 10% in_progress, 15% cancelled
                $roll = rand(1, 100);
                if ($roll <= 60) {
                    $status = BookingStatus::Completed;
                } elseif ($roll <= 75) {
                    $status = BookingStatus::Confirmed;
                } elseif ($roll <= 85) {
                    $status = BookingStatus::InProgress;
                } else {
                    $status = BookingStatus::Cancelled;
                }

                $booking = Booking::create([
                    'booking_code' => 'BK' . strtoupper(Str::random(8)),
                    'customer_id'  => $customerId,
                    'barber_id'    => $barberId,
                    'time_slot_id' => $timeSlot->id,
                    'booking_date' => $date->format('Y-m-d'),
                    'start_time'   => $startTime . ':00',
                    'end_time'     => $endTime . ':00',
                    'total_price'  => $totalPrice,
                    'status'       => $status,
                    'note'         => $notes[array_rand($notes)],
                    'cancelled_at' => $status === BookingStatus::Cancelled ? $date->copy()->addHours(rand(1, 3)) : null,
                    'cancel_reason'=> $status === BookingStatus::Cancelled ? 'Không sắp xếp được thời gian' : null,
                    'created_at'   => $date->copy()->subDays(rand(0, 1))->setHour(rand(7, 21)),
                    'updated_at'   => $date,
                ]);

                foreach ($selectedServices as $svc) {
                    $booking->services()->attach($svc['id'], [
                        'price_snapshot'    => $svc['price'],
                        'duration_snapshot' => $svc['duration_minutes'],
                    ]);
                }
            }
        }

        // ────────────────────────────────────────────────────
        // Bookings HÔM NAY (23/03/2026) — nhiều pending chưa duyệt!
        // ────────────────────────────────────────────────────
        $today = Carbon::today();
        $todaySlots = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00',
                       '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];

        // 8 booking pending (chưa duyệt)
        for ($i = 0; $i < 8; $i++) {
            $barberId   = $barberIds[$i % count($barberIds)];
            $customerId = $customerIds[array_rand($customerIds)];
            $startTime  = $todaySlots[$i % count($todaySlots)];

            $numServices = rand(1, 3);
            $selectedServices = collect($serviceList)->random($numServices);
            $totalPrice    = $selectedServices->sum('price');
            $totalDuration = $selectedServices->sum('duration_minutes');
            $endTime = Carbon::parse($startTime)->addMinutes($totalDuration)->format('H:i');

            $timeSlot = TimeSlot::firstOrCreate(
                ['barber_id' => $barberId, 'slot_date' => $today->format('Y-m-d'), 'start_time' => $startTime . ':00'],
                ['end_time' => $endTime . ':00', 'status' => 'booked']
            );
            if (!$timeSlot->wasRecentlyCreated) continue;

            $booking = Booking::create([
                'booking_code' => 'BK' . strtoupper(Str::random(8)),
                'customer_id'  => $customerId,
                'barber_id'    => $barberId,
                'time_slot_id' => $timeSlot->id,
                'booking_date' => $today->format('Y-m-d'),
                'start_time'   => $startTime . ':00',
                'end_time'     => $endTime . ':00',
                'total_price'  => $totalPrice,
                'status'       => BookingStatus::Pending,
                'note'         => $notes[array_rand($notes)],
                'created_at'   => $today->copy()->setHour(rand(6, 9))->setMinute(rand(0, 59)),
                'updated_at'   => $today,
            ]);

            foreach ($selectedServices as $svc) {
                $booking->services()->attach($svc['id'], [
                    'price_snapshot'    => $svc['price'],
                    'duration_snapshot' => $svc['duration_minutes'],
                ]);
            }
        }

        // 4 booking confirmed hôm nay
        for ($i = 0; $i < 4; $i++) {
            $barberId   = $barberIds[$i % count($barberIds)];
            $customerId = $customerIds[array_rand($customerIds)];
            $startTime  = $todaySlots[($i + 8) % count($todaySlots)];

            $numServices = rand(1, 2);
            $selectedServices = collect($serviceList)->random($numServices);
            $totalPrice    = $selectedServices->sum('price');
            $totalDuration = $selectedServices->sum('duration_minutes');
            $endTime = Carbon::parse($startTime)->addMinutes($totalDuration)->format('H:i');

            $timeSlot = TimeSlot::firstOrCreate(
                ['barber_id' => $barberId, 'slot_date' => $today->format('Y-m-d'), 'start_time' => $startTime . ':00'],
                ['end_time' => $endTime . ':00', 'status' => 'booked']
            );
            if (!$timeSlot->wasRecentlyCreated) continue;

            $booking = Booking::create([
                'booking_code' => 'BK' . strtoupper(Str::random(8)),
                'customer_id'  => $customerId,
                'barber_id'    => $barberId,
                'time_slot_id' => $timeSlot->id,
                'booking_date' => $today->format('Y-m-d'),
                'start_time'   => $startTime . ':00',
                'end_time'     => $endTime . ':00',
                'total_price'  => $totalPrice,
                'status'       => BookingStatus::Confirmed,
                'note'         => $notes[array_rand($notes)],
                'created_at'   => $today->copy()->subDay()->setHour(rand(18, 22)),
                'updated_at'   => $today,
            ]);

            foreach ($selectedServices as $svc) {
                $booking->services()->attach($svc['id'], [
                    'price_snapshot'    => $svc['price'],
                    'duration_snapshot' => $svc['duration_minutes'],
                ]);
            }
        }

        // 2 booking đang phục vụ hôm nay
        for ($i = 0; $i < 2; $i++) {
            $barberId   = $barberIds[$i % count($barberIds)];
            $customerId = $customerIds[array_rand($customerIds)];
            $startTime  = $todaySlots[($i + 12) % count($todaySlots)];

            $numServices = rand(1, 2);
            $selectedServices = collect($serviceList)->random($numServices);
            $totalPrice    = $selectedServices->sum('price');
            $totalDuration = $selectedServices->sum('duration_minutes');
            $endTime = Carbon::parse($startTime)->addMinutes($totalDuration)->format('H:i');

            $timeSlot = TimeSlot::firstOrCreate(
                ['barber_id' => $barberId, 'slot_date' => $today->format('Y-m-d'), 'start_time' => $startTime . ':00'],
                ['end_time' => $endTime . ':00', 'status' => 'booked']
            );
            if (!$timeSlot->wasRecentlyCreated) continue;

            $booking = Booking::create([
                'booking_code' => 'BK' . strtoupper(Str::random(8)),
                'customer_id'  => $customerId,
                'barber_id'    => $barberId,
                'time_slot_id' => $timeSlot->id,
                'booking_date' => $today->format('Y-m-d'),
                'start_time'   => $startTime . ':00',
                'end_time'     => $endTime . ':00',
                'total_price'  => $totalPrice,
                'status'       => BookingStatus::InProgress,
                'note'         => 'Đang cắt',
                'created_at'   => $today->copy()->subDay()->setHour(rand(15, 20)),
                'updated_at'   => $today,
            ]);

            foreach ($selectedServices as $svc) {
                $booking->services()->attach($svc['id'], [
                    'price_snapshot'    => $svc['price'],
                    'duration_snapshot' => $svc['duration_minutes'],
                ]);
            }
        }
    }
}
