<?php

namespace App\Services;

use App\Enums\LoyaltyPointType;
use App\Models\Booking;
use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    /**
     * Tỷ lệ quy đổi: mỗi 10,000 VND = 1 điểm.
     */
    const POINTS_PER_VND = 10000;

    /**
     * Cộng điểm khi booking hoàn thành.
     */
    public function rewardForBooking(Booking $booking): ?LoyaltyPoint
    {
        $points = (int) floor($booking->total_price / self::POINTS_PER_VND);

        if ($points <= 0) {
            return null;
        }

        return LoyaltyPoint::create([
            'user_id' => $booking->customer_id,
            'points' => $points,
            'type' => LoyaltyPointType::Earn,
            'description' => "Tích điểm booking #{$booking->booking_code}",
            'related_type' => Booking::class,
            'related_id' => $booking->id,
        ]);
    }

    /**
     * Trừ điểm (dùng cho đổi voucher, giảm giá...).
     */
    public function spendPoints(User $user, int $points, string $description, ?string $relatedType = null, ?int $relatedId = null): LoyaltyPoint
    {
        $balance = $this->getBalance($user);

        if ($balance < $points) {
            throw new \InvalidArgumentException("Không đủ điểm. Hiện có: {$balance}, cần: {$points}");
        }

        return LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => -$points, // Trừ điểm (giá trị âm)
            'type' => LoyaltyPointType::Spend,
            'description' => $description,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);
    }

    /**
     * Lấy số dư điểm của user.
     */
    public function getBalance(User $user): int
    {
        return (int) LoyaltyPoint::where('user_id', $user->id)->sum('points');
    }

    /**
     * Lấy lịch sử giao dịch điểm.
     */
    public function getHistory(User $user, int $limit = 20)
    {
        return LoyaltyPoint::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($limit);
    }
}
