<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // format: ORD-YYYYMMDD-XXXX
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shipping_address_id')->constrained('shipping_addresses');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(10.00); // VAT 10%
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('shipping_fee', 10, 2);
            $table->decimal('shipping_distance_km', 8, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // enum: pending, confirmed, shipping, delivered, cancelled
            $table->text('note')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
