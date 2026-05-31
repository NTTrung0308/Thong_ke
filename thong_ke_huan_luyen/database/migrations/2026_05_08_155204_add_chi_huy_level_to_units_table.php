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
        // Sử dụng Raw SQL vì Doctrine DBAL không hỗ trợ ENUM change()
        DB::statement("ALTER TABLE units MODIFY COLUMN level ENUM('chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE units MODIFY COLUMN level ENUM('trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi')");
    }
};
