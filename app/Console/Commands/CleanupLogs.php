<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

/**
 * Dọn dẹp file log cũ hơn số ngày chỉ định.
 * Mặc định xoá log cũ hơn 30 ngày.
 */
class CleanupLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=30 : Số ngày giữ lại log}';

    protected $description = 'Xoá các file log cũ hơn số ngày chỉ định';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $logPath = storage_path('logs');
        $cutoff = Carbon::now()->subDays($days);

        $deleted = 0;

        $files = File::glob("{$logPath}/*.log");

        foreach ($files as $file) {
            // Bỏ qua file log chính (không có ngày trong tên)
            $filename = basename($file);

            // Chỉ xoá file có định dạng ngày (vd: laravel-2026-03-01.log)
            if (preg_match('/\d{4}-\d{2}-\d{2}/', $filename, $matches)) {
                $fileDate = Carbon::parse($matches[0]);

                if ($fileDate->lt($cutoff)) {
                    File::delete($file);
                    $deleted++;
                    $this->line("  Đã xoá: {$filename}");
                }
            }
        }

        if ($deleted === 0) {
            $this->info("✓ Không có file log nào cần dọn dẹp.");
        } else {
            $this->info("✓ Đã xoá {$deleted} file log cũ hơn {$days} ngày.");
        }

        return Command::SUCCESS;
    }
}
