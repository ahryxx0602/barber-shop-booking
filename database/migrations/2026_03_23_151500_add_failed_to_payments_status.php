<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Thêm 'failed' vào enum status của bảng payments.
     * Cần thiết cho idempotency: khi payment gateway trả lỗi, ghi nhận 'failed' để không xử lý lại.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','paid','failed','refunded') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','paid','refunded') DEFAULT 'pending'");
    }
};
