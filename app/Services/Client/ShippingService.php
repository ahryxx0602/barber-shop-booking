<?php

namespace App\Services\Client;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Tính phí vận chuyển từ tọa độ địa chỉ giao hàng.
     *
     * @return array{fee: float, distance_km: float, is_free: bool}
     */
    public function calculateFee(float $destLat, float $destLng, float $subtotal = 0): array
    {
        [$shopLat, $shopLng] = $this->getShopCoordinates();

        $distanceKm = $this->getDistance(
            "{$shopLat},{$shopLng}",
            "{$destLat},{$destLng}"
        );

        $feeResult = $this->feeFromDistance($distanceKm);

        // Miễn phí ship khi đơn hàng đủ lớn
        $freeAbove = (float) config('services.shipping.free_above', 500000);
        if ($freeAbove > 0 && $subtotal >= $freeAbove) {
            $feeResult['fee'] = 0;
            $feeResult['is_free'] = true;
        }

        $feeResult['distance_km'] = $distanceKm;

        return $feeResult;
    }

    /**
     * Lấy khoảng cách (km) giữa 2 tọa độ.
     * Ưu tiên Google Maps Distance Matrix API (đường đi thực tế).
     * Fallback: Haversine formula (đường chim bay, miễn phí).
     */
    public function getDistance(string $origin, string $destination): float
    {
        $apiKey = config('services.google_maps.api_key');

        // Nếu có Google Maps API key → dùng Distance Matrix (chính xác hơn)
        if (!empty($apiKey)) {
            try {
                $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                    'origins'      => $origin,
                    'destinations' => $destination,
                    'key'          => $apiKey,
                    'units'        => 'metric',
                ]);

                $data = $response->json();

                if (
                    ($data['status'] ?? '') === 'OK' &&
                    ($data['rows'][0]['elements'][0]['status'] ?? '') === 'OK'
                ) {
                    $meters = $data['rows'][0]['elements'][0]['distance']['value'];
                    return round($meters / 1000, 2);
                }

                Log::warning('Google Maps Distance Matrix API response không hợp lệ', [
                    'status' => $data['status'] ?? 'unknown',
                ]);
            } catch (\Exception $e) {
                Log::error('Google Maps Distance Matrix API error', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        // Fallback: Haversine formula (miễn phí, không cần API)
        [$lat1, $lng1] = array_map('floatval', explode(',', $origin));
        [$lat2, $lng2] = array_map('floatval', explode(',', $destination));

        return $this->haversineDistance($lat1, $lng1, $lat2, $lng2);
    }

    /**
     * Tính khoảng cách đường chim bay (km) bằng công thức Haversine.
     * Hoàn toàn miễn phí, không cần API.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Quy đổi khoảng cách → phí vận chuyển.
     * - Dưới free_within_km (mặc định 20km) → miễn phí
     * - Trên 20km → fee = base_fee + (km_vượt × per_km_fee), cap max_fee
     *
     * @return array{fee: float, is_free: bool}
     */
    public function feeFromDistance(float $distanceKm): array
    {
        $freeWithinKm = (float) config('services.shipping.free_within_km', 20);

        // Miễn phí nếu trong bán kính
        if ($distanceKm <= $freeWithinKm) {
            return [
                'fee'     => 0,
                'is_free' => true,
            ];
        }

        // Tính phí cho phần km vượt quá
        $baseFee   = (float) config('services.shipping.base_fee', 10000);
        $perKmFee  = (float) config('services.shipping.per_km_fee', 2000);
        $maxFee    = (float) config('services.shipping.max_fee', 50000);

        $excessKm = $distanceKm - $freeWithinKm;
        $fee = $baseFee + ($excessKm * $perKmFee);
        $fee = min($fee, $maxFee);
        $fee = round($fee, 0);

        return [
            'fee'     => $fee,
            'is_free' => false,
        ];
    }

    /**
     * Lấy tọa độ cửa hàng từ config.
     *
     * @return array{0: float, 1: float} [latitude, longitude]
     */
    public function getShopCoordinates(): array
    {
        return [
            (float) config('services.shipping.shop_latitude', 10.762622),
            (float) config('services.shipping.shop_longitude', 106.660172),
        ];
    }
}
