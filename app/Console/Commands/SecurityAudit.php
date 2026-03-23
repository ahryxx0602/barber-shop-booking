<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Kiểm tra các cấu hình bảo mật quan trọng của ứng dụng.
 * Chạy lệnh: php artisan security:audit
 */
class SecurityAudit extends Command
{
    protected $signature = 'security:audit';

    protected $description = 'Kiểm tra cấu hình bảo mật ứng dụng';

    public function handle(): int
    {
        $this->info('🔒 Kiểm tra bảo mật BarberBook');
        $this->newLine();

        $passed = 0;
        $failed = 0;

        // 1. APP_DEBUG
        if (config('app.debug') === false) {
            $this->checkPass('APP_DEBUG đã tắt');
            $passed++;
        } else {
            $this->checkFail('APP_DEBUG đang BẬT — phải tắt trên production');
            $failed++;
        }

        // 2. APP_ENV
        if (config('app.env') === 'production') {
            $this->checkPass('APP_ENV = production');
            $passed++;
        } else {
            $this->checkWarn('APP_ENV = ' . config('app.env') . ' — nên là "production" trên server');
            $failed++;
        }

        // 3. APP_KEY
        if (!empty(config('app.key'))) {
            $this->checkPass('APP_KEY đã được thiết lập');
            $passed++;
        } else {
            $this->checkFail('APP_KEY chưa thiết lập — chạy: php artisan key:generate');
            $failed++;
        }

        // 4. HTTPS
        if (config('app.url') && str_starts_with(config('app.url'), 'https://')) {
            $this->checkPass('APP_URL sử dụng HTTPS');
            $passed++;
        } else {
            $this->checkWarn('APP_URL không dùng HTTPS — nên dùng HTTPS trên production');
            $failed++;
        }

        // 5. Session secure cookie
        if (config('session.secure')) {
            $this->checkPass('Session cookie đã bật Secure flag');
            $passed++;
        } else {
            $this->checkWarn('Session cookie chưa bật Secure — đặt SESSION_SECURE_COOKIE=true');
            $failed++;
        }

        // 6. Kiểm tra .env không public
        $envPublic = public_path('.env');
        if (!file_exists($envPublic)) {
            $this->checkPass('.env không nằm trong thư mục public');
            $passed++;
        } else {
            $this->checkFail('.env nằm trong thư mục public — LỖI NGHIÊM TRỌNG!');
            $failed++;
        }

        // 7. Database file permissions (SQLite)
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $perms = substr(sprintf('%o', fileperms($dbPath)), -4);
            if ($perms === '0644' || $perms === '0600') {
                $this->checkPass("Database SQLite permissions: {$perms}");
                $passed++;
            } else {
                $this->checkWarn("Database SQLite permissions: {$perms} — nên là 0644 hoặc 0600");
                $failed++;
            }
        }

        // Tổng kết
        $this->newLine();
        $this->info("Kết quả: ✅ {$passed} đạt | ❌ {$failed} cần xem lại");

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function checkPass(string $msg): void
    {
        $this->line("  ✅ {$msg}");
    }

    private function checkFail(string $msg): void
    {
        $this->error("  ❌ {$msg}");
    }

    private function checkWarn(string $msg): void
    {
        $this->warn("  ⚠️  {$msg}");
    }
}
