<?php

use Illuminate\Database\Migrations\Migration;

// Migration này đã được gộp vào create_barber_leaves_table, giữ file rỗng để tránh lỗi
return new class extends Migration
{
    public function up(): void
    {
        // Các cột status, admin_note, reviewed_by, reviewed_at
        // đã có sẵn trong create_barber_leaves_table migration
    }

    public function down(): void
    {
        //
    }
};
