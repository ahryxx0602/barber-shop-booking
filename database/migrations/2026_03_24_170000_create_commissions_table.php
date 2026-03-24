<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm cột commission_rate vào bảng barbers (% hoa hồng riêng cho từng thợ)
        Schema::table('barbers', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(0)->after('rating');
        });

        // Bảng commissions: lưu lịch sử hoa hồng mỗi booking completed
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->decimal('booking_amount', 12, 2);       // Tổng giá trị booking
            $table->decimal('commission_rate', 5, 2);        // Tỷ lệ % tại thời điểm tính
            $table->decimal('commission_amount', 12, 2);     // Số tiền hoa hồng = amount * rate / 100
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique('booking_id'); // Mỗi booking chỉ tính 1 lần
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');

        Schema::table('barbers', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};
