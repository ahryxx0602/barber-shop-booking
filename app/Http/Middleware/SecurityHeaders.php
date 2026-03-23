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

        return $response;
    }
}
