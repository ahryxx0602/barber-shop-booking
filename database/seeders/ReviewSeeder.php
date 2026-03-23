<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả booking đã hoàn thành
        $completedBookings = Booking::where('status', BookingStatus::Completed)->get();

        $comments = [
            'Cắt rất đẹp, đúng ý! Sẽ quay lại.',
            'Thợ cắt tay nghề cao, rất hài lòng.',
            'Không gian sạch sẽ, phục vụ nhiệt tình.',
            'Tóc cắt hơi ngắn hơn mong muốn nhưng tổng thể ổn.',
            'Rất chuyên nghiệp, đúng giờ. Highly recommend!',
            'Cắt đẹp, massage đầu rất thư giãn.',
            'Lần đầu đến nhưng rất ấn tượng, thợ tư vấn tốt.',
            'Giá hợp lý, chất lượng tốt.',
            'Phải chờ hơi lâu nhưng kết quả đẹp.',
            null,
            'Tuyệt vời! Kiểu tóc đúng trend.',
            null,
            'Thợ rất nhiệt tình, cắt kỹ từng chi tiết.',
            'Dịch vụ nhuộm tóc rất chuyên nghiệp.',
            'Cạo râu sạch sẽ, thoải mái.',
            null,
            'Sẽ giới thiệu bạn bè đến.',
            'Lần thứ 3 đến, luôn hài lòng.',
            'Cắt ổn, không xuất sắc nhưng đáng tiền.',
            'Gội đầu massage cực kỳ relaxing!',
        ];

        // 70% booking completed sẽ có review
        foreach ($completedBookings as $booking) {
            if (rand(1, 100) > 70) continue;

            // Rating: chủ yếu 4-5 sao, ít 3 sao, hiếm 1-2
            $roll = rand(1, 100);
            if ($roll <= 45) {
                $rating = 5;
            } elseif ($roll <= 80) {
                $rating = 4;
            } elseif ($roll <= 92) {
                $rating = 3;
            } elseif ($roll <= 97) {
                $rating = 2;
            } else {
                $rating = 1;
            }

            Review::create([
                'booking_id'  => $booking->id,
                'customer_id' => $booking->customer_id,
                'barber_id'   => $booking->barber_id,
                'rating'      => $rating,
                'comment'     => $comments[array_rand($comments)],
                'created_at'  => Carbon::parse($booking->booking_date)->addHours(rand(2, 48)),
                'updated_at'  => Carbon::parse($booking->booking_date)->addHours(rand(2, 48)),
            ]);
        }

        // Cập nhật rating trung bình cho từng barber
        $barbers = Barber::all();
        foreach ($barbers as $barber) {
            $avgRating = Review::where('barber_id', $barber->id)->avg('rating');
            if ($avgRating) {
                $barber->update(['rating' => round($avgRating, 2)]);
            }
        }
    }
}
