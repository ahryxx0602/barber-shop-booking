<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Queue job ghi notification vào database.
 * Thay vì ghi notification đồng bộ trong Listener, ta dispatch job này
 * để không block request hiện tại.
 */
class SendBookingNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $userId,
        private string $message,
    ) {}

    public function handle(): void
    {
        Notification::create([
            'user_id' => $this->userId,
            'message' => $this->message,
        ]);
    }
}
