<?php

namespace App\Services;

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
     * Lấy khoảng cách (km) giữa 2 tọa độ qua Google Maps Distance Matrix API.
     * Nếu API fail → fallback 10km.
     */
    public function getDistance(string $origin, string $destination): float
    {
        $apiKey = config('services.google_maps.api_key');

        if (empty($apiKey)) {
            Log::warning('Google Maps API key chưa cấu hình, dùng fallback distance.');
            return 10.0; // fallback mặc định
        }

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
                // Distance trả về dạng meters → chuyển sang km
                $meters = $data['rows'][0]['elements'][0]['distance']['value'];
                return round($meters / 1000, 2);
            }

            Log::warning('Google Maps Distance Matrix API response không hợp lệ', [
                'status' => $data['status'] ?? 'unknown',
                'data'   => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Google Maps Distance Matrix API error', [
                'message' => $e->getMessage(),
            ]);
        }

        // Fallback khi API fail
        return 10.0;
    }

    /**
     * Quy đổi khoảng cách → phí vận chuyển.
     * Công thức: fee = base_fee + (distance_km × per_km_fee), cap max_fee.
     *
     * @return array{fee: float, is_free: bool}
     */
    public function feeFromDistance(float $distanceKm): array
    {
        $baseFee   = (float) config('services.shipping.base_fee', 15000);
        $perKmFee  = (float) config('services.shipping.per_km_fee', 5000);
        $maxFee    = (float) config('services.shipping.max_fee', 100000);

        $fee = $baseFee + ($distanceKm * $perKmFee);
        $fee = min($fee, $maxFee);
        $fee = round($fee, 0); // Làm tròn số nguyên

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
