<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Commission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Tự động tính hoa hồng khi booking hoàn thành.
     * Chỉ tạo commission nếu barber có commission_rate > 0 và booking chưa có commission.
     */
    public function calculateForBooking(Booking $booking): ?Commission
    {
        $barber = Barber::find($booking->barber_id);

        if (!$barber || $barber->commission_rate <= 0) {
            return null;
        }

        // Kiểm tra idempotency - booking đã có commission chưa
        $existing = Commission::where('booking_id', $booking->id)->first();
        if ($existing) {
            return $existing;
        }

        $commissionAmount = round($booking->total_price * $barber->commission_rate / 100, 2);

        $commission = Commission::create([
            'barber_id'         => $booking->barber_id,
            'booking_id'        => $booking->id,
            'booking_amount'    => $booking->total_price,
            'commission_rate'   => $barber->commission_rate,
            'commission_amount' => $commissionAmount,
            'note'              => "Hoa hồng booking #{$booking->booking_code}",
        ]);

        Log::channel('booking')->info('Commission calculated', [
            'booking_code'      => $booking->booking_code,
            'barber_id'         => $barber->id,
            'booking_amount'    => $booking->total_price,
            'commission_rate'   => $barber->commission_rate,
            'commission_amount' => $commissionAmount,
        ]);

        return $commission;
    }

    /**
     * Cập nhật tỷ lệ hoa hồng cho barber.
     */
    public function updateRate(Barber $barber, float $rate): Barber
    {
        $barber->update(['commission_rate' => $rate]);

        Log::info('Commission rate updated', [
            'barber_id' => $barber->id,
            'new_rate'  => $rate,
        ]);

        return $barber;
    }

    /**
     * Cập nhật tỷ lệ hoa hồng hàng loạt cho nhiều barber.
     */
    public function bulkUpdateRate(float $rate, ?array $barberIds = null): int
    {
        $query = Barber::query();

        if ($barberIds) {
            $query->whereIn('id', $barberIds);
        }

        $count = $query->update(['commission_rate' => $rate]);

        Log::info('Commission rate bulk updated', [
            'rate'    => $rate,
            'count'   => $count,
            'barbers' => $barberIds ?? 'all',
        ]);

        return $count;
    }

    /**
     * Lấy tổng hoa hồng theo barber trong khoảng thời gian.
     */
    public function getSummaryByBarber(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate   = $endDate ?? now()->endOfMonth();

        return Barber::select('barbers.*')
            ->selectRaw('COALESCE(SUM(commissions.commission_amount), 0) as total_commission')
            ->selectRaw('COUNT(commissions.id) as total_commission_bookings')
            ->selectRaw('COALESCE(SUM(commissions.booking_amount), 0) as total_booking_amount')
            ->leftJoin('commissions', function ($join) use ($startDate, $endDate) {
                $join->on('barbers.id', '=', 'commissions.barber_id')
                    ->whereBetween('commissions.created_at', [$startDate, $endDate]);
            })
            ->with('user:id,name,avatar')
            ->groupBy('barbers.id')
            ->orderByDesc('total_commission')
            ->get()
            ->map(fn ($barber) => [
                'id'                       => $barber->id,
                'name'                     => $barber->user->name,
                'avatar'                   => $barber->user->avatar,
                'commission_rate'          => (float) $barber->commission_rate,
                'total_commission'         => (float) $barber->total_commission,
                'total_commission_bookings'=> (int) $barber->total_commission_bookings,
                'total_booking_amount'     => (float) $barber->total_booking_amount,
                'rating'                   => (float) $barber->rating,
            ])
            ->toArray();
    }

    /**
     * Lấy lịch sử hoa hồng chi tiết (có phân trang).
     */
    public function getHistory(?int $barberId = null, ?Carbon $startDate = null, ?Carbon $endDate = null, int $perPage = 15)
    {
        $query = Commission::with(['barber.user', 'booking.customer', 'booking.services'])
            ->orderByDesc('created_at');

        if ($barberId) {
            $query->where('barber_id', $barberId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->paginate($perPage);
    }

    /**
     * Thống kê tổng quan hoa hồng tháng hiện tại.
     */
    public function getMonthlyOverview(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();

        $stats = Commission::selectRaw('
            COALESCE(SUM(commission_amount), 0) as total_commission,
            COALESCE(SUM(booking_amount), 0) as total_booking_amount,
            COUNT(*) as total_records
        ')
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->first();

        // Tháng trước
        $prevStart = now()->subMonth()->startOfMonth();
        $prevEnd   = now()->subMonth()->endOfMonth();

        $prevStats = Commission::selectRaw('
            COALESCE(SUM(commission_amount), 0) as total_commission
        ')
        ->whereBetween('created_at', [$prevStart, $prevEnd])
        ->first();

        $change = 0;
        if ($prevStats->total_commission > 0) {
            $change = round(($stats->total_commission - $prevStats->total_commission) / $prevStats->total_commission * 100, 1);
        } elseif ($stats->total_commission > 0) {
            $change = 100;
        }

        return [
            'total_commission'      => (float) $stats->total_commission,
            'total_booking_amount'  => (float) $stats->total_booking_amount,
            'total_records'         => (int) $stats->total_records,
            'avg_rate'              => $stats->total_records > 0
                ? round($stats->total_commission / $stats->total_booking_amount * 100, 1)
                : 0,
            'change'                => $change,
        ];
    }
}
