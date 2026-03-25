<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
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

        if ($barbers->isEmpty() || $services->isEmpty() || $customers->isEmpty()) return;

        $barberIds    = $barbers->pluck('id')->toArray();
        $customerIds  = $customers->pluck('id')->toArray();
        $serviceList  = $services->toArray();
        // Heatmap needs distributed times 08:00 - 20:00 (every 30 mins)
        $timeSlots = [];
        for ($h = 8; $h <= 19; $h++) {
            $hStr = str_pad($h, 2, '0', STR_PAD_LEFT);
            $timeSlots[] = "$hStr:00";
            $timeSlots[] = "$hStr:30";
        }
        $timeSlots[] = "20:00";

        $notes = ['Cắt ngắn gọn', 'Muốn kiểu Hàn Quốc', 'Đến sớm 5 phút', null, 'Cạo sạch 2 bên'];
        $reviewComments = ['Cắt rất đẹp, thợ nhiệt tình.', 'Dịch vụ tốt, không gian thoải mái.', 'Sẽ quay lại lần sau.', 'Giá hơi cao nhưng chất lượng.', 'Tuyệt vời!', 'Cắt đúng ý mình, 10 điểm.'];

        $now = Carbon::now();

        // 1. PAST BOOKINGS: ~200 bookings in the last 60 days
        for ($i = 0; $i < 200; $i++) {
            $daysAgo = rand(1, 60);
            $date = $now->copy()->subDays($daysAgo);
            
            // Skip Sunday if barbers don't work, but let's allow some
            if ($date->isSunday()) $date->subDay();

            $barberId   = $barberIds[array_rand($barberIds)];
            $customerId = $customerIds[array_rand($customerIds)];
            $startTime  = $timeSlots[array_rand($timeSlots)];

            $numServices = rand(1, 3);
            $selectedServices = collect($serviceList)->random($numServices);
            $totalPrice    = $selectedServices->sum('price');
            $totalDuration = $selectedServices->sum('duration_minutes');
            $endTime = Carbon::parse($startTime)->addMinutes($totalDuration)->format('H:i');

            $timeSlot = TimeSlot::firstOrCreate(
                ['barber_id' => $barberId, 'slot_date' => $date->format('Y-m-d'), 'start_time' => $startTime . ':00'],
                ['end_time' => $endTime . ':00', 'status' => 'booked']
            );
            if (!$timeSlot->wasRecentlyCreated) continue;

            $booking = Booking::create([
                'booking_code' => 'BK' . strtoupper(Str::random(8)),
                'customer_id'  => $customerId,
                'barber_id'    => $barberId,
                'time_slot_id' => $timeSlot->id,
                'booking_date' => $date->format('Y-m-d'),
                'start_time'   => $startTime . ':00',
                'end_time'     => $endTime . ':00',
                'total_price'  => $totalPrice,
                'status'       => BookingStatus::Completed,
                'note'         => $notes[array_rand($notes)],
                'created_at'   => $date->copy()->subDays(rand(1, 3))->setHour(rand(8, 20)),
                'updated_at'   => $date,
            ]);

            foreach ($selectedServices as $svc) {
                $booking->services()->attach($svc['id'], [
                    'price_snapshot'    => $svc['price'],
                    'duration_snapshot' => $svc['duration_minutes'],
                ]);
            }

            // Payment
            $methods = [\App\Enums\PaymentMethod::Cash, \App\Enums\PaymentMethod::VNPay, \App\Enums\PaymentMethod::Momo];
            $method = $methods[array_rand($methods)];
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalPrice,
                'method' => $method,
                'status' => PaymentStatus::Paid,
                'transaction_id' => $method !== \App\Enums\PaymentMethod::Cash ? strtoupper(Str::random(10)) : null,
                'paid_at' => $date->copy()->setHour((int)explode(':', $startTime)[0])->setMinute(rand(0, 59)),
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Review
            if (rand(1, 100) <= 80) { // 80% chance to leave a review
                Review::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $customerId,
                    'barber_id' => $barberId,
                    'rating' => rand(4, 5),
                    'comment' => $reviewComments[array_rand($reviewComments)],
                    'created_at' => $date->copy()->addHours(1),
                    'updated_at' => $date->copy()->addHours(1),
                ]);
            }
        }

        // 2. FUTURE/TODAY BOOKINGS: ~30 bookings for the next 7 days
        for ($i = 0; $i < 30; $i++) {
            $daysAhead = rand(0, 7);
            $date = $now->copy()->addDays($daysAhead);
            if ($date->isSunday()) $date->addDay();

            $barberId   = $barberIds[array_rand($barberIds)];
            $customerId = $customerIds[array_rand($customerIds)];
            $startTime  = $timeSlots[array_rand($timeSlots)];

            $numServices = rand(1, 2);
            $selectedServices = collect($serviceList)->random($numServices);
            $totalPrice    = $selectedServices->sum('price');
            $totalDuration = $selectedServices->sum('duration_minutes');
            $endTime = Carbon::parse($startTime)->addMinutes($totalDuration)->format('H:i');

            $timeSlot = TimeSlot::firstOrCreate(
                ['barber_id' => $barberId, 'slot_date' => $date->format('Y-m-d'), 'start_time' => $startTime . ':00'],
                ['end_time' => $endTime . ':00', 'status' => 'booked']
            );
            if (!$timeSlot->wasRecentlyCreated) continue;

            $status = $daysAhead === 0 ? BookingStatus::Confirmed : (rand(0, 1) ? BookingStatus::Pending : BookingStatus::Confirmed);

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
                'created_at'   => $now->copy()->subHours(rand(1, 24)),
                'updated_at'   => $now,
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
