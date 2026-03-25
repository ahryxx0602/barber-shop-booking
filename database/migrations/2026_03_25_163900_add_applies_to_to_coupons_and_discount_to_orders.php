<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm cột applies_to vào bảng coupons
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('applies_to')->default('product')->after('type');
            // product = giảm giá sản phẩm, shipping = giảm phí ship, booking = giảm giá cắt tóc
        });

        // Thêm cột coupon vào bảng orders (e-commerce)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('product_coupon_code')->nullable()->after('note');
            $table->decimal('product_discount', 12, 2)->default(0)->after('product_coupon_code');
            $table->string('shipping_coupon_code')->nullable()->after('product_discount');
            $table->decimal('shipping_discount', 12, 2)->default(0)->after('shipping_coupon_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['product_coupon_code', 'product_discount', 'shipping_coupon_code', 'shipping_discount']);
        });
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('applies_to');
        });
    }
};
