<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
            $table->date('desired_date');
            $table->time('desired_time')->nullable(); // Giờ mong muốn (có thể null = bất kỳ giờ nào)
            $table->string('status')->default('waiting'); // waiting, notified, expired
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index(['barber_id', 'desired_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
