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
        Schema::table('units', function (Blueprint $table) {
            $table->enum('level', ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->enum('level', ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'])->change();
        });
    }
};
