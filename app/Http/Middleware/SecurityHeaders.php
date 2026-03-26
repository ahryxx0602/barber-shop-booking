<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thêm HTTP security headers vào mọi response.
 * Bảo vệ chống XSS, clickjacking, MIME sniffing, v.v.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chống MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Chống clickjacking (không cho embed trong iframe)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Bật XSS filter trên trình duyệt cũ
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Kiểm soát Referrer gửi đi
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Tắt quyền truy cập camera, microphone, geolocation
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // L2: CSP — dynamic theo environment
        $isLocal = app()->environment('local', 'testing', 'development');

        // Whitelist CDNs và External Services
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com https://maps.googleapis.com";
        $styleSrc  = "'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net https://unpkg.com";
        $fontSrc   = "'self' data: https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com https://cdn.jsdelivr.net";
        $connectSrc = "'self' https://provinces.open-api.vn https://api.provinces.open-api.vn https://nominatim.openstreetmap.org";

        if ($isLocal) {
            // Cho phép Vite dev server HMR (hot module replacement)
            $scriptSrc  .= " http://localhost:* http://127.0.0.1:*";
            $styleSrc   .= " http://localhost:* http://127.0.0.1:*";
            $connectSrc .= " ws://localhost:* ws://127.0.0.1:* http://localhost:* http://127.0.0.1:*";
            $fontSrc    .= " http://localhost:* http://127.0.0.1:*";
        }

        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; "
            . "script-src {$scriptSrc}; "
            . "style-src {$styleSrc}; "
            . "font-src {$fontSrc}; "
            . "img-src 'self' data: blob: https: http://localhost:* http://127.0.0.1:*; "
            . "connect-src {$connectSrc}; "
            . "frame-ancestors 'none'"
        );

        return $response;
    }
}
