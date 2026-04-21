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

        // Whitelist CDNs và External Services (Chấp nhận tất cả HTTPS để Frontend không bị nghẽn)
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' https:";
        $styleSrc  = "'self' 'unsafe-inline' https:";
        $fontSrc   = "'self' data: https:";
        $connectSrc = "'self' https:";

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
