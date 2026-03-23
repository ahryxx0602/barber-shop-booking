<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // fixed, percent
            $table->decimal('value', 12, 2); // Giá trị giảm (VND hoặc %)
            $table->decimal('min_amount', 12, 2)->default(0); // Đơn tối thiểu
            $table->decimal('max_discount', 12, 2)->nullable(); // Giảm tối đa (cho %)
            $table->date('expiry_date')->nullable();
            $table->integer('usage_limit')->nullable(); // Giới hạn lượt dùng
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Thêm cột discount vào bookings để lưu giá trị giảm
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('discount_amount', 12, 2)->default(0)->after('total_price');
            $table->string('coupon_code')->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'coupon_code']);
        });
        Schema::dropIfExists('coupons');
    }
};
