<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sử dụng Raw SQL vì Doctrine DBAL gặp lỗi với ENUM
        DB::statement("ALTER TABLE training_logs MODIFY COLUMN training_content TEXT NULL");
        DB::statement("ALTER TABLE training_logs MODIFY COLUMN rating ENUM('xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE training_logs MODIFY COLUMN training_content TEXT NOT NULL");
        DB::statement("ALTER TABLE training_logs MODIFY COLUMN rating ENUM('xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu') NOT NULL DEFAULT 'trung_bình'");
    }
};
