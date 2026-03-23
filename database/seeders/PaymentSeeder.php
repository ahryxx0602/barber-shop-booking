<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Booking completed + confirmed + in_progress => tạo payment
        $bookings = Booking::whereIn('status', [
            BookingStatus::Completed,
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
        ])->get();

        $methods = ['cash', 'vnpay', 'momo'];

        foreach ($bookings as $booking) {
            // Completed => 100% paid
            // Confirmed/InProgress => 80% paid, 20% pending
            if ($booking->status === BookingStatus::Completed) {
                $paymentStatus = 'paid';
            } else {
                $paymentStatus = rand(1, 100) <= 80 ? 'paid' : 'pending';
            }

            // Phương thức thanh toán: 50% cash, 30% momo, 20% vnpay
            $roll = rand(1, 100);
            if ($roll <= 50) {
                $method = 'cash';
            } elseif ($roll <= 80) {
                $method = 'momo';
            } else {
                $method = 'vnpay';
            }

            $paidAt = $paymentStatus === 'paid'
                ? Carbon::parse($booking->booking_date)->setHour(rand(8, 19))->setMinute(rand(0, 59))
                : null;

            $transactionId = $method !== 'cash' && $paymentStatus === 'paid'
                ? strtoupper($method) . '-' . Str::random(12)
                : null;

            Payment::create([
                'booking_id'     => $booking->id,
                'amount'         => $booking->total_price,
                'method'         => $method,
                'status'         => $paymentStatus,
                'transaction_id' => $transactionId,
                'paid_at'        => $paidAt,
                'created_at'     => Carbon::parse($booking->booking_date)->subHours(rand(0, 2)),
                'updated_at'     => $paidAt ?? Carbon::parse($booking->booking_date),
            ]);
        }
    }
}
