<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soldiers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Số hiệu quân nhân
            $table->string('full_name'); // Họ và tên
            $table->string('rank'); // Cấp bậc
            $table->string('position'); // Chức vụ
            $table->date('birth_date'); // Ngày tháng năm sinh
            $table->date('enlistment_date'); // Tháng năm nhập ngũ
            $table->date('party_join_date')->nullable(); // Ngày vào đảng đoàn
            $table->string('education'); // Học vấn
            $table->string('foreign_language')->nullable(); // Ngoại ngữ
            $table->string('professional_level')->nullable(); // Nghề nghiệp bậc chuyên môn
            $table->text('permanent_residence'); // Hộ khẩu thường trú
            $table->string('emergency_contact_name'); // Khi cần báo tin cho ai?
            $table->string('emergency_contact_address'); // Ở đâu
            $table->text('notes')->nullable(); // Ghi chú (thay đổi)

            // Foreign keys để phân quyền theo đơn vị
            $table->foreignId('unit_id')->constrained()->onDelete('cascade'); // Đơn vị thuộc cấp nào
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes(); // Xóa mềm
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soldiers');
    }
};
