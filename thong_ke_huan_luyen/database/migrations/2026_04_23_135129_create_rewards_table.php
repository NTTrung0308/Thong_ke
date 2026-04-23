<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();

            // Loại khen thưởng
            $table->enum('type', ['unit', 'superior']); // unit: theo đơn vị, superior: cấp trên quyết định

            // Đơn vị được khen thưởng
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('unit_name_at_time'); // Tên đơn vị tại thời điểm khen thưởng

            // Quân nhân liên quan (nếu có)
            $table->foreignId('soldier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('soldier_name_at_time')->nullable(); // Tên quân nhân tại thời điểm

            // Thông tin khen thưởng
            $table->text('reason'); // Lý do khen thưởng
            $table->string('reward_form'); // Hình thức khen thưởng (VD: Giấy khen, Bằng khen, Thưởng tiền...)
            $table->date('decision_date'); // Ngày quyết định
            $table->string('decision_month'); // Tháng quyết định (lưu dạng YYYY-MM)
            $table->string('decision_level'); // Cấp quyết định (Đại đội, Tiểu đoàn, Trung đoàn, Sư đoàn...)
            $table->string('decision_number')->nullable(); // Số quyết định

            // Người ký quyết định
            $table->string('signer_name')->nullable(); // Người ký
            $table->string('signer_position')->nullable(); // Chức vụ người ký

            // Kết quả
            $table->text('result')->nullable(); // Kết quả khen thưởng

            // File đính kèm (nếu có)
            $table->string('attachment')->nullable(); // Đường dẫn file quyết định

            // Audit fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['unit_id', 'type']);
            $table->index('decision_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
