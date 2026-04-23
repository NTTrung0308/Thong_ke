<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weapon_equipment', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại liên kết với quân nhân
            $table->foreignId('soldier_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');

            // Vũ khí trang bị
            $table->string('ak')->nullable(); // AK
            $table->string('rpd')->nullable(); // RPD
            $table->string('b41')->nullable(); // B41
            $table->string('m79')->nullable(); // M79 thông nòng
            $table->string('gun_accessories')->nullable(); // Phụ tùng dây súng
            $table->string('magazine_box')->nullable(); // Hộp tiếp đạn
            $table->string('oil_can')->nullable(); // Vịt dầu
            $table->string('bag')->nullable(); // Bao đồ
            $table->string('gun_cover')->nullable(); // Áo súng
            $table->string('muzzle_cover')->nullable(); // Bịt nòng
            $table->string('sight')->nullable(); // Kính ngắm
            $table->string('grenade')->nullable(); // Lựu đạn
            $table->string('infantry_shovel')->nullable(); // Xẻng bộ binh
            $table->string('infantry_pickaxe')->nullable(); // Cuốc bộ binh

            // Ngày tháng nhận/trả
            $table->date('receive_date')->nullable(); // Ngày nhận
            $table->date('return_date')->nullable(); // Ngày trả
            $table->string('received_by')->nullable(); // Người ký nhận
            $table->string('returned_by')->nullable(); // Người ký trả

            // Trạng thái
            $table->enum('status', ['dang-su-dung', 'da-tra', 'dang-bao-duong', 'hong'])->default('dang-su-dung');

            // Ghi chú
            $table->text('notes')->nullable(); // Ghi chú thay đổi trang bị

            // Audit fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['soldier_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weapon_equipment');
    }
};
