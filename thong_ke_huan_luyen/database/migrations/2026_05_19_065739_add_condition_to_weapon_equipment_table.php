<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('weapon_equipment', function (Blueprint $table) {
            $table->string('condition')->default('tot')->after('status'); // tot, hỏng, cần_bảo_dưỡng
            $table->text('history')->nullable()->after('condition'); // Lưu lịch sử bàn giao/sửa chữa đơn giản
        });
    }

    public function down()
    {
        Schema::table('weapon_equipment', function (Blueprint $table) {
            $table->dropColumn(['condition', 'history']);
        });
    }
};
