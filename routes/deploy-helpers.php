<?php

/**
 * ============================================================
 * Deploy Helpers — Chạy Artisan commands qua trình duyệt
 * ============================================================
 *
 * Dùng cho Shared Hosting không có SSH (VD: InfinityFree).
 * Mỗi endpoint được bảo vệ bằng DEPLOY_TOKEN trong .env.
 *
 * Cách dùng:
 *   https://your-domain.com/deploy/{command}?token=YOUR_SECRET_TOKEN
 *
 * ⚠️  SAU KHI DEPLOY XONG, hãy xóa file này hoặc comment
 *     dòng require trong bootstrap/app.php để tắt các route.
 * ============================================================
 */

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Middleware bảo vệ bằng token
|--------------------------------------------------------------------------
*/
Route::middleware('web')->prefix('deploy')->group(function () {

    // Kiểm tra token trước mỗi request
    Route::fallback(fn () => response('⛔ Not Found', 404));

    /**
     * Xác thực token đơn giản.
     * Token được lấy từ biến DEPLOY_TOKEN trong file .env
     */
    $verifyToken = function () {
        $token = request()->query('token');
        $validToken = config('app.deploy_token');

        if (empty($validToken) || $token !== $validToken) {
            abort(403, '⛔ Token không hợp lệ. Truy cập bị từ chối.');
        }
    };

    // ------------------------------------------------------------------
    // 1. Migrate database
    // ------------------------------------------------------------------
    Route::get('/migrate', function () use ($verifyToken) {
        $verifyToken();

        $exitCode = Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        return response("<h2>🗃️ Migrate</h2><pre>{$output}</pre><p>Exit code: {$exitCode}</p>", 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    });

    // ------------------------------------------------------------------
    // 2. Seed database (chạy lần đầu hoặc khi cần reset data)
    // ------------------------------------------------------------------
    Route::get('/seed', function () use ($verifyToken) {
        $verifyToken();

        $exitCode = Artisan::call('db:seed', ['--force' => true]);
        $output = Artisan::output();

        return response("<h2>🌱 Seed</h2><pre>{$output}</pre><p>Exit code: {$exitCode}</p>", 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    });

    // ------------------------------------------------------------------
    // 3. Tạo symbolic link cho storage
    // ------------------------------------------------------------------
    Route::get('/storage-link', function () use ($verifyToken) {
        $verifyToken();

        $exitCode = Artisan::call('storage:link');
        $output = Artisan::output();

        return response("<h2>🔗 Storage Link</h2><pre>{$output}</pre><p>Exit code: {$exitCode}</p>", 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    });

    // ------------------------------------------------------------------
    // 4. Cache config (tăng hiệu suất)
    // ------------------------------------------------------------------
    Route::get('/config-cache', function () use ($verifyToken) {
        $verifyToken();

        $exitCode = Artisan::call('config:cache');
        $output = Artisan::output();

        return response("<h2>⚙️ Config Cache</h2><pre>{$output}</pre><p>Exit code: {$exitCode}</p>", 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    });

    // ------------------------------------------------------------------
    // 5. Clear all cache
    // ------------------------------------------------------------------
    Route::get('/clear-cache', function () use ($verifyToken) {
        $verifyToken();

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return response("<h2>🧹 Clear Cache</h2><pre>✅ Config, Cache, Route, View cache đã được xóa.</pre>", 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    });

    // ------------------------------------------------------------------
    // 6. Cache routes + views (tăng hiệu suất)
    // ------------------------------------------------------------------
    Route::get('/optimize', function () use ($verifyToken) {
        $verifyToken();

        $exitCode = Artisan::call('optimize');
        $output = Artisan::output();

        return response("<h2>🚀 Optimize</h2><pre>{$output}</pre><p>Exit code: {$exitCode}</p>", 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    });

    // ------------------------------------------------------------------
    // 7. Chạy scheduled tasks (thay cho cron job)
    // ------------------------------------------------------------------
    Route::get('/schedule-run', function () use ($verifyToken) {
        $verifyToken();

        $exitCode = Artisan::call('schedule:run');
        $output = Artisan::output();

        return response("<h2>⏰ Schedule Run</h2><pre>{$output}</pre><p>Exit code: {$exitCode}</p>", 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    });

    // ------------------------------------------------------------------
    // 8. Dashboard — liệt kê tất cả lệnh có sẵn
    // ------------------------------------------------------------------
    Route::get('/', function () use ($verifyToken) {
        $verifyToken();

        $token = request()->query('token');
        $commands = [
            ['migrate', '🗃️ Migrate Database', 'Chạy migration tạo/cập nhật bảng'],
            ['seed', '🌱 Seed Database', 'Chạy seeder để thêm dữ liệu mẫu'],
            ['storage-link', '🔗 Storage Link', 'Tạo symbolic link storage → public'],
            ['config-cache', '⚙️ Config Cache', 'Cache cấu hình (tăng hiệu suất)'],
            ['clear-cache', '🧹 Clear Cache', 'Xóa toàn bộ cache'],
            ['optimize', '🚀 Optimize', 'Cache config + route + view'],
            ['schedule-run', '⏰ Schedule Run', 'Chạy scheduled tasks'],
        ];

        $html = '<html><head><meta charset="UTF-8"><title>Deploy Dashboard</title>';
        $html .= '<style>
            body { font-family: system-ui, -apple-system, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #0f172a; color: #e2e8f0; }
            h1 { color: #38bdf8; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; }
            .cmd { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 16px; margin: 12px 0; transition: border-color 0.2s; }
            .cmd:hover { border-color: #38bdf8; }
            .cmd a { color: #38bdf8; text-decoration: none; font-weight: 600; font-size: 1.1em; }
            .cmd p { color: #94a3b8; margin: 4px 0 0; font-size: 0.9em; }
            .warning { background: #422006; border: 1px solid #92400e; border-radius: 8px; padding: 12px 16px; margin: 20px 0; color: #fbbf24; }
        </style></head><body>';
        $html .= '<h1>🛠️ Classic Cut — Deploy Dashboard</h1>';
        $html .= '<div class="warning">⚠️ <strong>Lưu ý:</strong> Xóa file <code>routes/deploy-helpers.php</code> sau khi deploy xong!</div>';

        foreach ($commands as [$route, $label, $desc]) {
            $url = url("/deploy/{$route}?token={$token}");
            $html .= "<div class='cmd'><a href='{$url}'>{$label}</a><p>{$desc}</p></div>";
        }

        $html .= '</body></html>';

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    });
});
