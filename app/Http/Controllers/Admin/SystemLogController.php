<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Hiển thị nhật ký hệ thống cho admin.
 * Đọc trực tiếp file log trong storage/logs/.
 */
class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $channel = $request->get('channel', 'laravel');
        $level = $request->get('level', 'all');

        // Lấy file log mới nhất của channel
        $logFile = $this->getLatestLogFile($channel);
        $entries = [];

        if ($logFile && file_exists($logFile)) {
            $entries = $this->parseLogFile($logFile, $level);
        }

        // Danh sách channels có sẵn
        $channels = $this->getAvailableChannels();

        return view('admin.system.logs', compact('entries', 'channel', 'level', 'channels'));
    }

    /**
     * Tìm file log mới nhất theo channel name.
     */
    protected function getLatestLogFile(string $channel): ?string
    {
        $logPath = storage_path('logs');
        $pattern = "{$logPath}/{$channel}-*.log";

        $files = glob($pattern);

        if (empty($files)) {
            // Thử file không có ngày (vd: laravel.log)
            $singleFile = "{$logPath}/{$channel}.log";
            return file_exists($singleFile) ? $singleFile : null;
        }

        // Sắp xếp theo tên giảm dần (ngày mới nhất trước)
        rsort($files);

        return $files[0];
    }

    /**
     * Parse file log thành mảng entries.
     * Mỗi entry: timestamp, level, message.
     */
    protected function parseLogFile(string $filePath, string $levelFilter = 'all'): array
    {
        $content = file_get_contents($filePath);

        // Giới hạn hiển thị 200 entries cuối
        $maxEntries = 200;

        // Pattern: [2026-03-23 12:00:00] local.INFO: message
        $pattern = '/\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\]\s\S+\.(\w+):\s(.+?)(?=\[\d{4}-|\z)/s';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $entries = [];

        foreach ($matches as $match) {
            $entryLevel = strtolower($match[2]);

            if ($levelFilter !== 'all' && $entryLevel !== strtolower($levelFilter)) {
                continue;
            }

            $entries[] = [
                'timestamp' => $match[1],
                'level'     => strtoupper($match[2]),
                'message'   => trim($match[3]),
            ];
        }

        // Lấy entries cuối cùng (mới nhất)
        $entries = array_slice($entries, -$maxEntries);

        // Đảo ngược để mới nhất lên đầu
        return array_reverse($entries);
    }

    /**
     * Lấy danh sách channels có file log.
     */
    protected function getAvailableChannels(): array
    {
        $logPath = storage_path('logs');
        $files = glob("{$logPath}/*.log");

        $channels = [];
        foreach ($files as $file) {
            $filename = basename($file, '.log');
            // Bỏ phần ngày (vd: laravel-2026-03-23 → laravel)
            $channel = preg_replace('/-\d{4}-\d{2}-\d{2}$/', '', $filename);
            $channels[$channel] = $channel;
        }

        return array_values($channels);
    }
}
