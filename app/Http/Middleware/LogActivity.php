<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ghi log các hành động thay đổi dữ liệu (POST/PUT/PATCH/DELETE).
 * Bỏ qua GET requests để giảm noise.
 */
class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        // Chỉ log các request thay đổi dữ liệu
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $duration = round((microtime(true) - $start) * 1000, 2);

            Log::info('Activity', [
                'user_id'  => $request->user()?->id,
                'role'     => $request->user()?->role?->value ?? 'guest',
                'method'   => $request->method(),
                'url'      => $request->fullUrl(),
                'ip'       => $request->ip(),
                'status'   => $response->getStatusCode(),
                'duration' => "{$duration}ms",
            ]);
        }

        return $response;
    }
}
