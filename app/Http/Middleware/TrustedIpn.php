<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * M8: Middleware chặn IPN request từ IP không nằm trong whitelist.
 *
 * Dùng cho các route nhận callback server-to-server từ VNPay/MoMo.
 * Config whitelist tại config/services.php → 'trusted_ipn_ips'.
 *
 * Lưu ý: Trong môi trường local/sandbox, middleware tự động bypass
 * nếu IP là 127.0.0.1 hoặc chưa config whitelist.
 */
class TrustedIpn
{
    /**
     * Danh sách IP trusted mặc định (VNPay sandbox + MoMo sandbox).
     * Production nên cấu hình qua config/services.php.
     */
    private const DEFAULT_TRUSTED_IPS = [
        // VNPay sandbox
        '113.160.92.202',
        // MoMo sandbox (public IPs)
        '118.69.212.158',
        '113.160.92.0/24',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $trustedIps = config('services.trusted_ipn_ips', self::DEFAULT_TRUSTED_IPS);

        // Bypass nếu chưa config hoặc environment local
        if (empty($trustedIps) || app()->environment('local', 'testing')) {
            return $next($request);
        }

        $clientIp = $request->ip();

        foreach ($trustedIps as $trustedIp) {
            // Hỗ trợ exact match và CIDR notation
            if ($this->ipMatches($clientIp, $trustedIp)) {
                return $next($request);
            }
        }

        // IP không trong whitelist — log warning và reject
        Log::warning('IPN request from untrusted IP', [
            'ip'     => $clientIp,
            'url'    => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return response()->json([
            'RspCode' => '99',
            'Message' => 'Unauthorized IP',
        ], 403);
    }

    /**
     * Kiểm tra IP có match exact hoặc CIDR range không.
     */
    private function ipMatches(string $clientIp, string $trustedIp): bool
    {
        // Exact match
        if ($clientIp === $trustedIp) {
            return true;
        }

        // CIDR match (vd: 113.160.92.0/24)
        if (str_contains($trustedIp, '/')) {
            [$subnet, $mask] = explode('/', $trustedIp, 2);
            $mask = (int) $mask;

            if ($mask < 0 || $mask > 32) {
                return false;
            }

            $clientLong  = ip2long($clientIp);
            $subnetLong  = ip2long($subnet);

            if ($clientLong === false || $subnetLong === false) {
                return false;
            }

            $maskLong = -1 << (32 - $mask);

            return ($clientLong & $maskLong) === ($subnetLong & $maskLong);
        }

        return false;
    }
}
