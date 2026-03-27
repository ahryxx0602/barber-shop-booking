<?php

namespace App\Services;

use App\Enums\LoyaltyPointType;
use App\Models\Booking;
use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    const POINTS_PER_VND = 10000;

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

    public function spendPoints(User $user, int $points, string $description, ?string $relatedType = null, ?int $relatedId = null): LoyaltyPoint
    {
        $balance = $this->getBalance($user);

        if ($balance < $points) {
            throw new \InvalidArgumentException("Không đủ điểm. Hiện có: {$balance}, cần: {$points}");
        }

        return LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => -$points,
            'type' => LoyaltyPointType::Spend,
            'description' => $description,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);
    }

    public function getBalance(User $user): int
    {
        return (int) LoyaltyPoint::where('user_id', $user->id)->sum('points');
    }

    public function getHistory(User $user, int $limit = 20)
    {
        return LoyaltyPoint::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($limit);
    }
}
