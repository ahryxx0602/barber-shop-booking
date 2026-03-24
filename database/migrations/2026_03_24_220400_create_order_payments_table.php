<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method'); // enum: cod, vnpay, momo
            $table->string('status')->default('pending'); // enum: pending, paid, failed
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique('order_id'); // Mỗi đơn chỉ có 1 payment record
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
