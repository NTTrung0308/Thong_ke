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
        Schema::table('training_logs', function (Blueprint $table) {
            $table->text('training_content')->nullable()->change();
            $table->enum('rating', ['xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu'])->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_logs', function (Blueprint $table) {
            $table->text('training_content')->nullable(false)->change();
            $table->enum('rating', ['xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu'])->nullable(false)->default('trung_bình')->change();
        });
    }
};
