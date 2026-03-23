<?php

namespace App\Services;

use App\Models\Barber;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

/**
 * Service quản lý cache tập trung.
 * Tất cả cache keys và TTL được quản lý tại đây để dễ invalidate.
 */
class CacheService
{
    // Cache keys
    private const KEY_ACTIVE_SERVICES = 'active_services';
    private const KEY_ACTIVE_BARBERS = 'active_barbers';
    private const KEY_REPORT_PREFIX = 'report_';

    // TTL (seconds)
    private const TTL_SERVICES = 3600;     // 1 giờ
    private const TTL_BARBERS = 1800;      // 30 phút
    private const TTL_REPORT = 900;        // 15 phút

    /**
     * Lấy danh sách dịch vụ đang hoạt động (cached).
     */
    public function getActiveServices()
    {
        return Cache::remember(self::KEY_ACTIVE_SERVICES, self::TTL_SERVICES, function () {
            return Service::where('is_active', true)->orderBy('name')->get();
        });
    }

    /**
     * Lấy danh sách thợ đang hoạt động (cached).
     */
    public function getActiveBarbers()
    {
        return Cache::remember(self::KEY_ACTIVE_BARBERS, self::TTL_BARBERS, function () {
            return Barber::with('user')
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->get();
        });
    }

    /**
     * Cache kết quả báo cáo theo key tuỳ chỉnh.
     */
    public function rememberReport(string $key, callable $callback)
    {
        return Cache::remember(self::KEY_REPORT_PREFIX . $key, self::TTL_REPORT, $callback);
    }

    /**
     * Xoá cache dịch vụ (gọi khi tạo/sửa/xoá dịch vụ).
     */
    public function clearServiceCache(): void
    {
        Cache::forget(self::KEY_ACTIVE_SERVICES);
    }

    /**
     * Xoá cache thợ (gọi khi tạo/sửa/xoá thợ).
     */
    public function clearBarberCache(): void
    {
        Cache::forget(self::KEY_ACTIVE_BARBERS);
    }

    /**
     * Xoá cache báo cáo.
     */
    public function clearReportCache(): void
    {
        // Xoá các report cache phổ biến
        Cache::forget(self::KEY_REPORT_PREFIX . 'monthly_overview');
        Cache::forget(self::KEY_REPORT_PREFIX . 'top_barbers');
        Cache::forget(self::KEY_REPORT_PREFIX . 'top_services');
    }
}
