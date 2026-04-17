<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên đơn vị: Trung đoàn 1, Tiểu đoàn 2...
            $table->enum('level', ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']); // Cấp đơn vị
            $table->foreignId('parent_id')->nullable()->constrained('units'); // Đơn vị cấp trên
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
