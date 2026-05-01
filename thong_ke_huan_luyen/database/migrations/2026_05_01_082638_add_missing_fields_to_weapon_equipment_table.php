<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weapon_equipment', function (Blueprint $table) {
            $table->string('cleaning_rod')->nullable()->after('m79'); // Thông nòng
            $table->string('spare_parts')->nullable()->after('cleaning_rod'); // Phụ tùng
            $table->string('gun_strap')->nullable()->after('spare_parts'); // Dây súng
        });
    }

    public function down(): void
    {
        Schema::table('weapon_equipment', function (Blueprint $table) {
            $table->dropColumn(['cleaning_rod', 'spare_parts', 'gun_strap']);
        });
    }
};
