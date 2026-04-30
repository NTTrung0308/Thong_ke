<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplines', function (Blueprint $table) {
            $table->id();

            // Đơn vị và quân nhân vi phạm
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('unit_name_at_time'); // Tên đơn vị tại thời điểm vi phạm
            $table->foreignId('soldier_id')->constrained()->onDelete('cascade');
            $table->string('soldier_name_at_time'); // Tên quân nhân tại thời điểm
            $table->string('soldier_rank_at_time')->nullable(); // Cấp bậc tại thời điểm

            // Nội dung vi phạm
            $table->text('work_content'); // Nội dung công việc vi phạm
            $table->text('violation_details'); // Chi tiết vi phạm

            // Hình thức kỷ luật
            $table->string('discipline_form'); // Hình thức kỷ luật
            $table->date('decision_date'); // Ngày quyết định
            $table->string('decision_month'); // Tháng quyết định
            $table->string('decision_level'); // Cấp quyết định (Đại đội, Tiểu đoàn, Trung đoàn...)
            $table->string('decision_number')->nullable(); // Số quyết định

            // Người ký quyết định
            $table->string('signer_name')->nullable();
            $table->string('signer_position')->nullable();

            // Thời gian thi hành
            $table->date('execution_date')->nullable(); // Ngày thi hành
            $table->date('expiry_date')->nullable(); // Ngày hết hạn kỷ luật (nếu có)

            // Kết quả sau kỷ luật
            $table->text('result')->nullable(); // Kết quả sau khi thi hành
            $table->text('improvement_measures')->nullable(); // Biện pháp khắc phục

            // File đính kèm
            $table->string('attachment')->nullable(); // File quyết định kỷ luật

            // Trạng thái
            $table->enum('status', ['dang-thi-hanh', 'da-thi-hanh-xong', 'duoc-xoa-bo'])->default('dang-thi-hanh');

            // Audit fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['unit_id', 'soldier_id']);
            $table->index('decision_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplines');
    }
};
